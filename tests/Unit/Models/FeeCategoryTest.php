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
            'code' => 'ACAD',
            'description' => 'Biaya untuk kegiatan akademik',
        ]);

        $this->assertDatabaseHas('fee_categories', [
            'name' => 'Biaya Akademik',
            'code' => 'ACAD',
        ]);
    }

    /**
     * Test fee category has many fee masters
     */
    public function test_fee_category_has_many_fee_masters()
    {
        $category = FeeCategory::factory()->create();
        
        FeeMaster::factory(3)->create(['fee_category_id' => $category->id]);

        $this->assertCount(3, $category->fees);
    }

    /**
     * Test fee category delete
     */
    public function test_fee_category_can_be_deleted()
    {
        $category = FeeCategory::factory()->create();

        $category->delete();

        $this->assertDatabaseMissing('fee_categories', ['id' => $category->id]);
    }

    /**
     * Test fee category default is_active status
     */
    public function test_fee_category_default_is_active()
    {
        $category = FeeCategory::create([
            'name' => 'Biaya Makan',
            'code' => 'MAKAN',
            'description' => 'Biaya makan santri',
        ]);

        $this->assertTrue($category->is_active);
    }
}
