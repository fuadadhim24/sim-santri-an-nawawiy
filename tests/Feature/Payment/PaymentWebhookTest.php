<?php

namespace Tests\Feature\Payment;

use Tests\TestCase;
use App\Models\Billing;
use App\Models\Payment;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PaymentWebhookTest extends TestCase
{
    use RefreshDatabase;

    public function test_valid_webhook_processes_payment()
    {
        $this->withoutExceptionHandling();
        $billing = Billing::factory()->create(['status' => 'UNPAID', 'final_amount' => 100000]);
        
        $merchantKey = config('payment.duitku.merchant_key') ?: 'test_key';
        $merchantCode = config('payment.duitku.merchant_code') ?: 'test_merchant';
        $merchantOrderId = $billing->payment_reference ?? 'ORDER-' . $billing->id;
        $amount = $billing->final_amount;
        
        $signature = md5($merchantCode . $amount . $merchantOrderId . $merchantKey);

        $response = $this->post(route('duitku.callback'), [
            'merchantCode' => $merchantCode,
            'merchantOrderId' => $merchantOrderId,
            'amount' => $amount,
            'reference' => 'REF-123',
            'resultCode' => '00',
            'paymentCode' => 'VA',
            'signature' => $signature,
        ]);

        $response->assertSuccessful();
        $this->assertTrue($billing->fresh()->isPaid());
    }

    public function test_invalid_webhook_rejected()
    {
        $billing = Billing::factory()->create(['status' => 'UNPAID', 'final_amount' => 100000]);
        $merchantCode = config('payment.duitku.merchant_code') ?: 'test_merchant';

        $response = $this->post(route('duitku.callback'), [
            'merchantCode' => $merchantCode,
            'merchantOrderId' => 'ORDER-' . $billing->id,
            'amount' => $billing->final_amount,
            'reference' => 'REF-123',
            'resultCode' => '00',
            'signature' => 'invalid_signature',
        ]);

        $response->assertStatus(422);
        $this->assertFalse($billing->fresh()->isPaid());
    }

    public function test_duplicate_webhook_idempotent()
    {
        $billing = Billing::factory()->create(['status' => 'UNPAID', 'final_amount' => 100000]);
        $reference = 'REF-123';
        
        $merchantKey = config('payment.duitku.merchant_key') ?: 'test_key';
        $merchantCode = config('payment.duitku.merchant_code') ?: 'test_merchant';
        $merchantOrderId = $billing->payment_reference ?? 'ORDER-' . $billing->id;
        $signature = md5($merchantCode . $billing->final_amount . $merchantOrderId . $merchantKey);

        $payloadData = [
            'merchantCode' => $merchantCode,
            'merchantOrderId' => $merchantOrderId,
            'amount' => $billing->final_amount,
            'reference' => $reference,
            'resultCode' => '00',
            'paymentCode' => 'VA',
            'signature' => $signature,
        ];

        $this->post(route('duitku.callback'), $payloadData);

        $initialPaymentCount = Payment::where('duitku_reference', $reference)->count();
        $this->assertEquals(1, $initialPaymentCount);

        $this->post(route('duitku.callback'), $payloadData);

        $finalPaymentCount = Payment::where('duitku_reference', $reference)->count();
        $this->assertEquals(1, $finalPaymentCount);
    }

    public function test_webhook_missing_required_fields()
    {
        $merchantCode = config('payment.duitku.merchant_code') ?: 'test_merchant';
        $response = $this->post(route('duitku.callback'), [
            'merchantCode' => $merchantCode,
            'merchantOrderId' => 'ORDER-123',
            // Missing amount, reference, resultCode
        ]);

        $response->assertStatus(422);
    }

    public function test_webhook_with_failed_result_code()
    {
        $billing = Billing::factory()->create(['status' => 'UNPAID', 'final_amount' => 100000]);
        
        $merchantKey = config('payment.duitku.merchant_key') ?: 'test_key';
        $merchantCode = config('payment.duitku.merchant_code') ?: 'test_merchant';
        $merchantOrderId = 'ORDER-' . $billing->id;
        $signature = md5($merchantCode . $billing->final_amount . $merchantOrderId . $merchantKey);

        $response = $this->post(route('duitku.callback'), [
            'merchantCode' => $merchantCode,
            'merchantOrderId' => $merchantOrderId,
            'amount' => $billing->final_amount,
            'reference' => 'REF-123',
            'resultCode' => '01',  // Failed code
            'signature' => $signature,
        ]);

        $response->assertStatus(422);
        $this->assertFalse($billing->fresh()->isPaid());
    }
}
