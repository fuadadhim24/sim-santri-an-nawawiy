<?php

namespace Tests\Unit\Models;

use App\Models\Discount;
use App\Models\FeeMaster;
use App\Models\FeeCategory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FeeMasterTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test can create fee master
     */
    public function test_can_create_fee_master()
    {
        $category = FeeCategory::factory()->create();

        $feeMaster = FeeMaster::create([
            'item_name' => 'Biaya Pendaftaran',
            'amount' => 500000,
            'fee_category_id' => $category->id,
            'is_active' => true,
        ]);

        $this->assertDatabaseHas('fee_masters', [
            'item_name' => 'Biaya Pendaftaran',
            'amount' => 500000,
        ]);
    }

    /**
     * Test fee master has many discounts
     */
    public function test_fee_master_has_many_discounts()
    {
        $feeMaster = FeeMaster::factory()->create();
        
        Discount::factory(3)->create(['fee_master_id' => $feeMaster->id]);

        $this->assertCount(3, $feeMaster->discounts);
    }

    /**
     * Test fee master belongs to category
     */
    public function test_fee_master_belongs_to_category()
    {
        $category = FeeCategory::factory()->create();
        $feeMaster = FeeMaster::factory()->create(['fee_category_id' => $category->id]);

        $this->assertTrue($feeMaster->category()->exists());
        $this->assertEquals($category->id, $feeMaster->category->id);
    }

    /**
     * Test fee master with unit target
     */
    public function test_fee_master_with_unit_target()
    {
        $feeMaster = FeeMaster::factory()->create([
            'unit_target' => 'MTK',
        ]);

        $this->assertEquals('MTK', $feeMaster->unit_target);
    }

    /**
     * Test fee master with residence target
     */
    public function test_fee_master_with_residence_target()
    {
        $feeMaster = FeeMaster::factory()->create([
            'residence_target' => 'MUKIM',
        ]);

        $this->assertEquals('MUKIM', $feeMaster->residence_target);
    }

    /**
     * Test fee master replacement logic
     */
    public function test_fee_master_replacement()
    {
        $original = FeeMaster::factory()->create();
        $replacement = FeeMaster::factory()->create([
            'replaced_by' => $original->id,
        ]);

        // Verify relationship
        $this->assertTrue($original->replaces()->exists());
    }

    /**
     * Test fee master soft delete
     */
    public function test_fee_master_can_be_soft_deleted()
    {
        $feeMaster = FeeMaster::factory()->create();

        $feeMaster->delete();

        $this->assertSoftDeleted('fee_masters', ['id' => $feeMaster->id]);
    }

    /**
     * Test fee master is_active boolean cast
     */
    public function test_fee_master_is_active_cast()
    {
        $feeMaster = FeeMaster::factory()->create(['is_active' => true]);
        $this->assertTrue($feeMaster->is_active);

        $feeMaster->update(['is_active' => false]);
        $this->assertFalse($feeMaster->fresh()->is_active);
    }

    /**
     * Test fee master amount integer cast
     */
    public function test_fee_master_amount_cast()
    {
        $feeMaster = FeeMaster::factory()->create(['amount' => 1500000]);
        $this->assertEquals(1500000, $feeMaster->amount);
    }
}
