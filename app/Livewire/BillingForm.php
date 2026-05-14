<?php

namespace App\Livewire;

use App\Models\Billing;
use App\Models\FeeMaster;
use App\Models\Student;
use App\Services\EnhancedBillingService;
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

    #[Rule('nullable|date')]
    public $period_start = null;

    #[Rule('nullable|date|after_or_equal:period_start')]
    public $period_end = null;

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
            $this->period_start = $billing->billing_period_start?->format('Y-m-d');
            $this->period_end = $billing->billing_period_end?->format('Y-m-d');
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
        abort_unless(auth()->user()->role === 'SUPER_ADMIN' || auth()->user()->role === 'ADMIN_TU', 403);
        
        $this->validate();

        try {
            $service = new EnhancedBillingService();
            $student = Student::findOrFail($this->student_id);

            $billing = $service->generateBillSecurely(
                $student,
                $this->fee_master_id,
                $this->title,
                null,
                $this->period_start ? \Carbon\Carbon::parse($this->period_start) : null,
                $this->period_end ? \Carbon\Carbon::parse($this->period_end) : null
            );

            session()->flash('message', 'Tagihan berhasil dibuat.');
            return redirect()->route('admin.billings');
        } catch (\Exception $e) {
            session()->flash('error', $e->getMessage());
        }
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
