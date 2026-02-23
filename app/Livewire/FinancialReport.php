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
        $query = Billing::where('status', 'PAID');

        if ($this->startDate) {
            $query->whereDate('updated_at', '>=', $this->startDate);
        }

        if ($this->endDate) {
            $query->whereDate('updated_at', '<=', $this->endDate);
        }

        if ($this->search) {
            $query->whereHas('student', function($q) {
                $q->where('full_name', 'like', '%' . $this->search . '%')
                  ->orWhere('nis', 'like', '%' . $this->search . '%');
            });
        }

        $paidBills = $query->with('student')->orderBy('updated_at', 'desc')->paginate(10);

        $statsQuery = clone $query;
        $totalIncome = $statsQuery->sum('final_amount');
        $totalTransactions = $statsQuery->count();

        return view('livewire.financial-report', [
            'paidBills' => $paidBills,
            'totalIncome' => $totalIncome,
            'totalTransactions' => $totalTransactions
        ])->layout('layouts.admin');
    }
}
