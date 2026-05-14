<?php

namespace Tests\Feature\Billing;

use Tests\TestCase;
use App\Models\User;
use App\Models\Student;
use App\Models\FeeCategory;
use App\Models\FeeMaster;
use App\Models\Billing;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CreateBillingTest extends TestCase
{
    use RefreshDatabase;
    public function test_admin_can_create_billing()
    {
        $admin = User::factory()->create(['role' => 'SUPER_ADMIN']);
        $category = FeeCategory::factory()->create();
        $student = Student::factory()->create(['status' => 'ACTIVE']);
        $feeMaster = FeeMaster::factory()->create(['fee_category_id' => $category->id]);

        $this->actingAs($admin)
            ->post('/admin/billings', [
                'student_id' => $student->id,
                'fee_master_id' => $feeMaster->id,
                'title' => 'Test Billing',
                'original_amount' => $feeMaster->amount,
            ])
            ->assertRedirect();

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
        $feeMaster = FeeMaster::factory()->create(['fee_category_id' => $category->id]);

        $this->actingAs($guardian)
            ->post('/admin/billings', [
                'student_id' => $student->id,
                'fee_master_id' => $feeMaster->id,
            ])
            ->assertForbidden();
    }

    public function test_cannot_create_duplicate_billing()
    {
        $admin = User::factory()->create(['role' => 'SUPER_ADMIN']);
        $category = FeeCategory::factory()->create();
        $student = Student::factory()->create(['status' => 'ACTIVE']);
        $feeMaster = FeeMaster::factory()->create(['fee_category_id' => $category->id]);

        Billing::factory()->create([
            'student_id' => $student->id,
            'fee_master_id' => $feeMaster->id,
            'status' => 'UNPAID'
        ]);

        $this->actingAs($admin)
            ->post('/admin/billings', [
                'student_id' => $student->id,
                'fee_master_id' => $feeMaster->id,
                'title' => 'Duplicate Billing',
                'original_amount' => $feeMaster->amount,
            ])
            ->assertSessionHasErrors();
    }

    public function test_billing_cannot_be_created_for_inactive_student()
    {
        $admin = User::factory()->create(['role' => 'SUPER_ADMIN']);
        $category = FeeCategory::factory()->create();
        $student = Student::factory()->create(['status' => 'GRADUATED']);
        $feeMaster = FeeMaster::factory()->create(['fee_category_id' => $category->id]);

        $this->actingAs($admin)
            ->post('/admin/billings', [
                'student_id' => $student->id,
                'fee_master_id' => $feeMaster->id,
                'title' => 'Test Billing',
                'original_amount' => $feeMaster->amount,
            ])
            ->assertSessionHasErrors();
    }

    public function test_paid_billing_cannot_be_edited()
    {
        $admin = User::factory()->create(['role' => 'SUPER_ADMIN']);
        $billing = Billing::factory()->create(['status' => 'PAID']);

        $this->actingAs($admin)
            ->patch("/admin/billings/{$billing->id}", [
                'title' => 'Updated Title',
            ])
            ->assertForbidden();
    }

    public function test_paid_billing_cannot_be_deleted()
    {
        $admin = User::factory()->create(['role' => 'SUPER_ADMIN']);
        $billing = Billing::factory()->create(['status' => 'PAID']);

        $this->actingAs($admin)
            ->delete("/admin/billings/{$billing->id}")
            ->assertForbidden();
    }
}
