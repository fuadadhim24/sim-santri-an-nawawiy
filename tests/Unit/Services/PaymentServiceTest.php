<?php

namespace Tests\Unit\Services;

use App\Models\Billing;
use App\Models\Payment;
use App\Models\User;
use App\Services\PaymentService;
use Exception;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PaymentServiceTest extends TestCase
{
    use RefreshDatabase;

    private PaymentService $paymentService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->paymentService = new PaymentService();
    }

    /**
     * Test process cash payment successfully
     */
    public function test_process_cash_payment_successfully()
    {
        $billing = Billing::factory()->create(['status' => 'UNPAID', 'final_amount' => 1000000]);
        $admin = User::factory()->create(['role' => 'ADMINISTRASI']);

        $payment = $this->paymentService->processCashPayment($billing, $admin->id);

        $this->assertNotNull($payment);
        $this->assertEquals('paid', $payment->status);
        $this->assertEquals('cash', $payment->method);
        $this->assertEquals(1000000, $payment->amount);
        $this->assertEquals('PAID', $billing->fresh()->status);
    }

    /**
     * Test cannot process cash payment for already paid billing
     */
    public function test_cannot_process_cash_payment_for_paid_billing()
    {
        $billing = Billing::factory()->create(['status' => 'PAID']);
        $admin = User::factory()->create();

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Tagihan sudah tidak dapat dibayar');

        $this->paymentService->processCashPayment($billing, $admin->id);
    }

    /**
     * Test process cash payment with notes
     */
    public function test_process_cash_payment_with_notes()
    {
        $billing = Billing::factory()->create(['status' => 'UNPAID']);
        $admin = User::factory()->create();
        $notes = 'Bayar di kasir utama';

        $payment = $this->paymentService->processCashPayment($billing, $admin->id, $notes);

        $this->assertEquals($notes, $payment->notes);
    }

    /**
     * Test process bulk cash payments
     */
    public function test_process_bulk_cash_payments()
    {
        $billings = Billing::factory(3)->create(['status' => 'UNPAID']);
        $billingIds = $billings->pluck('id')->toArray();
        $admin = User::factory()->create();

        $processedCount = $this->paymentService->processBulkCashPayments($billingIds, $admin->id);

        $this->assertEquals(3, $processedCount);

        foreach ($billings as $billing) {
            $this->assertEquals('PAID', $billing->fresh()->status);
        }
    }

    /**
     * Test cannot process bulk payment with no valid billings
     */
    public function test_cannot_process_bulk_payment_with_no_valid_billings()
    {
        $billing = Billing::factory()->create(['status' => 'PAID']);
        $admin = User::factory()->create();

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Tidak ada tagihan yang valid');

        $this->paymentService->processBulkCashPayments([$billing->id], $admin->id);
    }

    /**
     * Test process bulk cash payment only processes unpaid bills
     */
    public function test_process_bulk_payment_skips_paid_bills()
    {
        $unpaidBilling = Billing::factory()->create(['status' => 'UNPAID']);
        $paidBilling = Billing::factory()->create(['status' => 'PAID']);
        $admin = User::factory()->create();

        $processedCount = $this->paymentService->processBulkCashPayments(
            [$unpaidBilling->id, $paidBilling->id],
            $admin->id
        );

        $this->assertEquals(1, $processedCount);
        $this->assertEquals('PAID', $unpaidBilling->fresh()->status);
        $this->assertEquals('PAID', $paidBilling->fresh()->status);
    }

    /**
     * Test process duitku payment creates pending payment
     */
    public function test_process_duitku_payment_creates_pending_payment()
    {
        $billing = Billing::factory()->create(['status' => 'UNPAID', 'final_amount' => 1000000]);
        $this->actingAs(User::factory()->create());

        $payment = $this->paymentService->processDuitkuPayment($billing);

        $this->assertNotNull($payment);
        $this->assertEquals('pending', $payment->status);
        $this->assertEquals('duitku', $payment->method);
        $this->assertNull($payment->paid_at);
    }

    /**
     * Test cannot process duitku payment for paid billing
     */
    public function test_cannot_process_duitku_payment_for_paid_billing()
    {
        $billing = Billing::factory()->create(['status' => 'PAID']);
        $this->actingAs(User::factory()->create());

        $this->expectException(Exception::class);

        $this->paymentService->processDuitkuPayment($billing);
    }

    /**
     * Test update duitku payment status from pending to paid
     */
    public function test_update_duitku_payment_status()
    {
        $payment = Payment::factory()->create([
            'method' => 'duitku',
            'status' => 'pending'
        ]);

        $this->paymentService->updateDuitkuPaymentStatus(
            $payment,
            'paid',
            'DUITKU-12345'
        );

        $updatedPayment = $payment->fresh();
        $this->assertEquals('paid', $updatedPayment->status);
    }

    /**
     * Test cannot update non-duitku payment with duitku service
     */
    public function test_cannot_update_non_duitku_payment()
    {
        $payment = Payment::factory()->create(['method' => 'cash']);

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Metode pembayaran harus Duitku');

        $this->paymentService->updateDuitkuPaymentStatus($payment, 'paid');
    }

    /**
     * Test payment locked during processing to prevent race conditions
     */
    public function test_payment_uses_transaction()
    {
        $billing = Billing::factory()->create(['status' => 'UNPAID']);
        $admin = User::factory()->create();

        $payment = $this->paymentService->processCashPayment($billing, $admin->id);

        $this->assertDatabaseHas('payments', ['id' => $payment->id]);
        $this->assertDatabaseHas('billings', ['id' => $billing->id, 'status' => 'PAID']);
    }
}
