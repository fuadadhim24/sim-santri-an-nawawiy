<?php

namespace App\Livewire;

use App\Models\Billing;
use App\Models\FeeMaster;
use App\Models\Student;
use Livewire\Attributes\Rule;
use Livewire\Component;

class BillingForm extends Component
{
    #[Rule('required|exists:students,id')]
    public $student_id = '';

    #[Rule('required|exists:fee_masters,id')]
    public $fee_master_id = '';

    #[Rule('required|string|max:255')]
    public $title = '';

    #[Rule('required|numeric|min:0')]
    public $original_amount = '';

    #[Rule('nullable|numeric|min:0')]
    public $discount_applied = 0;

    #[Rule('required|numeric|min:0')]
    public $final_amount = '';

    public $isEdit = false;

    public function mount($billing = null)
    {
        if ($billing && $billing->exists) {
            $this->isEdit = true;
            $this->student_id = $billing->student_id;
            $this->fee_master_id = $billing->fee_master_id;
            $this->title = $billing->title;
            $this->original_amount = $billing->original_amount;
            $this->discount_applied = $billing->discount_applied;
            $this->final_amount = $billing->final_amount;
        }
    }

    public function updatedFeeMasterId($value)
    {
        if ($value) {
            $fee = FeeMaster::find($value);
            if ($fee) {
                $this->original_amount = $fee->amount;
                $this->title = $fee->item_name;
                $this->calculateFinalAmount();
            }
        }
    }

    public function updatedDiscountApplied()
    {
        $this->calculateFinalAmount();
    }

    private function calculateFinalAmount()
    {
        $this->final_amount = max(0, (float)$this->original_amount - (float)$this->discount_applied);
    }

    public function save()
    {
        $this->validate();

        $data = [
            'student_id' => $this->student_id,
            'fee_master_id' => $this->fee_master_id ?: null,
            'title' => $this->title,
            'original_amount' => $this->original_amount,
            'discount_applied' => $this->discount_applied ?: 0,
            'final_amount' => $this->final_amount,
            'status' => 'UNPAID',
        ];

        Billing::create($data);

        session()->flash('message', 'Tagihan berhasil dibuat.');
        return redirect()->route('admin.billings');
    }

    public function getStudentsProperty()
    {
        return Student::where('is_active', true)->orderBy('full_name')->get();
    }

    public function getFeeMastersProperty()
    {
        return FeeMaster::with('category')->orderBy('item_name')->get();
    }

    public function render()
    {
        return view('livewire.billing-form')->layout('layouts.admin');
    }
}
