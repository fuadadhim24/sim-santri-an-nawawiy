<?php

namespace App\Http\Controllers;

use App\Models\Billing;
use Illuminate\Http\Request;

class ReceiptController extends Controller
{
    public function show($id)
    {
        $user = auth()->user();
        $billing = Billing::with(['student', 'payments'])->findOrFail($id);

        // Security check for Guardians
        if ($user->role === 'WALI_SANTRI') {
            $guardian = \App\Models\Guardian::where('user_id', $user->id)->first();
            if (!$guardian || !$guardian->students->contains($billing->student_id)) {
                abort(403, 'Anda tidak memiliki akses ke kwitansi ini.');
            }
        }

        return view('receipt', compact('billing'));
    }
}
