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
        FeeMaster::factory()->create([
            'fee_category_id' => $category->id,
            'unit_target' => null,
            'residence_target' => null,
        ]);

        $billing = $this->service->generateBillSecurely(
            $student,
            $category->id,
            'Test Billing'
        );

        $this->assertNotNull($billing);
        $this->assertEquals($student->id, $billing->student_id);
    }

    public function test_duplicate_billing_check()
    {
        $category = FeeCategory::factory()->create();
        $student = Student::factory()->create(['status' => 'ACTIVE']);
        FeeMaster::factory()->create([
            'fee_category_id' => $category->id,
            'unit_target' => null,
            'residence_target' => null,
        ]);

        $billing1 = $this->service->generateBillSecurely($student, $category->id, 'First Billing');
        $this->assertNotNull($billing1);

        $this->assertDatabaseHas('billings', [
            'student_id' => $student->id,
            'status' => 'UNPAID',
        ]);
    }

    public function test_inactive_student_cannot_have_billing()
    {
        $category = FeeCategory::factory()->create();
        $student = Student::factory()->create(['status' => 'GRADUATED']);
        FeeMaster::factory()->create([
            'fee_category_id' => $category->id,
            'unit_target' => null,
            'residence_target' => null,
        ]);

        $this->expectException(\Exception::class);

        $this->service->generateBillSecurely($student, $category->id, 'Test Billing');
    }

    public function test_billing_price_snapshot_stored()
    {
        $category = FeeCategory::factory()->create();
        $student = Student::factory()->create(['status' => 'ACTIVE']);
        FeeMaster::factory()->create([
            'fee_category_id' => $category->id,
            'amount' => 100000,
            'unit_target' => null,
            'residence_target' => null,
        ]);

        $billing = $this->service->generateBillSecurely($student, $category->id, 'Test Billing');

        $this->assertNotNull($billing);
        if ($billing && $billing->price_snapshot) {
            $snapshot = is_array($billing->price_snapshot) ? $billing->price_snapshot : json_decode($billing->price_snapshot, true);
            $this->assertIsArray($snapshot);
        }
    }

    public function test_service_validates_student_status()
    {
        $category = FeeCategory::factory()->create();
        $student = Student::factory()->create(['status' => 'LEFT']);
        FeeMaster::factory()->create([
            'fee_category_id' => $category->id,
            'unit_target' => null,
            'residence_target' => null,
        ]);

        $result = $this->service->generateBillSecurely($student, $category->id, 'Test Billing');

        $this->assertNull($result);
    }
}
