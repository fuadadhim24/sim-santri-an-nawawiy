<?php

namespace Tests\Feature\Livewire;

use App\Livewire\FeeMasterIndex;
use App\Models\FeeCategory;
use App\Models\FeeMaster;
use App\Models\Student;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class FeeMasterIndexTest extends TestCase
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
            ->test(FeeMasterIndex::class)
            ->assertStatus(200);
    }

    public function test_confirm_delete_dispatches_simple_confirm_when_no_unpaid_billings()
    {
        $category = FeeCategory::create(['name' => 'Bulanan', 'code' => 'SPP']);
        $feeMaster = FeeMaster::create([
            'item_name' => 'SPP Uji',
            'amount' => 100000,
            'fee_category_id' => $category->id,
            'recurrence_type' => 'MONTHLY',
            'is_active' => true,
        ]);

        Livewire::actingAs($this->admin)
            ->test(FeeMasterIndex::class)
            ->call('confirmDelete', $feeMaster->id)
            ->assertDispatched('show-simple-delete-confirm-modal');
    }

    public function test_confirm_delete_dispatches_complex_confirm_when_has_unpaid_billings()
    {
        $category = FeeCategory::create(['name' => 'Bulanan', 'code' => 'SPP']);
        $student = Student::factory()->create();
        $feeMaster = FeeMaster::create([
            'item_name' => 'SPP Uji',
            'amount' => 100000,
            'fee_category_id' => $category->id,
            'recurrence_type' => 'MONTHLY',
            'is_active' => true,
        ]);

        // Create an unpaid billing
        \App\Models\Billing::create([
            'student_id' => $student->id,
            'fee_master_id' => $feeMaster->id,
            'title' => 'SPP Uji',
            'original_amount' => 100000,
            'discount_applied' => 0,
            'final_amount' => 100000,
            'status' => 'UNPAID',
            'version' => 1,
            'visible_to_wali' => true,
        ]);

        Livewire::actingAs($this->admin)
            ->test(FeeMasterIndex::class)
            ->call('confirmDelete', $feeMaster->id)
            ->assertDispatched('show-delete-confirm-modal');
    }

    public function test_execute_delete_without_voiding_billings_keeps_unpaid_billings()
    {
        $category = FeeCategory::create(['name' => 'Bulanan', 'code' => 'SPP']);
        $student = Student::factory()->create();
        $feeMaster = FeeMaster::create([
            'item_name' => 'SPP Uji',
            'amount' => 100000,
            'fee_category_id' => $category->id,
            'recurrence_type' => 'MONTHLY',
            'is_active' => true,
        ]);

        $billing = \App\Models\Billing::create([
            'student_id' => $student->id,
            'fee_master_id' => $feeMaster->id,
            'title' => 'SPP Uji',
            'original_amount' => 100000,
            'discount_applied' => 0,
            'final_amount' => 100000,
            'status' => 'UNPAID',
            'version' => 1,
            'visible_to_wali' => true,
        ]);

        Livewire::actingAs($this->admin)
            ->test(FeeMasterIndex::class)
            ->call('executeDelete', $feeMaster->id, false);

        $feeMaster->refresh();
        $this->assertTrue($feeMaster->trashed());

        // Billing should still be active (unpaid) and NOT archived
        $billing->refresh();
        $this->assertEquals('UNPAID', $billing->status);
        $this->assertNull($billing->archived_at);
    }

    public function test_execute_delete_always_preserves_unpaid_billings()
    {
        $category = FeeCategory::create(['name' => 'Bulanan', 'code' => 'SPP']);
        $student = Student::factory()->create();
        $feeMaster = FeeMaster::create([
            'item_name' => 'SPP Uji',
            'amount' => 100000,
            'fee_category_id' => $category->id,
            'recurrence_type' => 'MONTHLY',
            'is_active' => true,
        ]);

        $billing = \App\Models\Billing::create([
            'student_id' => $student->id,
            'fee_master_id' => $feeMaster->id,
            'title' => 'SPP Uji',
            'original_amount' => 100000,
            'discount_applied' => 0,
            'final_amount' => 100000,
            'status' => 'UNPAID',
            'version' => 1,
            'visible_to_wali' => true,
        ]);

        Livewire::actingAs($this->admin)
            ->test(FeeMasterIndex::class)
            ->call('executeDelete', $feeMaster->id);

        $feeMaster->refresh();
        $this->assertTrue($feeMaster->trashed());

        // Billing should still be active and NOT archived
        $billing->refresh();
        $this->assertEquals('UNPAID', $billing->status);
        $this->assertNull($billing->archived_at);
    }
}
