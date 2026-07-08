<?php

namespace Tests\Feature\Livewire;

use App\Livewire\FeeMasterForm;
use App\Models\FeeCategory;
use App\Models\FeeMaster;
use App\Models\Student;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class FeeMasterFormTest extends TestCase
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
            ->test(FeeMasterForm::class)
            ->assertStatus(200);
    }

    public function test_only_bills_active_diterima_students()
    {
        $category = FeeCategory::create(['name' => 'Bulanan', 'type' => 'bulanan', 'code' => 'SPP']);

        $activeDiterima = Student::factory()->create(['is_active' => true, 'status' => 'diterima']);
        $activeMenunggu = Student::factory()->create(['is_active' => true, 'status' => 'menunggu']);
        $inactiveLulus = Student::factory()->create(['is_active' => false, 'status' => 'lulus']);

        Livewire::actingAs($this->admin)
            ->test(FeeMasterForm::class)
            ->set('item_name', 'SPP Juli')
            ->set('amount', 500000)
            ->set('fee_category_id', $category->id)
            ->set('start_date', '2026-07-01')
            ->call('processSave')
            ->assertHasNoErrors();

        $feeMaster = FeeMaster::where('item_name', 'SPP Juli')->first();
        $this->assertNotNull($feeMaster);

        $this->assertDatabaseHas('billings', [
            'student_id' => $activeDiterima->id,
            'fee_master_id' => $feeMaster->id
        ]);

        $this->assertDatabaseMissing('billings', [
            'student_id' => $activeMenunggu->id,
            'fee_master_id' => $feeMaster->id
        ]);

        $this->assertDatabaseMissing('billings', [
            'student_id' => $inactiveLulus->id,
            'fee_master_id' => $feeMaster->id
        ]);
    }

    public function test_generates_monthly_recurrence_with_due_date()
    {
        $category = FeeCategory::create(['name' => 'Bulanan', 'type' => 'bulanan', 'code' => 'SPP']);
        $student = Student::factory()->create(['is_active' => true, 'status' => 'diterima']);

        Livewire::actingAs($this->admin)
            ->test(FeeMasterForm::class)
            ->set('item_name', 'SPP Rutin')
            ->set('amount', 500000)
            ->set('fee_category_id', $category->id)
            ->set('recurrence_type', 'MONTHLY')
            ->set('billing_day', 10)
            ->set('due_days', 14)
            ->call('processSave')
            ->assertHasNoErrors();

        $feeMaster = FeeMaster::where('item_name', 'SPP Rutin')->first();
        
        $this->assertEquals('MONTHLY', $feeMaster->recurrence_type);
        $this->assertEquals(10, $feeMaster->billing_day);
        $this->assertEquals(14, $feeMaster->due_days);

        $this->assertDatabaseHas('billings', [
            'student_id' => $student->id,
            'fee_master_id' => $feeMaster->id
        ]);
        
        $billing = \App\Models\Billing::where('fee_master_id', $feeMaster->id)->first();
        $this->assertNotNull($billing);
    }

    public function test_target_class_level_and_every_6_months_recurrence()
    {
        $category = FeeCategory::create(['name' => 'Semester', 'type' => 'bulanan', 'code' => 'SEM']);
        
        $class7 = \App\Models\ClassLevel::create(['name' => 'Kelas 7 SMP', 'level_order' => 1]);
        $class8 = \App\Models\ClassLevel::create(['name' => 'Kelas 8 SMP', 'level_order' => 2]);

        $studentClass7 = Student::factory()->create(['is_active' => true, 'status' => 'diterima', 'class_level_id' => $class7->id]);
        $studentClass8 = Student::factory()->create(['is_active' => true, 'status' => 'diterima', 'class_level_id' => $class8->id]);

        Livewire::actingAs($this->admin)
            ->test(FeeMasterForm::class)
            ->set('item_name', 'Biaya Ujian')
            ->set('amount', 300000)
            ->set('fee_category_id', $category->id)
            ->set('recurrence_type', 'EVERY_6_MONTHS')
            ->set('class_level_target_id', $class7->id)
            ->set('billing_day', 5)
            ->set('due_days', 10)
            ->call('processSave')
            ->assertHasNoErrors();

        $feeMaster = FeeMaster::where('item_name', 'Biaya Ujian')->first();
        $this->assertNotNull($feeMaster);
        $this->assertEquals($class7->id, $feeMaster->class_level_target_id);
        $this->assertEquals('EVERY_6_MONTHS', $feeMaster->recurrence_type);

        $this->assertDatabaseHas('billings', [
            'student_id' => $studentClass7->id,
            'fee_master_id' => $feeMaster->id
        ]);

        $this->assertDatabaseMissing('billings', [
            'student_id' => $studentClass8->id,
            'fee_master_id' => $feeMaster->id
        ]);
    }

    public function test_only_active_categories_are_shown_in_form()
    {
        $activeCategory = FeeCategory::create(['name' => 'Active Category', 'code' => 'ACTIVE_CAT', 'is_active' => true]);
        $inactiveCategory = FeeCategory::create(['name' => 'Inactive Category', 'code' => 'INACTIVE_CAT', 'is_active' => false]);

        Livewire::actingAs($this->admin)
            ->test(FeeMasterForm::class)
            ->assertSet('feeCategories', function ($categories) use ($activeCategory, $inactiveCategory) {
                return $categories->contains('id', $activeCategory->id) 
                    && !$categories->contains('id', $inactiveCategory->id);
            });
    }

    public function test_edit_fee_master_does_not_retroactively_update_billings()
    {
        $category = FeeCategory::create(['name' => 'Bulanan', 'type' => 'bulanan', 'code' => 'SPP']);
        $student = Student::factory()->create(['is_active' => true, 'status' => 'diterima']);

        $feeMaster = FeeMaster::create([
            'item_name' => 'SPP Awal',
            'amount' => 100000,
            'fee_category_id' => $category->id,
            'recurrence_type' => 'MONTHLY',
            'is_active' => true,
        ]);

        $billing = \App\Models\Billing::create([
            'student_id' => $student->id,
            'fee_master_id' => $feeMaster->id,
            'title' => 'SPP Awal',
            'original_amount' => 100000,
            'discount_applied' => 0,
            'final_amount' => 100000,
            'status' => 'UNPAID',
            'due_date' => now()->format('Y-m-d'),
            'version' => 1,
            'visible_to_wali' => true,
        ]);

        Livewire::actingAs($this->admin)
            ->test(FeeMasterForm::class, ['feeMaster' => $feeMaster])
            ->set('amount', 150000)
            ->call('processSave')
            ->assertHasNoErrors();

        $newFeeMaster = FeeMaster::where('amount', 150000)->first();
        $this->assertNotNull($newFeeMaster);

        // Billing remains unchanged and linked to old fee master
        $billing->refresh();
        $this->assertEquals(100000, $billing->original_amount);
        $this->assertEquals($feeMaster->id, $billing->fee_master_id);
    }

    public function test_edit_fee_master_with_none_policy_does_not_update_any_billings()
    {
        $category = FeeCategory::create(['name' => 'Bulanan', 'type' => 'bulanan', 'code' => 'SPP']);
        $student = Student::factory()->create(['is_active' => true, 'status' => 'diterima']);

        $feeMaster = FeeMaster::create([
            'item_name' => 'SPP Awal',
            'amount' => 100000,
            'fee_category_id' => $category->id,
            'recurrence_type' => 'MONTHLY',
            'is_active' => true,
        ]);

        $billing = \App\Models\Billing::create([
            'student_id' => $student->id,
            'fee_master_id' => $feeMaster->id,
            'title' => 'SPP Awal',
            'original_amount' => 100000,
            'discount_applied' => 0,
            'final_amount' => 100000,
            'status' => 'UNPAID',
            'due_date' => now()->format('Y-m-d'),
            'version' => 1,
            'visible_to_wali' => true,
        ]);

        Livewire::actingAs($this->admin)
            ->test(FeeMasterForm::class, ['feeMaster' => $feeMaster])
            ->set('amount', 150000)
            ->set('update_policy', 'none')
            ->call('processSave')
            ->assertHasNoErrors();

        $newFeeMaster = FeeMaster::where('amount', 150000)->first();
        $this->assertNotNull($newFeeMaster);

        // Billing remains unchanged and linked to old fee master
        $billing->refresh();
        $this->assertEquals(100000, $billing->original_amount);
        $this->assertEquals($feeMaster->id, $billing->fee_master_id);

        // No billing exists under new fee master
        $this->assertFalse(\App\Models\Billing::where('fee_master_id', $newFeeMaster->id)->exists());
    }

    public function test_edit_fee_master_copies_discounts_to_new_fee_master()
    {
        $category = FeeCategory::create(['name' => 'Bulanan', 'type' => 'bulanan', 'code' => 'SPP']);
        $feeMaster = FeeMaster::create([
            'item_name' => 'SPP Awal',
            'amount' => 100000,
            'fee_category_id' => $category->id,
            'recurrence_type' => 'MONTHLY',
            'is_active' => true,
        ]);

        // Create a discount for the old fee master
        $discount = \App\Models\Discount::create([
            'fee_master_id' => $feeMaster->id,
            'target_status' => 'YATIM',
            'discount_amount' => 15000,
        ]);

        Livewire::actingAs($this->admin)
            ->test(FeeMasterForm::class, ['feeMaster' => $feeMaster])
            ->set('amount', 120000)
            ->call('processSave')
            ->assertHasNoErrors();

        $newFeeMaster = FeeMaster::where('amount', 120000)->first();
        $this->assertNotNull($newFeeMaster);

        // Assert discount is copied to the new fee master
        $this->assertDatabaseHas('discounts', [
            'fee_master_id' => $newFeeMaster->id,
            'target_status' => 'YATIM',
            'discount_amount' => 15000,
        ]);
    }
}
