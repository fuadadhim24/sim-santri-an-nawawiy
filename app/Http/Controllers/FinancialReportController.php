<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use Illuminate\Http\Request;

class FinancialReportController extends Controller
{
    public function print(Request $request)
    {
        $startDate = $request->input('startDate');
        $endDate = $request->input('endDate');
        $search = $request->input('search');

        $query = Payment::where('status', 'paid');

        if ($startDate) {
            $query->whereDate('paid_at', '>=', $startDate);
        }

        if ($endDate) {
            $query->whereDate('paid_at', '<=', $endDate);
        }

        if ($search) {
            $query->whereHas('billing.student', function($q) use ($search) {
                $q->where('full_name', 'like', '%' . $search . '%')
                  ->orWhere('nis', 'like', '%' . $search . '%');
            });
        }

        $payments = $query->with(['billing.student'])->orderBy('paid_at', 'asc')->get();

        $cashPayments = $payments->where('method', 'cash');
        $cashlessPayments = $payments->where('method', 'duitku');

        $totalCash = $cashPayments->sum('amount');
        $totalCashless = $cashlessPayments->sum('amount');
        $totalIncome = $payments->sum('amount');

        return view('admin.reports.financial-print', compact(
            'cashPayments',
            'cashlessPayments',
            'totalCash',
            'totalCashless',
            'totalIncome',
            'startDate',
            'endDate'
        ));
    }
}
