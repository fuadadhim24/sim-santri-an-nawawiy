<?php

namespace Tests\Feature\Livewire;

use App\Livewire\DiscountIndex;
use App\Models\Discount;
use App\Models\FeeCategory;
use App\Models\FeeMaster;
use App\Models\Student;
use App\Models\Billing;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class DiscountIndexTest extends TestCase
{
    use RefreshDatabase;

    protected $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = User::factory()->create(['role' => 'SUPER_ADMIN']);
    }

    public function test_renders_successfully()
    {
        Livewire::actingAs($this->admin)
            ->test(DiscountIndex::class)
            ->assertStatus(200);
    }

    public function test_confirm_delete_dispatches_simple_confirmation_when_no_billings_affected()
    {
        $category = FeeCategory::create(['name' => 'SPP', 'code' => 'SPP']);
        $feeMaster = FeeMaster::create([
            'fee_category_id' => $category->id,
            'item_name' => 'SPP SMP',
            'amount' => 150000,
            'start_date' => '2026-01-01',
            'is_active' => true
        ]);
        $discount = Discount::create([
            'fee_master_id' => $feeMaster->id,
            'target_status' => 'YATIM',
            'discount_amount' => 50000
        ]);

        Livewire::actingAs($this->admin)
            ->test(DiscountIndex::class)
            ->call('confirmDelete', $discount->id)
            ->assertDispatched('show-simple-delete-discount-confirmation');
    }

    public function test_confirm_delete_dispatches_multi_option_confirmation_when_billings_affected()
    {
        $category = FeeCategory::create(['name' => 'SPP', 'code' => 'SPP']);
        $feeMaster = FeeMaster::create([
            'fee_category_id' => $category->id,
            'item_name' => 'SPP SMP',
            'amount' => 150000,
            'start_date' => '2026-01-01',
            'is_active' => true
        ]);
        $discount = Discount::create([
            'fee_master_id' => $feeMaster->id,
            'target_status' => 'YATIM',
            'discount_amount' => 50000
        ]);

        $student = Student::factory()->create([
            'special_status' => 'YATIM',
            'status' => 'ACTIVE'
        ]);

        $billing = Billing::create([
            'student_id' => $student->id,
            'fee_master_id' => $feeMaster->id,
            'title' => 'SPP SMP',
            'original_amount' => 150000,
            'discount_applied' => 50000,
            'final_amount' => 100000,
            'status' => 'UNPAID'
        ]);

        Livewire::actingAs($this->admin)
            ->test(DiscountIndex::class)
            ->call('confirmDelete', $discount->id)
            ->assertDispatched('show-delete-discount-confirmation');
    }

    public function test_execute_delete_does_not_recalculate_billings()
    {
        $category = FeeCategory::create(['name' => 'SPP', 'code' => 'SPP']);
        $feeMaster = FeeMaster::create([
            'fee_category_id' => $category->id,
            'item_name' => 'SPP SMP',
            'amount' => 150000,
            'start_date' => '2026-01-01',
            'is_active' => true
        ]);
        $discount = Discount::create([
            'fee_master_id' => $feeMaster->id,
            'target_status' => 'YATIM',
            'discount_amount' => 50000
        ]);

        $student = Student::factory()->create([
            'special_status' => 'YATIM',
            'status' => 'ACTIVE'
        ]);

        $billing = Billing::create([
            'student_id' => $student->id,
            'fee_master_id' => $feeMaster->id,
            'title' => 'SPP SMP',
            'original_amount' => 150000,
            'discount_applied' => 50000,
            'final_amount' => 100000,
            'status' => 'UNPAID'
        ]);

        Livewire::actingAs($this->admin)
            ->test(DiscountIndex::class)
            ->dispatch('execute-delete-discount', id: $discount->id);

        // Discount is deleted
        $this->assertDatabaseMissing('discounts', ['id' => $discount->id]);

        // Billings are NOT recalculated (keeps discount and final amount)
        $billing->refresh();
        $this->assertEquals(50000, $billing->discount_applied);
        $this->assertEquals(100000, $billing->final_amount);
    }
}
