<?php

namespace Tests\Unit\Services;

use Tests\TestCase;
use App\Models\Billing;
use App\Models\Payment;
use App\Services\SecurePaymentService;
use Illuminate\Foundation\Testing\RefreshDatabase;

class SecurePaymentServiceTest extends TestCase
{
    use RefreshDatabase;

    protected $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new SecurePaymentService();
    }

    public function test_cash_payment_processed()
    {
        $billing = Billing::factory()->create(['status' => 'UNPAID', 'final_amount' => 100000]);
        $admin = \App\Models\User::factory()->create();

        $payment = $this->service->processCashPaymentSecurely(
            $billing,
            $admin->id,
            'Test cash payment'
        );

        $this->assertNotNull($payment);
        $this->assertTrue($billing->fresh()->isPaid());
    }

    public function test_duplicate_payment_prevented()
    {
        $billing = Billing::factory()->create(['status' => 'UNPAID', 'final_amount' => 100000]);
        $reference = 'REF-001';
        $merchantOrderId = 'ORDER-' . $billing->id;
        $merchantKey = config('payment.duitku.merchant_key') ?: 'test_key';
        $merchantCode = config('payment.duitku.merchant_code') ?: 'test_merchant';

        // Update payment_reference in billing so we can find it
        $billing->update(['payment_reference' => $reference]);

        $signature = md5($merchantCode . $billing->final_amount . $merchantOrderId . $merchantKey);

        $payment1 = $this->service->processDuitkuPaymentSecurely([
            'merchantCode' => $merchantCode,
            'merchantOrderId' => $merchantOrderId,
            'amount' => $billing->final_amount,
            'reference' => $reference,
            'resultCode' => '00',
            'signature' => $signature,
        ]);

        $payment2 = $this->service->processDuitkuPaymentSecurely([
            'merchantCode' => $merchantCode,
            'merchantOrderId' => $merchantOrderId,
            'amount' => $billing->final_amount,
            'reference' => $reference,
            'resultCode' => '00',
            'signature' => $signature,
        ]);

        $this->assertEquals($payment1->id, $payment2->id);
    }

    public function test_webhook_signature_verified()
    {
        $merchantKey = config('payment.duitku.merchant_key') ?: 'test_key';
        $merchantCode = config('payment.duitku.merchant_code') ?: 'test_merchant';
        $merchantOrderId = 'ORDER-123';
        $amount = 100000;

        $signature = md5($merchantCode . $amount . $merchantOrderId . $merchantKey);

        $reflection = new \ReflectionClass($this->service);
        $method = $reflection->getMethod('verifyDuitkuSignature');
        $method->setAccessible(true);

        $result = $method->invoke($this->service, [
            'merchantCode' => $merchantCode,
            'merchantOrderId' => $merchantOrderId,
            'amount' => $amount,
            'signature' => $signature,
        ]);

        $this->assertTrue($result);
    }

    public function test_webhook_signature_invalid()
    {
        $reflection = new \ReflectionClass($this->service);
        $method = $reflection->getMethod('verifyDuitkuSignature');
        $method->setAccessible(true);

        $result = $method->invoke($this->service, [
            'merchantCode' => 'test_merchant',
            'merchantOrderId' => 'ORDER-123',
            'amount' => 100000,
            'signature' => 'invalid_signature',
        ]);

        $this->assertFalse($result);
    }

    public function test_duitku_payment_idempotent()
    {
        $billing = Billing::factory()->create([
            'status' => 'UNPAID',
            'final_amount' => 100000
        ]);

        $reference = 'DUITKU-REF-001';
        $merchantOrderId = 'ORDER-' . $billing->id;
        $merchantKey = config('payment.duitku.merchant_key') ?: 'test_key';
        $merchantCode = config('payment.duitku.merchant_code') ?: 'test_merchant';

        // Update payment_reference in billing so we can find it
        $billing->update(['payment_reference' => $reference]);

        $signature = md5($merchantCode . $billing->final_amount . $merchantOrderId . $merchantKey);

        $payment1 = $this->service->processDuitkuPaymentSecurely([
            'merchantCode' => $merchantCode,
            'merchantOrderId' => $merchantOrderId,
            'amount' => $billing->final_amount,
            'reference' => $reference,
            'resultCode' => '00',
            'signature' => $signature,
        ]);

        $payment2 = $this->service->processDuitkuPaymentSecurely([
            'merchantCode' => $merchantCode,
            'merchantOrderId' => $merchantOrderId,
            'amount' => $billing->final_amount,
            'reference' => $reference,
            'resultCode' => '00',
            'signature' => $signature,
        ]);

        $this->assertEquals($payment1->id, $payment2->id);
        $this->assertEquals(1, Payment::where('duitku_reference', $reference)->count());
    }
}
