<?php

namespace App\Observers;

use App\Models\Payment;
use App\Services\AuditService;
use Illuminate\Support\Facades\Auth;

class PaymentObserver
{
    public function created(Payment $payment): void
    {
        AuditService::log(
            'payment_created',
            $payment,
            [],
            [
                'amount' => $payment->amount,
                'method' => $payment->method,
                'status' => $payment->status,
                'billing_id' => $payment->billing_id,
                'admin_id' => $payment->admin_id,
            ],
            'Pembayaran #' . $payment->id . ' berhasil dibuat oleh ' . (Auth::user()?->name ?? 'System')
        );
    }
}
