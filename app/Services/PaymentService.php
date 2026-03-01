<?php

namespace App\Services;

use App\Models\Billing;
use App\Models\Payment;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Exception;

class PaymentService
{
    public function processCashPayment(Billing $billing, int $adminId, ?string $notes = null): Payment
    {
        return DB::transaction(function () use ($billing, $adminId, $notes) {
            $billing = Billing::lockForUpdate()->find($billing->id);

            if (!$billing || $billing->status !== 'UNPAID') {
                throw new Exception('Tagihan sudah tidak dapat dibayar. Status: ' . ($billing->status ?? 'NOT_FOUND'));
            }

            $payment = Payment::create([
                'billing_id' => $billing->id,
                'admin_id' => $adminId,
                'method' => 'cash',
                'amount' => $billing->final_amount,
                'status' => 'paid',
                'notes' => $notes,
                'paid_at' => now(),
            ]);

            $billing->update(['status' => 'PAID']);

            return $payment;
        });
    }

    public function processBulkCashPayments(array $billingIds, int $adminId, ?string $notes = null): int
    {
        $processedCount = 0;

        DB::transaction(function () use ($billingIds, $adminId, $notes, &$processedCount) {
            $billings = Billing::whereIn('id', $billingIds)
                ->where('status', 'UNPAID')
                ->get();

            if ($billings->isEmpty()) {
                throw new Exception('Tidak ada tagihan yang valid untuk dibayaran massal.');
            }

            foreach ($billings as $billing) {
                Payment::create([
                    'billing_id' => $billing->id,
                    'admin_id' => $adminId,
                    'method' => 'cash',
                    'amount' => $billing->final_amount,
                    'status' => 'paid',
                    'notes' => $notes,
                    'paid_at' => now(),
                ]);

                $billing->update(['status' => 'PAID']);
                $processedCount++;
            }
        });

        Log::info('Bulk cash payment processed', [
            'admin_id' => $adminId,
            'billing_ids' => $billingIds,
            'count' => $processedCount,
            'notes' => $notes,
        ]);

        return $processedCount;
    }

    public function processDuitkuPayment(Billing $billing): Payment
    {
        if ($billing->status !== 'UNPAID') {
            throw new Exception('Tagihan sudah tidak dapat dibayar. Status: ' . $billing->status);
        }

        $payment = Payment::create([
            'billing_id' => $billing->id,
            'admin_id' => auth()->id(),
            'method' => 'duitku',
            'amount' => $billing->final_amount,
            'status' => 'pending',
            'paid_at' => null,
        ]);

        return $payment;
    }

    public function updateDuitkuPaymentStatus(Payment $payment, string $status, ?string $reference = null): void
    {
        if ($payment->method !== 'duitku') {
            throw new Exception('Metode pembayaran harus Duitku.');
        }

        if ($status === 'paid') {
            $payment->markAsPaidByDuitku($reference);
            $payment->billing->update(['status' => 'PAID']);
        } elseif ($status === 'failed') {
            $payment->markAsFailedByDuitku($reference);
        }

        Log::info('Duitku payment status updated', [
            'payment_id' => $payment->id,
            'status' => $status,
            'reference' => $reference,
        ]);
    }
}
