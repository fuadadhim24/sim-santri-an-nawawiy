<?php

namespace Tests\Unit\Services;

use App\Models\Billing;
use App\Models\Discount;
use App\Models\FeeCategory;
use App\Models\FeeMaster;
use App\Models\Student;
use App\Services\BillingService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BillingServiceTest extends TestCase
{
    use RefreshDatabase;

    private BillingService $billingService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->billingService = new BillingService();
    }

    /**
     * Test generate bill for student
     */
    public function test_generate_bill_creates_billing()
    {
        $student = Student::factory()->create();
        $category = FeeCategory::factory()->create();
        FeeMaster::factory()->create([
            'fee_category_id' => $category->id,
            'amount' => 1000000,
            'unit_target' => null,
            'residence_target' => null,
        ]);

        $billing = $this->billingService->generateBill(
            $student,
            $category->id,
            'Test Billing'
        );

        $this->assertNotNull($billing);
        $this->assertEquals('Test Billing', $billing->title);
        $this->assertEquals(1000000, $billing->final_amount);
        $this->assertEquals('UNPAID', $billing->status);
    }

    /**
     * Test generate bill with discount for special student
     */
    public function test_generate_bill_applies_discount_for_special_student()
    {
        $student = Student::factory()->create(['special_status' => 'YATIM']);
        $category = FeeCategory::factory()->create();
        $feeMaster = FeeMaster::factory()->create([
            'fee_category_id' => $category->id,
            'amount' => 1000000,
            'unit_target' => null,
            'residence_target' => null,
        ]);

        Discount::factory()->create([
            'fee_master_id' => $feeMaster->id,
            'target_status' => 'YATIM',
            'discount_amount' => 250000,
        ]);

        $billing = $this->billingService->generateBill(
            $student,
            $category->id,
            'Billing with Discount'
        );

        $this->assertEquals(250000, $billing->discount_applied);
        $this->assertEquals(750000, $billing->final_amount);
    }

    /**
     * Test generate bill with multiple fee masters
     */
    public function test_generate_bill_with_multiple_fees()
    {
        $student = Student::factory()->create();
        $category = FeeCategory::factory()->create();

        FeeMaster::factory(3)->create([
            'fee_category_id' => $category->id,
            'amount' => 500000,
            'unit_target' => null,
            'residence_target' => null,
        ]);

        $billing = $this->billingService->generateBill(
            $student,
            $category->id,
            'Multiple Fees Billing'
        );

        $this->assertEquals(1500000, $billing->original_amount);
        $this->assertEquals(1500000, $billing->final_amount);
    }

    /**
     * Test generate bill returns null for invalid category
     */
    public function test_generate_bill_returns_null_for_invalid_category()
    {
        $student = Student::factory()->create();

        $billing = $this->billingService->generateBill(
            $student,
            999,
            'Invalid Billing'
        );

        $this->assertNull($billing);
    }

    /**
     * Test generate bill respects unit target
     */
    public function test_generate_bill_respects_unit_target()
    {
        $student = Student::factory()->create(['unit_code' => 'UNIT_A']);
        $category = FeeCategory::factory()->create();

        FeeMaster::factory()->create([
            'fee_category_id' => $category->id,
            'amount' => 1000000,
            'unit_target' => 'UNIT_A',
            'residence_target' => null,
        ]);

        FeeMaster::factory()->create([
            'fee_category_id' => $category->id,
            'amount' => 2000000,
            'unit_target' => 'UNIT_B',
            'residence_target' => null,
        ]);

        $billing = $this->billingService->generateBill(
            $student,
            $category->id,
            'Unit Specific Billing'
        );

        $this->assertEquals(1000000, $billing->original_amount);
    }

    /**
     * Test generate bill respects residence target
     */
    public function test_generate_bill_respects_residence_target()
    {
        $student = Student::factory()->create(['residence_status' => 'MUKIM']);
        $category = FeeCategory::factory()->create();

        FeeMaster::factory()->create([
            'fee_category_id' => $category->id,
            'amount' => 1000000,
            'unit_target' => null,
            'residence_target' => 'MUKIM',
        ]);

        FeeMaster::factory()->create([
            'fee_category_id' => $category->id,
            'amount' => 500000,
            'unit_target' => null,
            'residence_target' => 'NON_MUKIM',
        ]);

        $billing = $this->billingService->generateBill(
            $student,
            $category->id,
            'Residence Specific Billing'
        );

        $this->assertEquals(1000000, $billing->original_amount);
    }

    /**
     * Test discount cannot exceed original amount
     */
    public function test_discount_cannot_exceed_original_amount()
    {
        $student = Student::factory()->create(['special_status' => 'YATIM']);
        $category = FeeCategory::factory()->create();
        $feeMaster = FeeMaster::factory()->create([
            'fee_category_id' => $category->id,
            'amount' => 1000000,
            'unit_target' => null,
            'residence_target' => null,
        ]);

        Discount::factory()->create([
            'fee_master_id' => $feeMaster->id,
            'target_status' => 'YATIM',
            'discount_amount' => 2000000, // Discount lebih besar dari amount
        ]);

        $billing = $this->billingService->generateBill(
            $student,
            $category->id,
            'Test Discount Cap'
        );

        // Final amount should not be negative
        $this->assertEquals(0, $billing->final_amount);
        $this->assertEquals(1000000, $billing->discount_applied);
    }
}
