<?php

namespace App\Livewire;

use App\Models\Billing;
use Livewire\Component;

class FinancialReport extends Component
{
    public $startDate;
    public $endDate;

    public function mount()
    {
        // Default: Current Month
        $this->startDate = date('Y-m-01');
        $this->endDate = date('Y-m-t');
    }

    public function render()
    {
        $query = Billing::where('status', 'PAID');

        if ($this->startDate) {
            $query->whereDate('updated_at', '>=', $this->startDate); // Use updated_at for payment time
        }

        if ($this->endDate) {
            $query->whereDate('updated_at', '<=', $this->endDate);
        }

        $paidBills = $query->with('student')->orderBy('updated_at', 'desc')->get();

        $totalIncome = $paidBills->sum('final_amount');
        $totalTransactions = $paidBills->count();

        return view('livewire.financial-report', [
            'paidBills' => $paidBills,
            'totalIncome' => $totalIncome,
            'totalTransactions' => $totalTransactions
        ])->layout('layouts.admin');
    }
}
