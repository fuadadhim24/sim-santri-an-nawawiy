<?php

namespace Tests\Feature;

use App\Models\Billing;
use App\Models\User;
use App\Services\SecurePaymentService;
use Exception;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PaymentSecurityTest extends TestCase
{
    use RefreshDatabase;

    private SecurePaymentService $paymentService;
    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->paymentService = new SecurePaymentService();
        $this->admin = User::factory()->create(['role' => 'ADMINISTRASI']);
    }

    /**
     * NEGATIVE TEST: Prevent double payment via race condition
     */
    public function test_prevent_double_payment_race_condition()
    {
        $billing = Billing::factory()->create([
            'status' => 'UNPAID',
            'final_amount' => 1000000,
        ]);

        // First payment succeeds
        $payment1 = $this->paymentService->processCashPaymentSecurely(
            $billing,
            $this->admin->id
        );

        $this->assertEquals('paid', $payment1->status);
        $this->assertEquals('PAID', $billing->fresh()->status);

        // Try second payment - should fail
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('sudah tidak dapat dibayar');

        $this->paymentService->processCashPaymentSecurely(
            $billing->fresh(),
            $this->admin->id
        );
    }

    /**
     * NEGATIVE TEST: Prevent pending payment duplicate
     */
    public function test_prevent_pending_payment_duplicate()
    {
        $billing = Billing::factory()->create(['status' => 'UNPAID']);

        // Create a pending payment manually
        $pendingPayment = \App\Models\Payment::factory()->create([
            'billing_id' => $billing->id,
            'status' => 'pending',
        ]);

        // Try to create another payment for same billing
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('pembayaran pending');

        $this->paymentService->processCashPaymentSecurely(
            $billing,
            $this->admin->id
        );
    }

    /**
     * NEGATIVE TEST: Reject payment amount mismatch
     */
    public function test_reject_duitku_callback_amount_mismatch()
    {
        $billing = Billing::factory()->create([
            'status' => 'UNPAID',
            'final_amount' => 1000000,
            'payment_reference' => 'ORDER-123',
        ]);

        $merchantKey = config('payment.duitku.merchant_key') ?: 'test_key';
        $merchantCode = config('payment.duitku.merchant_code') ?: 'test_merchant';
        $merchantOrderId = 'ORDER-123';
        $amount = 1.00;
        $signature = md5($merchantCode . $amount . $merchantOrderId . $merchantKey);

        // Malicious callback with wrong amount
        $fraudCallback = [
            'merchantCode' => $merchantCode,
            'merchantOrderId' => $merchantOrderId,
            'amount' => $amount,
            'reference' => 'DUITKU-FRAUD',
            'resultCode' => '0000',
            'signature' => $signature,
        ];

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('tidak sesuai');

        $this->paymentService->validateDuitkuCallback($fraudCallback);
    }

    /**
     * NEGATIVE TEST: Reject payment for already-paid billing
     */
    public function test_reject_payment_for_paid_billing()
    {
        $billing = Billing::factory()->create([
            'status' => 'PAID',
            'final_amount' => 1000000,
            'payment_reference' => 'ORDER-456',
        ]);

        $merchantKey = config('payment.duitku.merchant_key') ?: 'test_key';
        $merchantCode = config('payment.duitku.merchant_code') ?: 'test_merchant';
        $merchantOrderId = 'ORDER-456';
        $amount = 1000000;
        $signature = md5($merchantCode . $amount . $merchantOrderId . $merchantKey);

        $callback = [
            'merchantCode' => $merchantCode,
            'merchantOrderId' => $merchantOrderId,
            'amount' => $amount,
            'reference' => 'DUITKU-456',
            'resultCode' => '0000',
            'signature' => $signature,
        ];

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('sudah dibayar');

        $this->paymentService->validateDuitkuCallback($callback);
    }

    /**
     * NEGATIVE TEST: Reject payment for cancelled billing
     */
    public function test_reject_payment_for_cancelled_billing()
    {
        $billing = Billing::factory()->create([
            'status' => 'CANCELLED',
            'final_amount' => 1000000,
            'payment_reference' => 'ORDER-789',
        ]);

        $merchantKey = config('payment.duitku.merchant_key') ?: 'test_key';
        $merchantCode = config('payment.duitku.merchant_code') ?: 'test_merchant';
        $merchantOrderId = 'ORDER-789';
        $amount = 1000000;
        $signature = md5($merchantCode . $amount . $merchantOrderId . $merchantKey);

        $callback = [
            'merchantCode' => $merchantCode,
            'merchantOrderId' => $merchantOrderId,
            'amount' => $amount,
            'reference' => 'DUITKU-789',
            'resultCode' => '0000',
            'signature' => $signature,
        ];

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('telah dibatalkan');

        $this->paymentService->validateDuitkuCallback($callback);
    }

    /**
     * POSITIVE TEST: Accept payment for overdue billing (tagihan terlambat tetap bisa dibayar)
     */
    public function test_accept_payment_for_overdue_billing()
    {
        $billing = Billing::factory()->create([
            'status' => 'UNPAID',
            'final_amount' => 1000000,
            'payment_reference' => 'ORDER-EXP',
            'expires_at' => now()->subDay(), // Past due date
        ]);

        $merchantKey = config('payment.duitku.merchant_key') ?: 'test_key';
        $merchantCode = config('payment.duitku.merchant_code') ?: 'test_merchant';
        $merchantOrderId = 'ORDER-EXP';
        $amount = 1000000;
        $signature = md5($merchantCode . $amount . $merchantOrderId . $merchantKey);

        $callback = [
            'merchantCode' => $merchantCode,
            'merchantOrderId' => $merchantOrderId,
            'amount' => $amount,
            'reference' => 'DUITKU-EXP',
            'resultCode' => '0000',
            'signature' => $signature,
        ];

        // Overdue billings should still be accepted — no exception thrown
        $result = $this->paymentService->validateDuitkuCallback($callback);

        $this->assertNotNull($result);
        $this->assertEquals($billing->id, $result['billing']->id);
    }

    /**
     * NEGATIVE TEST: Prevent access to non-existent billing
     */
    public function test_prevent_payment_for_nonexistent_billing()
    {
        $merchantKey = config('payment.duitku.merchant_key') ?: 'test_key';
        $merchantCode = config('payment.duitku.merchant_code') ?: 'test_merchant';
        $merchantOrderId = 'ORDER-FAKE-999999';
        $amount = 1000000;
        $signature = md5($merchantCode . $amount . $merchantOrderId . $merchantKey);

        $callback = [
            'merchantCode' => $merchantCode,
            'merchantOrderId' => $merchantOrderId,
            'amount' => $amount,
            'reference' => 'DUITKU-FAKE',
            'resultCode' => '0000',
            'signature' => $signature,
        ];

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('tidak ditemukan');

        $this->paymentService->validateDuitkuCallback($callback);
    }

    /**
     * NEGATIVE TEST: Snapshot amount matches billing at payment time
     */
    public function test_payment_snapshot_prevents_price_manipulation()
    {
        $billing = Billing::factory()->create([
            'status' => 'UNPAID',
            'final_amount' => 1000000,
        ]);

        // Process payment with snapshot
        $payment = $this->paymentService->processCashPaymentSecurely(
            $billing,
            $this->admin->id
        );

        // Payment snapshot should match billing amount
        $this->assertEquals(
            $billing->final_amount,
            $payment->snapshot_billing_amount
        );
    }
}
