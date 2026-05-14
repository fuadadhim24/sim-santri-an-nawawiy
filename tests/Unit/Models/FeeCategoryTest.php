<?php

namespace Tests\Unit\Models;

use App\Models\FeeCategory;
use App\Models\FeeMaster;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FeeCategoryTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test can create fee category
     */
    public function test_can_create_fee_category()
    {
        $category = FeeCategory::create([
            'name' => 'Biaya Akademik',
            'description' => 'Biaya untuk kegiatan akademik',
        ]);

        $this->assertDatabaseHas('fee_categories', [
            'name' => 'Biaya Akademik',
        ]);
    }

    /**
     * Test fee category has many fee masters
     */
    public function test_fee_category_has_many_fee_masters()
    {
        $category = FeeCategory::factory()->create();
        
        FeeMaster::factory(3)->create(['fee_category_id' => $category->id]);

        $this->assertCount(3, $category->feeMasters ?? []);
    }

    /**
     * Test fee category soft delete
     */
    public function test_fee_category_can_be_soft_deleted()
    {
        $category = FeeCategory::factory()->create();

        $category->delete();

        $this->assertSoftDeleted('fee_categories', ['id' => $category->id]);
    }
}
