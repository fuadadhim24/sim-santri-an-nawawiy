<?php

namespace App\Livewire;

use App\Models\Billing;
use App\Models\Student;
use Livewire\Component;

class PaymentEntry extends Component
{
    public $search = '';
    public $selectedStudent = null;
    public $unpaidBills = [];

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
        }
    }

    public function processPayment($billingId)
    {
        $bill = Billing::find($billingId);

        if ($bill && $bill->status == 'UNPAID') {
            $bill->update(['status' => 'PAID']);

            // Refresh the list
            $this->unpaidBills = $this->selectedStudent->billings()
                ->where('status', 'UNPAID')
                ->orderBy('created_at', 'desc')
                ->get();

            session()->flash('message', 'Payment recorded successfully for: ' . $bill->title);
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
