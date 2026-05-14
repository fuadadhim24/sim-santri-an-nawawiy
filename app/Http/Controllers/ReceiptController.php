<?php

namespace App\Http\Controllers;

use App\Models\Billing;
use Illuminate\Http\Request;

class ReceiptController extends Controller
{
    public function show(Billing $billing)
    {
        $this->authorize('view', $billing);

        $billing = $billing->load(['student', 'payments']);

        return view('receipt', compact('billing'));
    }
}
