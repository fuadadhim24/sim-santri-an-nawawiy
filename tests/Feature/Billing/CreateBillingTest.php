<?php

namespace Tests\Feature\Billing;

use Tests\TestCase;
use App\Models\User;
use App\Models\Student;
use App\Models\FeeCategory;
use App\Models\FeeMaster;
use App\Models\Billing;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

class CreateBillingTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_create_billing()
    {
        $admin = User::factory()->create(['role' => 'SUPER_ADMIN']);
        $category = FeeCategory::factory()->create();
        $student = Student::factory()->create(['status' => 'ACTIVE']);
        $feeMaster = FeeMaster::factory()->create([
            'fee_category_id' => $category->id,
            'amount' => 10000,
            'unit_target' => null,
            'residence_target' => null,
        ]);

        $this->actingAs($admin);

        Livewire::test(\App\Livewire\BillingForm::class)
            ->set('student_id', $student->id)
            ->set('fee_master_id', $feeMaster->id)
            ->set('title', 'Test Billing')
            ->set('original_amount', $feeMaster->amount)
            ->set('final_amount', $feeMaster->amount)
            ->call('save')
            ->assertHasNoErrors()
            ->assertRedirect(route('admin.billings'));

        $this->assertDatabaseHas('billings', [
            'student_id' => $student->id,
            'fee_master_id' => $feeMaster->id,
        ]);
    }

    public function test_guardian_cannot_create_billing()
    {
        $guardian = User::factory()->create(['role' => 'WALI_SANTRI']);
        $category = FeeCategory::factory()->create();
        $student = Student::factory()->create();
        $feeMaster = FeeMaster::factory()->create([
            'fee_category_id' => $category->id,
            'unit_target' => null,
            'residence_target' => null,
        ]);

        $this->actingAs($guardian);

        Livewire::test(\App\Livewire\BillingForm::class)
            ->set('student_id', $student->id)
            ->set('fee_master_id', $feeMaster->id)
            ->set('title', 'Test Billing')
            ->set('original_amount', $feeMaster->amount)
            ->set('final_amount', $feeMaster->amount)
            ->call('save')
            ->assertStatus(403);
    }

    public function test_cannot_create_duplicate_billing()
    {
        $admin = User::factory()->create(['role' => 'SUPER_ADMIN']);
        $category = FeeCategory::factory()->create();
        $student = Student::factory()->create(['status' => 'ACTIVE']);
        $feeMaster = FeeMaster::factory()->create([
            'fee_category_id' => $category->id,
            'unit_target' => null,
            'residence_target' => null,
        ]);

        Billing::factory()->create([
            'student_id' => $student->id,
            'fee_master_id' => $feeMaster->id,
            'status' => 'UNPAID'
        ]);

        $this->actingAs($admin);

        Livewire::test(\App\Livewire\BillingForm::class)
            ->set('student_id', $student->id)
            ->set('fee_master_id', $feeMaster->id)
            ->set('title', 'Duplicate Billing')
            ->set('original_amount', $feeMaster->amount)
            ->set('final_amount', $feeMaster->amount)
            ->call('save')
            ->assertNoRedirect();

        $this->assertEquals(1, Billing::count());
    }

    public function test_billing_cannot_be_created_for_inactive_student()
    {
        $admin = User::factory()->create(['role' => 'SUPER_ADMIN']);
        $category = FeeCategory::factory()->create();
        $student = Student::factory()->create(['status' => 'GRADUATED']);
        $feeMaster = FeeMaster::factory()->create([
            'fee_category_id' => $category->id,
            'unit_target' => null,
            'residence_target' => null,
        ]);

        $this->actingAs($admin);

        Livewire::test(\App\Livewire\BillingForm::class)
            ->set('student_id', $student->id)
            ->set('fee_master_id', $feeMaster->id)
            ->set('title', 'Test Billing')
            ->set('original_amount', $feeMaster->amount)
            ->set('final_amount', $feeMaster->amount)
            ->call('save')
            ->assertNoRedirect();

        $this->assertEquals(0, Billing::count());
    }

    public function test_paid_billing_cannot_be_edited()
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Tagihan yang sudah dibayar tidak dapat diubah.');

        $billing = Billing::factory()->create(['status' => 'PAID']);
        $billing->update(['title' => 'Updated Title']);
    }

    public function test_paid_billing_cannot_be_deleted()
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Tidak dapat menghapus tagihan yang sudah dibayar.');

        $billing = Billing::factory()->create(['status' => 'PAID']);
        $billing->delete();
    }
}
