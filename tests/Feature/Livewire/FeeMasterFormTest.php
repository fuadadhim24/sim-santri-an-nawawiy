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

        // Create 3 students with different statuses
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

        // Assert only the $activeDiterima student received a billing
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

        // Assert billing has due date
        $this->assertDatabaseHas('billings', [
            'student_id' => $student->id,
            'fee_master_id' => $feeMaster->id
        ]);
        
        $billing = \App\Models\Billing::where('fee_master_id', $feeMaster->id)->first();
        $this->assertNotNull($billing);
    }
}
