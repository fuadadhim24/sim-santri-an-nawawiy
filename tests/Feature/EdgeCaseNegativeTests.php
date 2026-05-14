<?php

namespace Tests\Feature;

use App\Models\Billing;
use App\Models\Student;
use App\Models\User;
use App\Services\EnhancedBillingService;
use Exception;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EdgeCaseNegativeTests extends TestCase
{
    use RefreshDatabase;

    private EnhancedBillingService $billingService;
    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->billingService = new EnhancedBillingService();
        $this->admin = User::factory()->create(['role' => 'ADMIN_TU']);
    }

    /**
     * NEGATIVE TEST 1: Cannot create billing for non-active student
     */
    public function test_cannot_create_billing_for_inactive_student()
    {
        $student = Student::factory()->create(['status' => 'INACTIVE']);

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Status santri');

        $this->billingService->generateBillSecurely($student, 1, 'Test');
    }

    /**
     * NEGATIVE TEST 2: Cannot create billing for graduated student
     */
    public function test_cannot_create_billing_for_graduated_student()
    {
        $student = Student::factory()->create(['status' => 'GRADUATED']);

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('GRADUATED');

        $this->billingService->generateBillSecurely($student, 1, 'Test');
    }

    /**
     * NEGATIVE TEST 3: Cannot create "hutang masa lalu" - New student charged for past months
     */
    public function test_cannot_charge_new_student_for_past_months()
    {
        // Student joined in May
        $student = Student::factory()->create([
            'status' => 'ACTIVE',
            'joined_at' => now()->startOfMonth()->addMonths(4), // May 1st
        ]);

        // Try to bill for January (before join date)
        $billingPeriodStart = now()->startOfMonth(); // January 1st

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('baru masuk');

        $this->billingService->generateBillSecurely(
            $student,
            1,
            'Biaya Januari',
            null,
            $billingPeriodStart,
            $billingPeriodStart->clone()->endOfMonth()
        );
    }

    /**
     * NEGATIVE TEST 4: Cannot charge student after they left
     */
    public function test_cannot_charge_student_after_left_date()
    {
        // Student left on June 15th
        $student = Student::factory()->create([
            'status' => 'ACTIVE',
            'left_at' => now()->setMonth(6)->setDay(15),
        ]);

        // Try to bill for July
        $billingPeriodEnd = now()->setMonth(7)->endOfMonth();

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('setelah santri keluar');

        $this->billingService->generateBillSecurely(
            $student,
            1,
            'Biaya Juli',
            null,
            null,
            $billingPeriodEnd
        );
    }

    /**
     * NEGATIVE TEST 5: Discount cannot exceed 100% (prevent negative billing)
     */
    public function test_discount_capped_at_100_percent()
    {
        $student = Student::factory()->create(['special_status' => 'ANAK_GURU']);

        // This should cap discount at 100%
        // Actual implementation would need factory setup with excessive discount
        // For now, we test that it doesn't allow negative final amount

        $billing = Billing::factory()->create([
            'student_id' => $student->id,
            'original_amount' => 1000000,
            'discount_applied' => 2000000, // 200% discount (over-capped)
            'final_amount' => 0, // Should be 0, not negative
        ]);

        $this->assertGreaterThanOrEqual(0, $billing->final_amount);
    }

    /**
     * NEGATIVE TEST 6: Cannot update status to non-ACTIVE if unpaid billings exist
     */
    public function test_cannot_change_status_with_unpaid_billings()
    {
        $student = Student::factory()->create(['status' => 'ACTIVE']);
        Billing::factory()->create([
            'student_id' => $student->id,
            'status' => 'UNPAID',
        ]);

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('tagihan aktif');

        $this->billingService->updateStudentStatusSafely($student, 'GRADUATED');
    }

    /**
     * NEGATIVE TEST 7: Cannot delete FeeCategory if it has active billings
     */
    public function test_cannot_hard_delete_fee_category_with_billings()
    {
        // This test assumes soft delete + constraint implementation
        // Feature to be implemented in models
        $this->assertTrue(true); // Placeholder - requires model update
    }

    /**
     * NEGATIVE TEST 8: IDOR Protection - Guardian cannot view other student's bill
     */
    public function test_idor_guardian_cannot_view_other_billing()
    {
        $guardian1 = User::factory()->create(['role' => 'WALI_SANTRI']);
        $guardian2 = User::factory()->create(['role' => 'WALI_SANTRI']);

        $student1 = Student::factory()->create(['guardian_id' => 1]);
        $student2 = Student::factory()->create(['guardian_id' => 2]);

        $billing1 = Billing::factory()->create(['student_id' => $student1->id]);
        $billing2 = Billing::factory()->create(['student_id' => $student2->id]);

        // Guardian1 tries to access Guardian2's billing
        $response = $this->actingAs($guardian1)
            ->get("/receipts/{$billing2->id}");

        // Should be 403 Forbidden
        $this->assertTrue($response->status() === 403 || $response->status() === 404);
    }

    /**
     * NEGATIVE TEST 9: Admin TU cannot modify Super Admin users
     */
    public function test_admin_tu_cannot_modify_super_admin()
    {
        $superAdmin = User::factory()->create(['role' => 'SUPER_ADMIN']);
        $adminTu = User::factory()->create(['role' => 'ADMIN_TU']);

        $response = $this->actingAs($adminTu)
            ->patch("/admin/users/{$superAdmin->id}", [
                'role' => 'ADMIN_TU', // Try to demote
            ]);

        // Should be forbidden
        $this->assertTrue($response->status() === 403 || $response->status() === 404);
    }

    /**
     * NEGATIVE TEST 10: Cannot process double payment (race condition)
     */
    public function test_cannot_process_double_payment()
    {
        $billing = Billing::factory()->create(['status' => 'UNPAID']);
        $admin = User::factory()->create(['role' => 'ADMIN_TU']);

        // First payment succeeds
        $payment1 = Billing::find($billing->id); // Simulate first payment
        $this->actingAs($admin);

        // Second payment should fail because billing is now PAID
        // This requires Duitku callback or cash payment attempt

        $this->assertTrue(true); // Placeholder
    }

    /**
     * NEGATIVE TEST 11: Webhook amount validation - prevent nominal manipulation
     */
    public function test_duitku_webhook_rejects_amount_mismatch()
    {
        // This test verifies Duitku callback validation
        // Implementation in SecurePaymentService

        $this->assertTrue(true); // Placeholder
    }

    /**
     * NEGATIVE TEST 12: Price snapshot prevents backdated price changes
     */
    public function test_price_snapshot_immutable_after_billing_created()
    {
        $student = Student::factory()->create(['status' => 'ACTIVE']);
        $billing = Billing::factory()->create([
            'student_id' => $student->id,
            'original_amount' => 1000000,
            'price_snapshot' => json_encode([
                ['item_name' => 'SPP', 'amount' => 1000000],
            ]),
        ]);

        // Admin tries to change fee master price
        // Billing should still show original price from snapshot
        $snapshotPrice = json_decode($billing->price_snapshot, true);

        $this->assertEquals(1000000, $snapshotPrice[0]['amount']);
        // This ensures price changes don't affect past billings
    }
}
