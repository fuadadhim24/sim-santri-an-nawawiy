<?php

namespace Tests\Feature\Livewire;

use App\Livewire\SpecialStatusIndex;
use App\Models\SpecialStatus;
use App\Models\Student;
use App\Models\Discount;
use App\Models\FeeMaster;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class SpecialStatusIndexTest extends TestCase
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
            ->test(SpecialStatusIndex::class)
            ->assertStatus(200);
    }

    public function test_can_create_new_special_status()
    {
        Livewire::actingAs($this->admin)
            ->test(SpecialStatusIndex::class)
            ->call('saveData', null, 'TEST_NEW_STATUS', 'Test New Status', 'Test Description')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('special_statuses', [
            'code' => 'TEST_NEW_STATUS',
            'name' => 'Test New Status',
            'is_system' => false,
        ]);
    }

    public function test_can_edit_existing_status()
    {
        $status = SpecialStatus::create([
            'code' => 'CUSTOM_PREV',
            'name' => 'Custom Prev Name',
            'is_system' => false,
        ]);

        Livewire::actingAs($this->admin)
            ->test(SpecialStatusIndex::class)
            ->call('saveData', $status->id, 'CUSTOM_PREV', 'Updated Custom Name', 'Updated Description')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('special_statuses', [
            'id' => $status->id,
            'name' => 'Updated Custom Name',
        ]);
    }

    public function test_editing_status_code_updates_related_students_and_discounts()
    {
        $status = SpecialStatus::create([
            'code' => 'CUSTOM_PREV',
            'name' => 'Custom Prev Name',
            'is_system' => false,
        ]);

        $student = Student::factory()->create([
            'special_status' => 'CUSTOM_PREV',
        ]);

        $feeMaster = FeeMaster::factory()->create();
        $discount = Discount::create([
            'fee_master_id' => $feeMaster->id,
            'target_status' => 'CUSTOM_PREV',
            'discount_amount' => 5000,
        ]);

        Livewire::actingAs($this->admin)
            ->test(SpecialStatusIndex::class)
            ->call('saveData', $status->id, 'CUSTOM_NEW', 'Custom Prev Name', '')
            ->assertHasNoErrors();

        // Database has updated code
        $this->assertDatabaseHas('special_statuses', [
            'id' => $status->id,
            'code' => 'CUSTOM_NEW',
        ]);

        // Student status is updated
        $student->refresh();
        $this->assertEquals('CUSTOM_NEW', $student->special_status);

        // Discount target is updated
        $discount->refresh();
        $this->assertEquals('CUSTOM_NEW', $discount->target_status);
    }

    public function test_cannot_delete_system_status()
    {
        $status = SpecialStatus::where('code', 'UMUM')->first();
        $this->assertNotNull($status);

        Livewire::actingAs($this->admin)
            ->test(SpecialStatusIndex::class)
            ->call('confirmDelete', $status->id)
            ->assertDispatched('swal:error');

        $this->assertDatabaseHas('special_statuses', ['code' => 'UMUM']);
    }

    public function test_cannot_delete_status_in_use()
    {
        $status = SpecialStatus::create([
            'code' => 'IN_USE',
            'name' => 'In Use Status',
            'is_system' => false,
        ]);

        Student::factory()->create([
            'special_status' => 'IN_USE',
        ]);

        Livewire::actingAs($this->admin)
            ->test(SpecialStatusIndex::class)
            ->call('confirmDelete', $status->id)
            ->assertDispatched('swal:error-in-use');

        $this->assertDatabaseHas('special_statuses', ['code' => 'IN_USE']);
    }

    public function test_can_delete_unused_status()
    {
        $status = SpecialStatus::create([
            'code' => 'UNUSED',
            'name' => 'Unused Status',
            'is_system' => false,
        ]);

        Livewire::actingAs($this->admin)
            ->test(SpecialStatusIndex::class)
            ->call('confirmDelete', $status->id)
            ->assertDispatched('confirm-delete-special-status');

        Livewire::actingAs($this->admin)
            ->test(SpecialStatusIndex::class)
            ->call('executeDelete', $status->id);

        $this->assertDatabaseMissing('special_statuses', ['code' => 'UNUSED']);
    }
}
