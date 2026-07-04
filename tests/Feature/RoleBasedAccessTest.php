<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Student;
use App\Models\Guardian;
use App\Models\Billing;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Livewire\Livewire;
use App\Livewire\BillingIndex;

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
        $student->update(['nis' => null]);

        $this->actingAs($superAdmin);

        Livewire::test(\App\Livewire\StudentAcceptanceConfirm::class, ['student' => $student])
            ->call('confirmAcceptance');

        $student->refresh();
        $this->assertEquals('diterima', $student->status);
        $this->assertNotNull($student->nis);
        $this->assertMatchesRegularExpression('/^\d{4}\.\d{2}\.\d{4}$/', $student->nis);
    }

    // ADMINISTRASI Tests
    public function test_ADMINISTRASI_cannot_access_master_data()
    {
        $adminTU = User::where('role', 'ADMINISTRASI')->first();
        
        $this->actingAs($adminTU)
            ->get('/admin/fee-categories')
            ->assertRedirect(route('admin.dashboard'));

        $this->actingAs($adminTU)
            ->get('/admin/fee-masters')
            ->assertRedirect(route('admin.dashboard'));
    }

    public function test_ADMINISTRASI_can_process_payment()
    {
        $adminTU = User::where('role', 'ADMINISTRASI')->first();
        $billing = Billing::where('status', 'UNPAID')->first();

        $this->actingAs($adminTU);

        Livewire::test(BillingIndex::class)
            ->call('processCashPayment', $billing->id);

        $billing->refresh();
        $this->assertEquals('PAID', $billing->status);
    }

    public function test_ADMINISTRASI_can_create_student()
    {
        $adminTU = User::where('role', 'ADMINISTRASI')->first();
        $guardian = Guardian::first();

        $this->actingAs($adminTU);

        $kk = \Illuminate\Http\UploadedFile::fake()->image('kk.jpg');
        $foto = \Illuminate\Http\UploadedFile::fake()->image('foto.jpg');
        $akta = \Illuminate\Http\UploadedFile::fake()->image('akta.jpg');

        Livewire::test(\App\Livewire\StudentForm::class)
            ->set('guardian_id', $guardian->id)
            ->set('full_name', 'Test Student')
            ->set('unit_code', '02')
            ->set('residence_status', 'NON_MONDOK')
            ->set('special_status', 'UMUM')
            ->set('class_name', '8A')
            ->set('address', 'Jl. Test')
            ->set('kk_file', $kk)
            ->set('foto_file', $foto)
            ->set('akta_file', $akta)
            ->call('save')
            ->assertRedirect(route('admin.students'));

        $this->assertDatabaseHas('students', [
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
            ->assertRedirect('/my-dashboard');
    }

    public function test_wali_santri_cannot_access_fee_masters()
    {
        $guardian = Guardian::first();
        $waliUser = $guardian->user;

        $this->actingAs($waliUser)
            ->get('/admin/fee-masters')
            ->assertRedirect('/my-dashboard');
    }

    // Business Logic Tests
    public function test_billing_status_transitions_correctly()
    {
        $adminTU = User::where('role', 'ADMINISTRASI')->first();
        $billing = Billing::where('status', 'UNPAID')->first();
        $originalAmount = $billing->amount;

        $this->actingAs($adminTU);

        Livewire::test(BillingIndex::class)
            ->call('processCashPayment', $billing->id);

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

        // Create a billing for this inactive student to make sure they show in the list
        Billing::create([
            'student_id' => $student->id,
            'title' => 'Test Inactive Student Billing',
            'original_amount' => 100000,
            'discount_applied' => 0,
            'final_amount' => 100000,
            'status' => 'UNPAID',
        ]);

        // Inactive student should show in billing list
        $this->actingAs($superAdmin)
            ->get('/admin/billings')
            ->assertSee($student->full_name);
    }
}
