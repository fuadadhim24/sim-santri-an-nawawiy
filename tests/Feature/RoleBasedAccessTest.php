<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Student;
use App\Models\Guardian;
use App\Models\Billing;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RoleBasedAccessTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    // SUPER_ADMIN Tests
    public function test_super_admin_can_access_all_master_data()
    {
        $superAdmin = User::where('role', 'SUPER_ADMIN')->first();
        
        $this->actingAs($superAdmin)
            ->get('/admin/fee-categories')
            ->assertStatus(200);

        $this->actingAs($superAdmin)
            ->get('/admin/fee-masters')
            ->assertStatus(200);

        $this->actingAs($superAdmin)
            ->get('/admin/discounts')
            ->assertStatus(200);
    }

    public function test_super_admin_can_access_all_operational_features()
    {
        $superAdmin = User::where('role', 'SUPER_ADMIN')->first();
        
        $this->actingAs($superAdmin)
            ->get('/admin/students')
            ->assertStatus(200);

        $this->actingAs($superAdmin)
            ->get('/admin/guardians')
            ->assertStatus(200);

        $this->actingAs($superAdmin)
            ->get('/admin/billings')
            ->assertStatus(200);

        $this->actingAs($superAdmin)
            ->get('/admin/reports/financial')
            ->assertStatus(200);
    }

    public function test_super_admin_can_accept_student()
    {
        $superAdmin = User::where('role', 'SUPER_ADMIN')->first();
        $student = Student::where('status', 'menunggu')->first();

        $this->actingAs($superAdmin)
            ->post("/admin/students/{$student->id}/accept", [
                'decision' => 'diterima'
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('students', [
            'id' => $student->id,
            'status' => 'diterima',
        ]);
    }

    // ADMIN_TU Tests
    public function test_admin_tu_can_access_master_data()
    {
        $adminTU = User::where('role', 'ADMIN_TU')->first();
        
        $this->actingAs($adminTU)
            ->get('/admin/fee-categories')
            ->assertStatus(200);

        $this->actingAs($adminTU)
            ->get('/admin/fee-masters')
            ->assertStatus(200);
    }

    public function test_admin_tu_can_process_payment()
    {
        $adminTU = User::where('role', 'ADMIN_TU')->first();
        $billing = Billing::where('status', 'BELUM LUNAS')->first();

        // Test cash payment
        $this->actingAs($adminTU)
            ->post("/admin/billings/{$billing->id}/payment/cash", [
                'amount' => $billing->amount,
            ])
            ->assertRedirect();

        $billing->refresh();
        $this->assertEquals('PAID', $billing->status);
    }

    public function test_admin_tu_can_create_student()
    {
        $adminTU = User::where('role', 'ADMIN_TU')->first();
        $guardian = Guardian::first();

        $this->actingAs($adminTU)
            ->post('/admin/students', [
                'guardian_id' => $guardian->id,
                'nis' => '2026.99.0999',
                'full_name' => 'Test Student',
                'unit_code' => '02',
                'residence_status' => 'PULANG',
                'special_status' => 'UMUM',
                'class_name' => '8A',
                'address' => 'Jl. Test',
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('students', [
            'nis' => '2026.99.0999',
            'full_name' => 'Test Student',
        ]);
    }

    // WALI_SANTRI Tests
    public function test_wali_santri_can_view_own_student()
    {
        $guardian = Guardian::first();
        $waliUser = $guardian->user;
        $student = $guardian->students()->first();

        $this->actingAs($waliUser)
            ->get("/students/{$student->id}")
            ->assertStatus(200);
    }

    public function test_wali_santri_cannot_view_other_student()
    {
        $guardian = Guardian::first();
        $waliUser = $guardian->user;
        
        // Get a student from different guardian
        $otherStudent = Student::where('guardian_id', '!=', $guardian->id)->first();

        if ($otherStudent) {
            $this->actingAs($waliUser)
                ->get("/students/{$otherStudent->id}")
                ->assertStatus(403);
        }
    }

    public function test_wali_santri_can_view_own_receipt()
    {
        $guardian = Guardian::first();
        $waliUser = $guardian->user;
        $student = $guardian->students()->first();
        $billing = $student->billings()->first();

        $this->actingAs($waliUser)
            ->get("/receipts/{$billing->id}")
            ->assertStatus(200);
    }

    public function test_wali_santri_cannot_view_other_receipt()
    {
        $guardian = Guardian::first();
        $waliUser = $guardian->user;
        
        // Get billing from different student
        $otherBilling = Billing::whereHas('student', function ($q) use ($guardian) {
            $q->where('guardian_id', '!=', $guardian->id);
        })->first();

        if ($otherBilling) {
            $this->actingAs($waliUser)
                ->get("/receipts/{$otherBilling->id}")
                ->assertStatus(403);
        }
    }

    public function test_wali_santri_can_register_spmb()
    {
        $guardian = Guardian::first();
        $waliUser = $guardian->user;

        $this->actingAs($waliUser)
            ->get('/spmb-schedules')
            ->assertStatus(200);
    }

    public function test_wali_santri_cannot_access_admin_billing()
    {
        $guardian = Guardian::first();
        $waliUser = $guardian->user;

        $this->actingAs($waliUser)
            ->get('/admin/billings')
            ->assertStatus(403);
    }

    public function test_wali_santri_cannot_access_fee_masters()
    {
        $guardian = Guardian::first();
        $waliUser = $guardian->user;

        $this->actingAs($waliUser)
            ->get('/admin/fee-masters')
            ->assertStatus(403);
    }

    // Business Logic Tests
    public function test_billing_status_transitions_correctly()
    {
        $adminTU = User::where('role', 'ADMIN_TU')->first();
        $billing = Billing::where('status', 'BELUM LUNAS')->first();
        $originalAmount = $billing->amount;

        // Process payment
        $this->actingAs($adminTU)
            ->post("/admin/billings/{$billing->id}/payment/cash", [
                'amount' => $originalAmount,
            ]);

        $billing->refresh();
        
        // Verify status changed
        $this->assertEquals('PAID', $billing->status);
        
        // Verify payment record created
        $this->assertDatabaseHas('payments', [
            'billing_id' => $billing->id,
            'amount' => $originalAmount,
        ]);
    }

    public function test_student_active_status_controls_billing()
    {
        $superAdmin = User::where('role', 'SUPER_ADMIN')->first();
        $student = Student::where('is_active', false)->first();

        // Inactive student should show in billing list
        $this->actingAs($superAdmin)
            ->get('/admin/billings')
            ->assertSee($student->full_name);
    }
}
