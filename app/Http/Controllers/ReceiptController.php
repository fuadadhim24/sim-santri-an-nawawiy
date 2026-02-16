<?php

namespace App\Http\Controllers;

use App\Models\Billing;
use Illuminate\Http\Request;

class ReceiptController extends Controller
{
    public function show($id)
    {
        $billing = Billing::with('student')->findOrFail($id);
        return view('receipt', compact('billing'));
    }
}
