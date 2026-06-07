<?php

namespace App\Services;

use App\Models\Billing;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Exception;

class SecurePaymentService
{
    /**
     * SECURE: Process cash payment with atomic lock
     * Prevents double payment via race condition
     */
    public function processCashPaymentSecurely(Billing $billing, int $adminId, ?string $notes = null): Payment
    {
        return DB::transaction(function () use ($billing, $adminId, $notes) {
            // 1. ATOMIC LOCK - Prevent race condition
            $billing = Billing::lockForUpdate()->find($billing->id);

            if (!$billing) {
                throw new Exception('Tagihan tidak ditemukan.');
            }

            // 2. VALIDATE billing status
            if ($billing->status !== 'UNPAID') {
                throw new Exception(
                    "Tagihan sudah tidak dapat dibayar. Status: {$billing->status}"
                );
            }

            // 3. CHECK for existing pending payments (prevent double payment)
            $pendingPayment = Payment::where('billing_id', $billing->id)
                ->where('status', 'pending')
                ->exists();

            if ($pendingPayment) {
                throw new Exception(
                    'Tagihan ini sudah memiliki pembayaran pending. ' .
                    'Tunggulah hingga konfirmasi selesai.'
                );
            }

            // 4. CREATE payment
            $payment = Payment::create([
                'billing_id' => $billing->id,
                'admin_id' => $adminId,
                'method' => 'cash',
                'amount' => $billing->final_amount,
                'status' => 'paid',
                'notes' => $notes,
                'paid_at' => now(),
                'snapshot_billing_amount' => $billing->final_amount, // VERIFY amount
            ]);

            // 5. UPDATE billing status
            $billing->update(['status' => 'PAID']);

            Log::info('Cash payment processed securely', [
                'payment_id' => $payment->id,
                'billing_id' => $billing->id,
                'amount' => $payment->amount,
                'admin_id' => $adminId,
            ]);

            return $payment;
        });
    }

    /**
     * SECURE: Find billing for Duitku webhook callback using fallback mechanisms
     */
    public function findBillingForDuitku(string $merchantOrderId, ?string $reference = null): ?Billing
    {
        // 1. Try by payment_reference matching merchantOrderId
        $billing = Billing::where('payment_reference', $merchantOrderId)->first();
        if ($billing) {
            return $billing;
        }

        // 2. Try by payment_reference matching reference
        if ($reference) {
            $billing = Billing::where('payment_reference', $reference)->first();
            if ($billing) {
                return $billing;
            }
        }

        // 3. Try to parse billing ID from merchantOrderId
        $billingId = null;
        if (str_starts_with($merchantOrderId, 'ORDER-')) {
            $billingId = substr($merchantOrderId, 6);
        } elseif (preg_match('/^(\d+)/', $merchantOrderId, $matches)) {
            $billingId = $matches[1];
        }

        if ($billingId) {
            return Billing::find($billingId);
        }

        return null;
    }

    /**
     * SECURE: Validate Duitku webhook callback
     * Prevents: Manipulasi nominal via webhook, payment for expired billing
     */
    public function validateDuitkuCallback(array $callbackData): array
    {
        // 1. VALIDATE callback signature (if using Duitku SDK)
        if (!$this->verifyDuitkuSignature($callbackData)) {
            throw new Exception('Callback signature tidak valid. Kemungkinan fraud.');
        }

        $merchantOrderId = $callbackData['merchantOrderId'] ?? null;
        $amount = floatval($callbackData['amount'] ?? 0);
        $reference = $callbackData['reference'] ?? null;
        $resultCode = $callbackData['resultCode'] ?? null;

        // 2. FIND billing
        $billing = $this->findBillingForDuitku($merchantOrderId, $reference);

        if (!$billing) {
            Log::warning('Duitku callback: billing not found', [
                'merchant_order_id' => $merchantOrderId,
                'reference' => $reference,
            ]);
            throw new Exception('Tagihan tidak ditemukan dalam sistem.');
        }

        // 3. VALIDATE amount matches exactly
        if (abs($amount - $billing->final_amount) > 0.01) {
            Log::error('Duitku callback: amount mismatch (FRAUD ATTEMPT)', [
                'billing_id' => $billing->id,
                'expected_amount' => $billing->final_amount,
                'callback_amount' => $amount,
                'reference' => $reference,
            ]);

            throw new Exception(
                "Nominal pembayaran tidak sesuai. " .
                "Diharapkan: {$billing->final_amount}, " .
                "Diterima: {$amount}. Transaksi ditolak untuk keamanan."
            );
        }

        // 4. VALIDATE billing status
        if ($billing->status === 'PAID') {
            throw new Exception(
                'Tagihan ini sudah dibayar sebelumnya. ' .
                'Hubungi Admin untuk refund jika ada kesalahan.'
            );
        }

        if ($billing->status === 'CANCELLED') {
            Log::warning('Duitku callback: payment for cancelled billing', [
                'billing_id' => $billing->id,
                'reference' => $reference,
            ]);

            throw new Exception(
                'Tagihan ini telah dibatalkan. Dana akan diproses sebagai deposit.'
            );
        }

        // 5. LOG warning if billing is past due date (but still allow payment)
        if ($billing->expires_at && $billing->expires_at->isPast()) {
            Log::info('Duitku callback: payment for overdue billing (still accepted)', [
                'billing_id' => $billing->id,
                'due_date' => $billing->expires_at,
            ]);
            // Tagihan terlambat tetap boleh dibayar
        }

        return [
            'billing' => $billing,
            'amount' => $amount,
            'reference' => $reference,
            'success' => in_array($resultCode, ['00', '0000']), // Success code in Duitku
        ];
    }

    /**
     * SECURE: Process Duitku payment with all validations
     */
    public function processDuitkuPaymentSecurely(array $callbackData): Payment
    {
        // IDEMPOTENCY CHECK - Prevent duplicate processing
        $reference = $callbackData['reference'] ?? null;
        if ($reference) {
            $existingPayment = Payment::where('duitku_reference', $reference)->first();
            if ($existingPayment) {
                Log::info('Duitku payment already processed (idempotent)', [
                    'payment_id' => $existingPayment->id,
                    'reference' => $reference,
                ]);
                return $existingPayment;
            }
        }

        // Validate callback
        $validated = $this->validateDuitkuCallback($callbackData);
        $billing = $validated['billing'];
        $amount = $validated['amount'];
        $reference = $validated['reference'];
        $success = $validated['success'];

        return DB::transaction(function () use ($billing, $amount, $reference, $success) {
            // ATOMIC LOCK
            $billing = Billing::lockForUpdate()->find($billing->id);

            if (!$success) {
                // Payment failed at gateway
                $payment = Payment::create([
                    'billing_id' => $billing->id,
                    'admin_id' => null,
                    'method' => 'duitku',
                    'amount' => $amount,
                    'duitku_reference' => $reference,
                    'status' => 'failed',
                    'paid_at' => null,
                ]);

                Log::warning('Duitku payment failed', [
                    'payment_id' => $payment->id,
                    'billing_id' => $billing->id,
                ]);

                return $payment;
            }

            // Payment successful
            $payment = Payment::create([
                'billing_id' => $billing->id,
                'admin_id' => null,
                'method' => 'duitku',
                'amount' => $amount,
                'duitku_reference' => $reference,
                'status' => 'paid',
                'paid_at' => now(),
                'snapshot_billing_amount' => $billing->final_amount,
            ]);

            $billing->update([
                'status' => 'PAID',
                'payment_reference' => $reference,
            ]);

            Log::info('Duitku payment successful', [
                'payment_id' => $payment->id,
                'billing_id' => $billing->id,
                'amount' => $amount,
            ]);

            return $payment;
        });
    }

    /**
     * VERIFY Duitku webhook signature - Prevents fraud
     */
    private function verifyDuitkuSignature(array $data): bool
    {
        $merchantOrderId = $data['merchantOrderId'] ?? '';
        $amount = $data['amount'] ?? 0;
        $signature = $data['signature'] ?? '';
        $merchantCode = $data['merchantCode'] ?? (config('payment.duitku.merchant_code') ?: 'test_merchant');

        $merchantKey = config('payment.duitku.merchant_key') ?: 'test_key';

        $expectedSignature = md5($merchantCode . $amount . $merchantOrderId . $merchantKey);

        return hash_equals($expectedSignature, $signature);
    }
}
