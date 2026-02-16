<?php

namespace App\Livewire;

use App\Models\Billing;
use Livewire\Component;
use Livewire\WithPagination;

class BillingIndex extends Component
{
    use WithPagination;

    public $search = '';
    public $statusFilter = '';

    public function cancelPayment($billingId)
    {
        $bill = \App\Models\Billing::find($billingId);

        // Only SUPER_ADMIN can cancel/void payments
        if (\Illuminate\Support\Facades\Auth::user()->role !== 'SUPER_ADMIN') {
             return;
        }

        if ($bill && $bill->status == 'PAID') {
            $bill->update(['status' => 'UNPAID']);
            session()->flash('message', 'Payment cancelled. Invoice ' . $bill->title . ' reverted to UNPAID.');
        }
    }

    public function render()
    {
        $query = \App\Models\Billing::with('student');

        if ($this->search) {
            $query->whereHas('student', function ($q) {
                $q->where('full_name', 'like', '%' . $this->search . '%')
                  ->orWhere('nis', 'like', '%' . $this->search . '%');
            });
        }

        if ($this->statusFilter) {
            $query->where('status', $this->statusFilter);
        }

        $billings = $query->orderBy('created_at', 'desc')->paginate(10);

        return view('livewire.billing-index', [
            'billings' => $billings
        ])->layout('layouts.admin');
    }
}
