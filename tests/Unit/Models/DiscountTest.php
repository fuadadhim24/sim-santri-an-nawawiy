<?php

namespace Tests\Unit\Models;

use App\Models\Discount;
use App\Models\FeeMaster;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DiscountTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test can create discount
     */
    public function test_can_create_discount()
    {
        $feeMaster = FeeMaster::factory()->create();

        $discount = Discount::create([
            'fee_master_id' => $feeMaster->id,
            'target_status' => 'YATIM',
            'discount_amount' => 250000,
        ]);

        $this->assertDatabaseHas('discounts', [
            'fee_master_id' => $feeMaster->id,
            'target_status' => 'YATIM',
            'discount_amount' => 250000,
        ]);
    }

    /**
     * Test discount belongs to fee master
     */
    public function test_discount_belongs_to_fee_master()
    {
        $feeMaster = FeeMaster::factory()->create();
        $discount = Discount::factory()->create(['fee_master_id' => $feeMaster->id]);

        $this->assertTrue($discount->feeMaster()->exists());
        $this->assertEquals($feeMaster->id, $discount->feeMaster->id);
    }

    /**
     * Test discount for different target statuses
     */
    public function test_discount_for_different_target_statuses()
    {
        $feeMaster = FeeMaster::factory()->create();

        $statuses = ['ANAK_GURU', 'YATIM'];

        foreach ($statuses as $status) {
            $discount = Discount::create([
                'fee_master_id' => $feeMaster->id,
                'target_status' => $status,
                'discount_amount' => 100000,
            ]);

            $this->assertEquals($status, $discount->target_status);
        }
    }

    /**
     * Test multiple discounts for same fee master
     */
    public function test_multiple_discounts_for_same_fee_master()
    {
        $feeMaster = FeeMaster::factory()->create();

        Discount::factory(3)->create(['fee_master_id' => $feeMaster->id]);

        $this->assertCount(3, $feeMaster->discounts);
    }
}
