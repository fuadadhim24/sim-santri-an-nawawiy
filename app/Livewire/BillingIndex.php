<?php

namespace App\Livewire;

use App\Models\Billing;
use App\Services\PaymentService;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class BillingIndex extends Component
{
    use WithPagination;

    public $search = '';
    public $statusFilter = '';
    public $showPaymentModal = false;
    public $selectedBilling = null;

    protected $listeners = ['openPaymentModal'];

    public function openPaymentModal($billingId)
    {
        $this->selectedBilling = Billing::with('student')->find($billingId);
        $this->showPaymentModal = true;
    }

    public function processCashPayment($billingId)
    {
        if (!Auth::check() || !in_array(Auth::user()->role, ['ADMIN', 'SUPER_ADMIN'])) {
            session()->flash('error', 'Unauthorized action.');
            $this->showPaymentModal = false;
            return;
        }

        $billing = Billing::find($billingId);

        if (!$billing || $billing->status !== 'UNPAID') {
            session()->flash('error', 'Tagihan tidak valid untuk dibayar.');
            $this->showPaymentModal = false;
            return;
        }

        try {
            $paymentService = new PaymentService();
            $paymentService->processCashPayment($billing, Auth::id());

            session()->flash('message', 'Pembayaran berhasil diproses.');
        } catch (\Exception $e) {
            session()->flash('error', 'Gagal memproses pembayaran: ' . $e->getMessage());
        }

        $this->showPaymentModal = false;
        $this->selectedBilling = null;
    }

    public function render()
    {
        $query = Billing::with(['student', 'feeMaster', 'payments'])->where('visible_to_wali', true);

        if ($this->search) {
            $query->where(function ($q) {
                $q->whereHas('student', function ($sq) {
                    $sq->where('full_name', 'like', '%' . $this->search . '%')
                        ->orWhere('nis', 'like', '%' . $this->search . '%');
                })->orWhere('title', 'like', '%' . $this->search . '%');
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
