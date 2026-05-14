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

        $payment = $this->service->processCashPaymentSecurely([
            'billing_id' => $billing->id,
            'amount' => $billing->final_amount,
            'method' => 'TUNAI',
        ]);

        $this->assertNotNull($payment);
        $this->assertTrue($billing->fresh()->isPaid());
    }

    public function test_duplicate_payment_prevented()
    {
        $billing = Billing::factory()->create(['status' => 'UNPAID', 'final_amount' => 100000]);
        $reference = 'REF-001';

        $payment1 = $this->service->processDuitkuPaymentSecurely([
            'billingId' => $billing->id,
            'amount' => $billing->final_amount,
            'reference' => $reference,
            'resultCode' => '00',
        ]);

        $payment2 = $this->service->processDuitkuPaymentSecurely([
            'billingId' => $billing->id,
            'amount' => $billing->final_amount,
            'reference' => $reference,
            'resultCode' => '00',
        ]);

        $this->assertEquals($payment1->id, $payment2->id);
    }

    public function test_webhook_signature_verified()
    {
        $merchantKey = config('payment.duitku.merchant_key', 'test_key');
        $merchantOrderId = 'ORDER-123';
        $amount = 100000;

        $signature = md5($merchantKey . $merchantOrderId . $amount);

        $reflection = new \ReflectionClass($this->service);
        $method = $reflection->getMethod('verifyDuitkuSignature');
        $method->setAccessible(true);

        $result = $method->invoke($this->service, [
            'merchantOrderId' => $merchantOrderId,
            'amount' => $amount,
        ], $signature);

        $this->assertTrue($result);
    }

    public function test_webhook_signature_invalid()
    {
        $reflection = new \ReflectionClass($this->service);
        $method = $reflection->getMethod('verifyDuitkuSignature');
        $method->setAccessible(true);

        $result = $method->invoke($this->service, [
            'merchantOrderId' => 'ORDER-123',
            'amount' => 100000,
        ], 'invalid_signature');

        $this->assertFalse($result);
    }

    public function test_duitku_payment_idempotent()
    {
        $billing = Billing::factory()->create([
            'status' => 'UNPAID',
            'final_amount' => 100000
        ]);

        $reference = 'DUITKU-REF-001';

        $payment1 = $this->service->processDuitkuPaymentSecurely([
            'billingId' => $billing->id,
            'amount' => $billing->final_amount,
            'reference' => $reference,
            'resultCode' => '00',
        ]);

        $payment2 = $this->service->processDuitkuPaymentSecurely([
            'billingId' => $billing->id,
            'amount' => $billing->final_amount,
            'reference' => $reference,
            'resultCode' => '00',
        ]);

        $this->assertEquals($payment1->id, $payment2->id);
        $this->assertEquals(1, Payment::where('duitku_reference', $reference)->count());
    }
}
