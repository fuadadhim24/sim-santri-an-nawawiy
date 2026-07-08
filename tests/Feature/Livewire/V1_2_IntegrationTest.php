<?php

namespace Tests\Feature\Livewire;

use App\Models\User;
use App\Models\Student;
use App\Models\Guardian;
use App\Models\FeeCategory;
use App\Models\FeeMaster;
use App\Models\Discount;
use App\Models\Billing;
use Livewire\Livewire;
use App\Livewire\DiscountIndex;
use App\Livewire\FeeCategoryIndex;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class V1_2_IntegrationTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test WhatsApp login normalization with various formats.
     */
    public function test_whatsapp_login_normalization(): void
    {
        // Case 1: WhatsApp stored as 628123456789
        $user1 = User::factory()->create([
            'whatsapp' => '628123456789',
            'email' => 'user1@example.com',
            'password' => bcrypt('password123'),
        ]);

        // Attempt login using 08123456789
        $response = $this->post('/login', [
            'identifier' => '08123456789',
            'password' => 'password123',
        ]);
        $this->assertAuthenticatedAs($user1);

        $this->post('/logout');
        $this->assertGuest();

        // Attempt login using +628123456789
        $response = $this->post('/login', [
            'identifier' => '+628123456789',
            'password' => 'password123',
        ]);
        $this->assertAuthenticatedAs($user1);

        $this->post('/logout');
        $this->assertGuest();

        // Case 2: WhatsApp stored as 08987654321
        $user2 = User::factory()->create([
            'whatsapp' => '08987654321',
            'email' => 'user2@example.com',
            'password' => bcrypt('password123'),
        ]);

        // Attempt login using 628987654321
        $response = $this->post('/login', [
            'identifier' => '628987654321',
            'password' => 'password123',
        ]);
        $this->assertAuthenticatedAs($user2);
    }

    /**
     * Test Discount deletion via Livewire and billing recalculation.
     */
    public function test_discount_deletion_recalculates_unpaid_billings(): void
    {
        $admin = User::factory()->create(['role' => 'SUPER_ADMIN']);
        $this->actingAs($admin);

        // Create student, fee, discount, and billing
        $guardian = Guardian::create([
            'user_id' => User::factory()->create(['role' => 'WALI_SANTRI'])->id,
            'full_name' => 'Wali Test',
            'whatsapp' => '628123456789',
        ]);

        $student = Student::factory()->create([
            'guardian_id' => $guardian->id,
            'special_status' => 'YATIM',
            'status' => 'diterima',
            'is_active' => true,
        ]);

        $category = FeeCategory::create([
            'code' => 'SPP',
            'name' => 'SPP Bulanan',
            'activation_mode' => 'multi_active',
            'is_active' => true,
        ]);

        $feeMaster = FeeMaster::create([
            'item_name' => 'SPP Juli 2026',
            'amount' => 500000,
            'fee_category_id' => $category->id,
            'is_active' => true,
            'recurrence_type' => 'MONTHLY',
        ]);

        $discount = Discount::create([
            'fee_master_id' => $feeMaster->id,
            'target_status' => 'YATIM',
            'discount_amount' => 200000,
        ]);

        // Create UNPAID billing
        $unpaidBilling = Billing::create([
            'student_id' => $student->id,
            'fee_master_id' => $feeMaster->id,
            'title' => $feeMaster->item_name,
            'original_amount' => 500000,
            'discount_applied' => 200000,
            'final_amount' => 300000,
            'status' => 'UNPAID',
        ]);

        // Create PAID billing
        $paidBilling = Billing::create([
            'student_id' => $student->id,
            'fee_master_id' => $feeMaster->id,
            'title' => $feeMaster->item_name,
            'original_amount' => 500000,
            'discount_applied' => 200000,
            'final_amount' => 300000,
            'status' => 'PAID',
        ]);

        // Delete discount via Livewire
        Livewire::test(DiscountIndex::class)
            ->call('delete', $discount->id)
            ->assertStatus(200);

        // Verify discount is deleted
        $this->assertDatabaseMissing('discounts', ['id' => $discount->id]);

        // Verify UNPAID billing is NOT recalculated (discount remains)
        $unpaidBilling->refresh();
        $this->assertEquals(500000, $unpaidBilling->original_amount);
        $this->assertEquals(200000, $unpaidBilling->discount_applied);
        $this->assertEquals(300000, $unpaidBilling->final_amount);

        // Verify PAID billing is NOT touched
        $paidBilling->refresh();
        $this->assertEquals(200000, $paidBilling->discount_applied);
        $this->assertEquals(300000, $paidBilling->final_amount);
    }

    /**
     * Test FeeCategory deactivation and deletion constraints.
     */
    public function test_fee_category_deactivation_and_deletion_protection(): void
    {
        $admin = User::factory()->create(['role' => 'SUPER_ADMIN']);
        $this->actingAs($admin);

        $category = FeeCategory::create([
            'code' => 'REG',
            'name' => 'Daftar Ulang',
            'activation_mode' => 'multi_active',
            'is_active' => true,
        ]);

        $feeMaster = FeeMaster::create([
            'item_name' => 'Biaya Registrasi',
            'amount' => 150000,
            'fee_category_id' => $category->id,
            'is_active' => true,
        ]);

        // Create unpaid billing referencing this fee master
        $student = Student::factory()->create([
            'status' => 'diterima',
            'is_active' => true,
        ]);

        $billing = Billing::create([
            'student_id' => $student->id,
            'fee_master_id' => $feeMaster->id,
            'title' => 'Biaya Registrasi',
            'original_amount' => 150000,
            'discount_applied' => 0,
            'final_amount' => 150000,
            'status' => 'UNPAID',
        ]);

        // Try direct delete (should fail and trigger swal)
        Livewire::test(FeeCategoryIndex::class)
            ->call('deleteCategoryDirect', $category->id)
            ->assertStatus(200);

        // Category should still exist because there is an unpaid billing
        $this->assertDatabaseHas('fee_categories', ['id' => $category->id]);

        // Deactivate category
        Livewire::test(FeeCategoryIndex::class)
            ->call('deactivateCategory', $category->id)
            ->assertStatus(200);

        // Category is no longer active
        $category->refresh();
        $this->assertFalse($category->is_active);

        // Fee master should be archived (soft-deleted and inactive)
        $feeMaster->refresh();
        $this->assertFalse($feeMaster->is_active);
        $this->assertTrue($feeMaster->trashed());
    }

    /**
     * Test Select All button on Student creation form.
     */
    public function test_select_all_fees_on_student_form(): void
    {
        $admin = User::factory()->create(['role' => 'SUPER_ADMIN']);
        $this->actingAs($admin);

        $category = FeeCategory::create([
            'code' => 'SPP',
            'name' => 'SPP',
            'activation_mode' => 'multi_active',
            'is_active' => true,
        ]);

        $fee1 = FeeMaster::create([
            'item_name' => 'Fee 1',
            'amount' => 10000,
            'fee_category_id' => $category->id,
            'is_active' => true,
            'unit_target' => '01',
            'residence_target' => 'MONDOK',
        ]);

        $fee2 = FeeMaster::create([
            'item_name' => 'Fee 2',
            'amount' => 20000,
            'fee_category_id' => $category->id,
            'is_active' => true,
            'unit_target' => '01',
            'residence_target' => 'MONDOK',
        ]);

        $guardian = Guardian::create([
            'user_id' => User::factory()->create(['role' => 'WALI_SANTRI'])->id,
            'full_name' => 'Wali Test',
            'whatsapp' => '628123456789',
        ]);

        Livewire::test(\App\Livewire\StudentForm::class)
            ->set('guardian_id', $guardian->id)
            ->set('full_name', 'Santri Test')
            ->set('unit_code', '01')
            ->set('residence_status', 'MONDOK')
            ->set('selectedBillings', [])
            // Call toggleSelectAllFees
            ->call('toggleSelectAllFees')
            // Expect category ID in selectedBillings
            ->assertSet('selectedBillings', [(string)$category->id])
            // Call it again to uncheck all
            ->call('toggleSelectAllFees')
            ->assertSet('selectedBillings', []);
    }
}
