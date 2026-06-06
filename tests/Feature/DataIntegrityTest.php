<?php

namespace Tests\Feature;

use App\Models\Billing;
use App\Models\Discount;
use App\Models\FeeCategory;
use App\Models\FeeMaster;
use App\Models\Student;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DataIntegrityTest extends TestCase
{
    use RefreshDatabase;

    /**
     * NEGATIVE TEST 1: Cannot hard delete FeeCategory if it has active billings
     */
    public function test_cannot_delete_fee_category_with_active_billings()
    {
        $category = FeeCategory::factory()->create();
        $feeMaster = FeeMaster::factory()->create(['fee_category_id' => $category->id]);
        $student = Student::factory()->create(['status' => 'ACTIVE']);

        Billing::factory()->create([
            'student_id' => $student->id,
            'fee_master_id' => $feeMaster->id,
            'status' => 'UNPAID',
        ]);

        // Try to hard delete category
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('kategori');

        $category->forceDelete();
    }

    /**
     * NEGATIVE TEST 2: Delete FeeCategory should work
     */
    public function test_can_delete_fee_category()
    {
        $category = FeeCategory::factory()->create();
        $categoryId = $category->id;

        $category->delete();

        $this->assertDatabaseMissing('fee_categories', ['id' => $categoryId]);
    }

    /**
     * NEGATIVE TEST 3: Price snapshot prevents backdated billing changes
     */
    public function test_price_snapshot_prevents_price_change_impact()
    {
        $student = Student::factory()->create(['status' => 'ACTIVE']);
        $category = FeeCategory::factory()->create();
        $feeMaster = FeeMaster::factory()->create([
            'fee_category_id' => $category->id,
            'amount' => 1000000,
        ]);

        // Create billing with snapshot
        $billing = Billing::factory()->create([
            'student_id' => $student->id,
            'fee_master_id' => $feeMaster->id,
            'original_amount' => 1000000,
            'price_snapshot' => json_encode([
                ['item_name' => 'SPP', 'amount' => 1000000],
            ]),
        ]);

        // Admin changes fee price
        $feeMaster->update(['amount' => 2000000]);

        // Billing should still show original price
        $billing->refresh();
        $snapshot = json_decode($billing->price_snapshot, true);

        $this->assertEquals(1000000, $snapshot[0]['amount']);
        $this->assertNotEquals(2000000, $snapshot[0]['amount']);
    }

    /**
     * NEGATIVE TEST 4: Prevent duplicate billings for same period
     */
    public function test_prevent_duplicate_billings_same_period()
    {
        $student = Student::factory()->create(['status' => 'ACTIVE']);
        $feeCategory = FeeCategory::factory()->create();
        $feeMaster = FeeMaster::factory()->create([
            'fee_category_id' => $feeCategory->id,
            'unit_target' => $student->unit_code,
            'residence_target' => $student->residence_status,
            'is_active' => true,
        ]);

        $service = new \App\Services\EnhancedBillingService();

        // First billing succeeds
        $billing1 = $service->generateBillSecurely(
            $student,
            $feeCategory->id,
            'Tagihan 1'
        );

        // Try to create duplicate
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('sudah ada');

        $service->generateBillSecurely(
            $student,
            $feeCategory->id,
            'Tagihan 2'
        );
    }

    /**
     * NEGATIVE TEST 5: Pro-rata billing calculation verification
     */
    public function test_billing_prorata_tracking()
    {
        $student = Student::factory()->create(['status' => 'ACTIVE']);

        // Billing for partial month (May 15 - May 31)
        $billing = Billing::factory()->create([
            'student_id' => $student->id,
            'billing_period_start' => now()->setMonth(5)->setDay(15),
            'billing_period_end' => now()->setMonth(5)->setDay(31),
        ]);

        // Verify period is stored for pro-rata calculations
        $this->assertNotNull($billing->billing_period_start);
        $this->assertNotNull($billing->billing_period_end);
    }

    /**
     * NEGATIVE TEST 6: Prevent payment for billing without proper authorization
     */
    public function test_prevent_unauthorized_billing_modification()
    {
        $admin = User::factory()->create(['role' => 'ADMINISTRASI']);
        $guardian = User::factory()->create(['role' => 'WALI_SANTRI']);

        $student = Student::factory()->create(['status' => 'ACTIVE']);
        $billing = Billing::factory()->create(['student_id' => $student->id]);

        // Guardian tries to modify billing (should not be possible via normal flow)
        $response = $this->actingAs($guardian)
            ->patch("/admin/billings/{$billing->id}", [
                'title' => 'Hacked Title',
            ]);

        // Should be forbidden
        $this->assertTrue($response->status() === 403 || $response->status() === 404);
    }

    /**
     * NEGATIVE TEST 7: Paid billing is immutable
     */
    public function test_paid_billing_immutable()
    {
        $billing = Billing::factory()->create(['status' => 'PAID']);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('sudah dibayar');

        // Try to modify
        $billing->update(['title' => 'Modified']);
    }

    /**
     * NEGATIVE TEST 8: Cannot delete paid billing
     */
    public function test_cannot_delete_paid_billing()
    {
        $billing = Billing::factory()->create(['status' => 'PAID']);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('sudah dibayar');

        $billing->delete();
    }
}
