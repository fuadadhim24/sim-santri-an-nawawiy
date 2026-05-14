<?php

namespace App\Livewire;

use App\Models\Billing;
use App\Models\Student;
use App\Services\SecurePaymentService;
use Livewire\Component;
use SweetAlert2\Laravel\Swal;

class PaymentEntry extends Component
{
    public $search = '';
    public $selectedStudent = null;
    public $unpaidBills = [];
    public $billingIdToProcess = null;

    public function updatedSearch()
    {
        $this->selectedStudent = null;
        $this->unpaidBills = [];
    }

    public function selectStudent($studentId)
    {
        $this->selectedStudent = Student::find($studentId);
        if ($this->selectedStudent) {
            $this->unpaidBills = $this->selectedStudent->billings()
                ->where('status', 'UNPAID')
                ->orderBy('created_at', 'desc')
                ->get();
            $this->search = '';
            $this->calculateTotal();
        }
    }

    public function calculateTotal()
    {
        $this->totalAmount = collect($this->unpaidBills)->sum('final_amount');
    }

    public function confirmPayment($billingId)
    {
        $bill = Billing::find($billingId);
        log('confirming payment for billing ID: ' . $billingId);

        if ($bill && $bill->status == 'UNPAID') {
            $this->billingIdToProcess = $billingId;

            $this->dispatch('confirm-payment',
                billingId: $bill->id,
                title: $bill->title,
                amount: number_format($bill->final_amount, 0, ',', '.'),
                date: $bill->created_at->locale('id')->isoFormat('D MMMM Y'),
                studentName: $this->selectedStudent->full_name
            );
        }
    }

    #[\Livewire\Attributes\On('confirmed-payment')]
    public function processPayment()
    {
        abort_unless(auth()->user()->role === 'SUPER_ADMIN' || auth()->user()->role === 'ADMIN_TU', 403);
        
        if (!$this->billingIdToProcess) {
            return;
        }

        try {
            $bill = Billing::find($this->billingIdToProcess);

            if (!$bill || $bill->status != 'UNPAID') {
                throw new \Exception('Tagihan tidak ditemukan atau sudah dibayar.');
            }

            $service = new SecurePaymentService();
            $payment = $service->processCashPaymentSecurely(
                $bill,
                auth()->id(),
                'Pembayaran tunai oleh admin'
            );

            $this->unpaidBills = $this->selectedStudent->billings()
                ->where('status', 'UNPAID')
                ->orderBy('created_at', 'desc')
                ->get();

            Swal::success([
                'title' => 'Pembayaran berhasil dicatat untuk: ' . $bill->title,
            ]);

            $this->billingIdToProcess = null;
        } catch (\Exception $e) {
            Swal::error([
                'title' => 'Error',
                'text' => $e->getMessage(),
            ]);
            $this->billingIdToProcess = null;
        }
    }

    public function render()
    {
        $students = [];
        if (strlen($this->search) >= 3) {
            $students = Student::where('full_name', 'like', '%' . $this->search . '%')
                ->orWhere('nis', 'like', '%' . $this->search . '%')
                ->take(5)
                ->get();
        }

        return view('livewire.payment-entry', [
            'students' => $students
        ])->layout('layouts.admin');
    }
}
