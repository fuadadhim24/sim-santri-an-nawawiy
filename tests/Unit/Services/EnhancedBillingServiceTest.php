<?php

namespace Tests\Unit\Services;

use Tests\TestCase;
use App\Models\Student;
use App\Models\FeeMaster;
use App\Models\FeeCategory;
use App\Models\Billing;
use App\Services\EnhancedBillingService;
use Illuminate\Foundation\Testing\RefreshDatabase;

class EnhancedBillingServiceTest extends TestCase
{
    use RefreshDatabase;

    protected $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new EnhancedBillingService();
    }

    public function test_billing_created_successfully()
    {
        $category = FeeCategory::factory()->create();
        $student = Student::factory()->create(['status' => 'ACTIVE']);
        $feeMaster = FeeMaster::factory()->create(['fee_category_id' => $category->id]);

        $billing = $this->service->generateBillSecurely(
            $student,
            $feeMaster->id,
            ['discount' => 0]
        );

        $this->assertNotNull($billing);
        $this->assertEquals($student->id, $billing->student_id);
        $this->assertEquals($feeMaster->id, $billing->fee_master_id);
        $this->assertIsArray($billing->price_snapshot);
    }

    public function test_duplicate_billing_prevented()
    {
        $category = FeeCategory::factory()->create();
        $student = Student::factory()->create(['status' => 'ACTIVE']);
        $feeMaster = FeeMaster::factory()->create(['fee_category_id' => $category->id]);

        $this->service->generateBillSecurely($student, $feeMaster->id);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Billing sudah ada');

        $this->service->generateBillSecurely($student, $feeMaster->id);
    }

    public function test_inactive_student_cannot_have_billing()
    {
        $category = FeeCategory::factory()->create();
        $student = Student::factory()->create(['status' => 'GRADUATED']);
        $feeMaster = FeeMaster::factory()->create(['fee_category_id' => $category->id]);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('tidak aktif');

        $this->service->generateBillSecurely($student, $feeMaster->id);
    }

    public function test_billing_price_snapshot_stored()
    {
        $category = FeeCategory::factory()->create();
        $student = Student::factory()->create(['status' => 'ACTIVE']);
        $feeMaster = FeeMaster::factory()->create([
            'fee_category_id' => $category->id,
            'amount' => 100000
        ]);

        $billing = $this->service->generateBillSecurely(
            $student,
            $feeMaster->id,
            ['discount' => 10000]
        );

        $this->assertIsArray($billing->price_snapshot);
        $this->assertArrayHasKey('original_amount', $billing->price_snapshot);
        $this->assertArrayHasKey('discount_applied', $billing->price_snapshot);
        $this->assertArrayHasKey('final_amount', $billing->price_snapshot);
    }

    public function test_discount_capped_at_100_percent()
    {
        $category = FeeCategory::factory()->create();
        $student = Student::factory()->create(['status' => 'ACTIVE']);
        $feeMaster = FeeMaster::factory()->create([
            'fee_category_id' => $category->id,
            'amount' => 100000
        ]);

        $billing = $this->service->generateBillSecurely(
            $student,
            $feeMaster->id,
            ['discount' => 150000]
        );

        $this->assertLessThanOrEqual($feeMaster->amount, $billing->discount_applied);
    }
}
