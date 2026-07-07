<?php

namespace Tests\Feature\Livewire;

use App\Livewire\DiscountForm;
use App\Models\ClassLevel;
use App\Models\Discount;
use App\Models\FeeCategory;
use App\Models\FeeMaster;
use App\Models\Student;
use App\Models\Billing;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class DiscountFormTest extends TestCase
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
            ->test(DiscountForm::class)
            ->assertStatus(200);
    }

    public function test_can_create_discount()
    {
        $category = FeeCategory::create(['name' => 'SPP', 'code' => 'SPP']);
        $feeMaster = FeeMaster::create([
            'fee_category_id' => $category->id,
            'item_name' => 'SPP SMP',
            'amount' => 150000,
            'start_date' => '2026-01-01',
            'is_active' => true
        ]);

        Livewire::actingAs($this->admin)
            ->test(DiscountForm::class)
            ->set('fee_master_id', $feeMaster->id)
            ->set('target_status', 'YATIM')
            ->set('discount_amount', 50000)
            ->call('save')
            ->assertRedirect(route('admin.discounts'));

        $this->assertDatabaseHas('discounts', [
            'fee_master_id' => $feeMaster->id,
            'target_status' => 'YATIM',
            'discount_amount' => 50000
        ]);
    }

    public function test_affected_billings_preview_recalculates_correctly()
    {
        $category = FeeCategory::create(['name' => 'SPP', 'code' => 'SPP']);
        $feeMaster = FeeMaster::create([
            'fee_category_id' => $category->id,
            'item_name' => 'SPP SMP',
            'amount' => 150000,
            'start_date' => '2026-01-01',
            'is_active' => true
        ]);

        $student = Student::factory()->create([
            'special_status' => 'YATIM',
            'status' => 'diterima',
            'is_active' => true
        ]);

        $billing = Billing::create([
            'student_id' => $student->id,
            'fee_master_id' => $feeMaster->id,
            'title' => 'SPP Januari 2026',
            'original_amount' => 150000,
            'discount_applied' => 0,
            'final_amount' => 150000,
            'status' => 'UNPAID',
            'due_date' => '2026-01-10'
        ]);

        Livewire::actingAs($this->admin)
            ->test(DiscountForm::class)
            ->set('fee_master_id', $feeMaster->id)
            ->set('target_status', 'YATIM')
            ->set('discount_amount', 40000)
            ->assertSet('affectedBillings', collect([
                [
                    'student_name' => $student->full_name,
                    'student_nis' => $student->nis,
                    'billing_title' => 'SPP Januari 2026',
                    'original_amount' => 150000.0,
                    'current_discount' => 0.0,
                    'current_final' => 150000.0,
                    'new_discount' => 40000.0,
                    'new_final' => 110000.0,
                    'diff' => -40000.0,
                ]
            ]));
    }

    public function test_can_edit_discount_and_recalculate_based_on_policy()
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
            'discount_amount' => 30000
        ]);

        $student = Student::factory()->create([
            'special_status' => 'YATIM',
            'status' => 'diterima',
            'is_active' => true
        ]);

        // Overdue billing (due date in past)
        $overdueBilling = Billing::create([
            'student_id' => $student->id,
            'fee_master_id' => $feeMaster->id,
            'title' => 'SPP Januari 2020',
            'original_amount' => 150000,
            'discount_applied' => 30000,
            'final_amount' => 120000,
            'status' => 'UNPAID',
            'due_date' => '2020-01-10' // Past
        ]);

        // Future billing (due date in future relative to 2026 test runtime)
        $futureBilling = Billing::create([
            'student_id' => $student->id,
            'fee_master_id' => $feeMaster->id,
            'title' => 'SPP Desember 2026',
            'original_amount' => 150000,
            'discount_applied' => 30000,
            'final_amount' => 120000,
            'status' => 'UNPAID',
            'due_date' => '2026-12-10' // Future
        ]);

        // Current month billing
        $currentMonthBilling = Billing::create([
            'student_id' => $student->id,
            'fee_master_id' => $feeMaster->id,
            'title' => 'SPP Bulan Ini',
            'original_amount' => 150000,
            'discount_applied' => 30000,
            'final_amount' => 120000,
            'status' => 'UNPAID',
            'due_date' => now()->format('Y-m-d') // Today (This Month)
        ]);

        // Next month billing
        $nextMonthBilling = Billing::create([
            'student_id' => $student->id,
            'fee_master_id' => $feeMaster->id,
            'title' => 'SPP Bulan Depan',
            'original_amount' => 150000,
            'discount_applied' => 30000,
            'final_amount' => 120000,
            'status' => 'UNPAID',
            'due_date' => now()->addMonth()->startOfMonth()->format('Y-m-d') // Next Month
        ]);

        // Scenario 1: Only recalculate future billings (including this month)
        Livewire::actingAs($this->admin)
            ->test(DiscountForm::class, ['discount' => $discount])
            ->set('discount_amount', 50000)
            ->set('recalculate_policy', 'future')
            ->call('save')
            ->assertRedirect(route('admin.discounts'));

        // Future billing should be updated to 50000 discount
        $this->assertEquals(50000, $futureBilling->fresh()->discount_applied);
        $this->assertEquals(100000, $futureBilling->fresh()->final_amount);

        // Current month billing should be updated to 50000 discount (since it is today/future)
        $this->assertEquals(50000, $currentMonthBilling->fresh()->discount_applied);
        $this->assertEquals(100000, $currentMonthBilling->fresh()->final_amount);

        // Overdue billing should remain at 30000 discount
        $this->assertEquals(30000, $overdueBilling->fresh()->discount_applied);
        $this->assertEquals(120000, $overdueBilling->fresh()->final_amount);

        // Reset discounts for next scenario
        $futureBilling->refresh()->update(['discount_applied' => 30000, 'final_amount' => 120000]);
        $currentMonthBilling->refresh()->update(['discount_applied' => 30000, 'final_amount' => 120000]);

        // Scenario 2: Only recalculate starting next month (excluding this month)
        Livewire::actingAs($this->admin)
            ->test(DiscountForm::class, ['discount' => $discount])
            ->set('discount_amount', 60000)
            ->set('recalculate_policy', 'next_month')
            ->call('save')
            ->assertRedirect(route('admin.discounts'));

        // Next month billing should be updated to 60000 discount
        $this->assertEquals(60000, $nextMonthBilling->fresh()->discount_applied);
        $this->assertEquals(90000, $nextMonthBilling->fresh()->final_amount);

        // Future billing (which is Dec 2026) should be updated to 60000 discount
        $this->assertEquals(60000, $futureBilling->fresh()->discount_applied);
        $this->assertEquals(90000, $futureBilling->fresh()->final_amount);

        // Current month billing should remain at 30000 discount (excluded!)
        $this->assertEquals(30000, $currentMonthBilling->fresh()->discount_applied);
        $this->assertEquals(120000, $currentMonthBilling->fresh()->final_amount);

        // Overdue billing should remain at 30000 discount
        $this->assertEquals(30000, $overdueBilling->fresh()->discount_applied);
        $this->assertEquals(120000, $overdueBilling->fresh()->final_amount);
    }
}
