<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Student;
use App\Models\Guardian;
use App\Models\Billing;
use App\Models\FeeCategory;
use App\Models\FeeMaster;
use App\Models\Discount;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ComprehensiveFeatureTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    /**
     * SUPER_ADMIN Tests
     */
    public function test_super_admin_master_data_pages_load()
    {
        $superAdmin = User::where('role', 'SUPER_ADMIN')->first();
        
        // All master data should be accessible
        $this->actingAs($superAdmin)->get('/admin/fee-categories')->assertStatus(200);
        $this->actingAs($superAdmin)->get('/admin/fee-masters')->assertStatus(200);
        $this->actingAs($superAdmin)->get('/admin/discounts')->assertStatus(200);
        $this->actingAs($superAdmin)->get('/admin/users')->assertStatus(200);
    }

    public function test_super_admin_operational_features_load()
    {
        $superAdmin = User::where('role', 'SUPER_ADMIN')->first();
        
        // All operational features should be accessible
        $this->actingAs($superAdmin)->get('/admin/students')->assertStatus(200);
        $this->actingAs($superAdmin)->get('/admin/guardians')->assertStatus(200);
        $this->actingAs($superAdmin)->get('/admin/billings')->assertStatus(200);
        $this->actingAs($superAdmin)->get('/admin/spmb-schedules')->assertStatus(200);
        $this->actingAs($superAdmin)->get('/admin/student-acceptance')->assertStatus(200);
    }

    public function test_super_admin_reports_load()
    {
        $superAdmin = User::where('role', 'SUPER_ADMIN')->first();
        
        $this->actingAs($superAdmin)->get('/admin/reports/financial')->assertStatus(200);
    }

    /**
     * ADMIN_TU Tests
     */
    public function test_admin_tu_cannot_access_super_admin_only_features()
    {
        $adminTU = User::where('role', 'ADMIN_TU')->first();
        
        // These should be forbidden or redirect
        $response = $this->actingAs($adminTU)->get('/admin/users');
        $this->assertTrue($response->status() === 403 || $response->status() === 302);
        
        $response = $this->actingAs($adminTU)->get('/admin/fee-masters');
        $this->assertTrue($response->status() === 403 || $response->status() === 302);
        
        $response = $this->actingAs($adminTU)->get('/admin/fee-categories');
        $this->assertTrue($response->status() === 403 || $response->status() === 302);
        
        $response = $this->actingAs($adminTU)->get('/admin/discounts');
        $this->assertTrue($response->status() === 403 || $response->status() === 302);
    }

    public function test_admin_tu_can_access_operational_features()
    {
        $adminTU = User::where('role', 'ADMIN_TU')->first();
        
        $this->actingAs($adminTU)->get('/admin/students')->assertStatus(200);
        $this->actingAs($adminTU)->get('/admin/guardians')->assertStatus(200);
        $this->actingAs($adminTU)->get('/admin/billings')->assertStatus(200);
    }

    /**
     * WALI_SANTRI Tests
     */
    public function test_wali_santri_dashboard_restricted()
    {
        $guardian = Guardian::first();
        $waliUser = $guardian->user;
        
        // Wali santri has limited interface
        $this->actingAs($waliUser)->get('/my-dashboard')->assertStatus(200);
        $this->actingAs($waliUser)->get('/spmb-schedules')->assertStatus(200);
    }

    public function test_wali_santri_cannot_access_admin_pages()
    {
        $guardian = Guardian::first();
        $waliUser = $guardian->user;
        
        // Should be redirected or forbidden
        $response = $this->actingAs($waliUser)->get('/admin/billings');
        $this->assertTrue($response->status() === 403 || $response->status() === 302);
        
        $response = $this->actingAs($waliUser)->get('/admin/students');
        $this->assertTrue($response->status() === 403 || $response->status() === 302);
    }

    public function test_wali_santri_student_detail_idor_protection()
    {
        $guardian1 = Guardian::first();
        $waliUser1 = $guardian1->user;
        $student1 = $guardian1->students()->first();
        
        // Get another guardian's student
        $guardian2 = Guardian::whereNot('id', $guardian1->id)->first();
        $student2 = $guardian2->students()->first();
        
        if ($student2) {
            // Should not be able to see other student
            $response = $this->actingAs($waliUser1)->get("/students/{$student2->id}");
            $this->assertTrue($response->status() === 403 || $response->status() === 302);
        }
    }

    /**
     * Business Logic Tests
     */
    public function test_student_has_billings()
    {
        $student = Student::first();
        
        $this->assertGreaterThan(0, $student->billings()->count());
    }

    public function test_billing_payment_creates_payment_record()
    {
        $billing = Billing::where('status', 'PAID')->first();
        
        $this->assertNotNull($billing, 'Paid billing should exist');
        $this->assertDatabaseHas('payments', [
            'billing_id' => $billing->id,
        ]);
    }

    public function test_paid_billing_has_receipt()
    {
        $paidBilling = Billing::where('status', 'PAID')->first();
        
        if ($paidBilling) {
            $this->assertTrue($paidBilling->payments()->count() > 0);
        }
    }

    public function test_student_status_lifecycle()
    {
        $student = Student::first();
        
        // Should have a status
        $this->assertNotNull($student->status);
        $this->assertContains($student->status, ['menunggu', 'diterima', 'ditolak']);
    }

    public function test_guardian_has_students()
    {
        $guardian = Guardian::first();
        
        $this->assertGreaterThan(0, $guardian->students()->count());
    }

    public function test_user_has_guardian_relationship()
    {
        $waliUser = User::where('role', 'WALI_SANTRI')->first();
        
        $this->assertNotNull($waliUser->guardian);
        $this->assertEquals($waliUser->id, $waliUser->guardian->user_id);
    }

    /**
     * Receipt & Authorization Tests
     */
    public function test_receipt_accessible_to_authorized_user()
    {
        $billing = Billing::first();
        $guardian = $billing->student->guardian;
        $waliUser = $guardian->user;
        
        $this->actingAs($waliUser)->get("/receipts/{$billing->id}")->assertStatus(200);
    }

    public function test_receipt_inaccessible_to_unauthorized_user()
    {
        $billing = Billing::first();
        $guardian1 = $billing->student->guardian;
        
        $guardian2 = Guardian::whereNot('id', $guardian1->id)->first();
        if ($guardian2) {
            $waliUser2 = $guardian2->user;
            $response = $this->actingAs($waliUser2)->get("/receipts/{$billing->id}");
            $this->assertEquals(403, $response->status());
        }
    }

    /**
     * Database Integrity Tests
     */
    public function test_billing_has_required_fields()
    {
        $billing = Billing::first();
        
        $this->assertNotNull($billing->student_id);
        $this->assertNotNull($billing->amount);
        $this->assertNotNull($billing->status);
    }

    public function test_payment_has_required_fields()
    {
        $payment = \App\Models\Payment::first();
        
        if ($payment) {
            $this->assertNotNull($payment->billing_id);
            $this->assertNotNull($payment->amount);
        }
    }

    public function test_student_has_required_fields()
    {
        $student = Student::first();
        
        $this->assertNotNull($student->nis);
        $this->assertNotNull($student->full_name);
        $this->assertNotNull($student->guardian_id);
    }

    /**
     * Fee Master & Category Tests
     */
    public function test_fee_categories_exist()
    {
        $this->assertGreaterThan(0, FeeCategory::count());
    }

    public function test_fee_masters_exist()
    {
        $this->assertGreaterThan(0, FeeMaster::count());
    }

    public function test_fee_master_has_category()
    {
        $feeMaster = FeeMaster::first();
        
        if ($feeMaster) {
            $this->assertNotNull($feeMaster->fee_category_id);
        }
    }
}
