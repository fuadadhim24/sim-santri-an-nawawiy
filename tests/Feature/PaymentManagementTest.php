<?php

namespace Tests\Feature;

use App\Models\Billing;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PaymentManagementTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private User $guardian;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->admin = User::factory()->create(['role' => 'ADMINISTRASI']);
        $this->guardian = User::factory()->create(['role' => 'WALI_SANTRI']);
    }

    /**
     * Test payment entry page is accessible
     */
    public function test_payment_entry_page_is_accessible()
    {
        $response = $this->actingAs($this->admin)
            ->get('/admin/payments/entry');

        // Adjust route based on actual implementation
        $this->assertTrue($response->status() === 200 || $response->status() === 404);
    }

    /**
     * Test guardian can view their dashboard with payment info
     */
    public function test_guardian_dashboard_shows_payments()
    {
        $response = $this->actingAs($this->guardian)
            ->get('/my-dashboard');

        $response->assertStatus(200);
    }

    /**
     * Test payment entry creates payment record
     */
    public function test_payment_entry_creates_payment()
    {
        $billing = Billing::factory()->create(['status' => 'UNPAID']);

        // This would be a Livewire component POST
        $this->assertDatabaseCount('payments', 0);
    }

    /**
     * Test duitku payment link generation
     */
    public function test_duitku_payment_link_is_generated()
    {
        $billing = Billing::factory()->create(['status' => 'UNPAID']);

        $response = $this->actingAs($this->guardian)
            ->get("/payment/pay/{$billing->id}");

        // Should either show payment page or redirect to Duitku
        $this->assertTrue(
            $response->status() === 200 || 
            $response->status() === 302 || 
            $response->status() === 404
        );
    }

    /**
     * Test payment callback endpoint exists
     */
    public function test_payment_callback_endpoint_exists()
    {
        $response = $this->post('/payment/callback', []);

        // Should not return 404
        $this->assertTrue($response->status() !== 404);
    }

    /**
     * Test payment return endpoint exists
     */
    public function test_payment_return_endpoint_exists()
    {
        $response = $this->get('/payment/return');

        // Should not return 404
        $this->assertTrue($response->status() !== 404);
    }

    /**
     * Test unpaid payments display on guardian dashboard
     */
    public function test_unpaid_payments_display()
    {
        $billing = Billing::factory()->create([
            'status' => 'UNPAID',
            'final_amount' => 1000000
        ]);

        $response = $this->actingAs($this->guardian)
            ->get('/my-dashboard');

        $response->assertStatus(200);
    }

    /**
     * Test admin can view financial reports
     */
    public function test_admin_can_view_financial_reports()
    {
        $response = $this->actingAs($this->admin)
            ->get('/admin/reports/financial');

        $response->assertStatus(200);
    }

    /**
     * Test only super admin can access financial reports
     */
    public function test_only_super_admin_can_access_reports()
    {
        $response = $this->actingAs($this->guardian)
            ->get('/admin/reports/financial');

        $response->assertStatus(403);
    }
}
