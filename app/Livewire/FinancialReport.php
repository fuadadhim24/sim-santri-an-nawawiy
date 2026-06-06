<?php

namespace App\Livewire;

use App\Models\Billing;
use Livewire\Component;
use Livewire\WithPagination;

class FinancialReport extends Component
{
    use WithPagination;

    public $startDate;
    public $endDate;
    public $search = '';

    protected $queryString = [
        'search' => ['except' => ''],
        'startDate' => ['except' => ''],
        'endDate' => ['except' => ''],
    ];

    public function mount()
    {
        $this->startDate = date('Y-m-01');
        $this->endDate = date('Y-m-t');
    }

    public function updated($property)
    {
        if (in_array($property, ['search', 'startDate', 'endDate'])) {
            $this->resetPage();
        }
    }

    public function render()
    {
        $query = \App\Models\Payment::where('status', 'paid');

        if ($this->startDate) {
            $query->whereDate('paid_at', '>=', $this->startDate);
        }

        if ($this->endDate) {
            $query->whereDate('paid_at', '<=', $this->endDate);
        }

        if ($this->search) {
            $query->whereHas('billing.student', function($q) {
                $q->where('full_name', 'like', '%' . $this->search . '%')
                  ->orWhere('nis', 'like', '%' . $this->search . '%');
            });
        }

        $payments = $query->with(['billing.student'])->orderBy('paid_at', 'desc')->paginate(10);

        $statsQuery = clone $query;
        $totalIncome = (int) $statsQuery->sum('amount');
        $totalTransactions = $statsQuery->count();

        return view('livewire.financial-report', [
            'payments' => $payments,
            'totalIncome' => $totalIncome,
            'totalTransactions' => $totalTransactions
        ])->layout('layouts.admin');
    }
}
