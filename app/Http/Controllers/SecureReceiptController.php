<?php

namespace App\Http\Controllers;

use App\Models\Billing;
use App\Policies\BillingPolicy;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Auth;

class ReceiptController extends Controller
{
    use AuthorizesRequests;

    /**
     * SECURE: Show billing receipt with IDOR protection
     */
    public function show(Billing $billing)
    {
        // 1. AUTHORIZE - Check if user can view this billing
        $this->authorize('view', $billing);

        // 2. VERIFY ownership (extra layer of security)
        if (Auth::user()->role === 'WALI_SANTRI') {
            abort_unless(
                $billing->student->guardian_id === Auth::user()->guardian?->id,
                403,
                'Anda tidak memiliki akses untuk melihat kwitansi ini.'
            );
        }

        // 3. Log access untuk audit trail
        \Illuminate\Support\Facades\Log::info('Receipt viewed', [
            'billing_id' => $billing->id,
            'user_id' => Auth::id(),
            'user_role' => Auth::user()->role,
        ]);

        return view('receipts.show', ['billing' => $billing]);
    }

    /**
     * SECURE: Print receipt with same protections
     */
    public function print(Billing $billing)
    {
        // 1. AUTHORIZE
        $this->authorize('view', $billing);

        // 2. VERIFY
        if (Auth::user()->role === 'WALI_SANTRI') {
            abort_unless(
                $billing->student->guardian_id === Auth::user()->guardian?->id,
                403
            );
        }

        return response()->view('receipts.print', ['billing' => $billing])
            ->header('Content-Type', 'application/pdf');
    }
}
