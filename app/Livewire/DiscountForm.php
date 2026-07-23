<?php

namespace App\Livewire;

use App\Models\Discount;
use App\Models\FeeMaster;
use Livewire\Attributes\Rule;
use Livewire\Component;

class DiscountForm extends Component
{
    public ?Discount $discount = null;

    #[Rule('required|exists:fee_masters,id')]
    public $fee_master_id = '';

    public $target_status = '';

    #[Rule('required|numeric|min:0')]
    public $discount_amount = '';

    public $isEdit = false;

    // Computed property for fee masters
    public function getFeeMastersProperty()
    {
        return FeeMaster::with('category')->orderBy('fee_category_id')->orderBy('item_name')->get();
    }

    // Computed property for affected billings preview
    public function getAffectedBillingsProperty()
    {
        return collect();
    }

    public function mount(Discount $discount = null)
    {
        if ($discount && $discount->exists) {
            $this->discount = $discount;
            $this->fee_master_id = $discount->fee_master_id;
            $this->target_status = $discount->target_status;
            $this->discount_amount = $discount->discount_amount;
            $this->isEdit = true;
        } else {
            $this->target_status = \App\Models\SpecialStatus::where('code', '!=', 'UMUM')->first()?->code ?? '';
        }
    }

    public function save()
    {
        $validCodes = \App\Models\SpecialStatus::where('code', '!=', 'UMUM')->pluck('code')->toArray();
        $this->validate([
            'target_status' => 'required|in:' . implode(',', $validCodes),
        ]);
        $this->validate();

        $data = [
            'fee_master_id' => $this->fee_master_id,
            'target_status' => $this->target_status,
            'discount_amount' => $this->discount_amount,
        ];

        if ($this->isEdit) {
            \Illuminate\Support\Facades\DB::transaction(function () use ($data) {
                // Prevent DiscountObserver from automatically running recalculateBillingsForFeeMaster
                Discount::withoutEvents(function () use ($data) {
                    $this->discount->update($data);
                });
            });
            session()->flash('message', 'Discount updated successfully.');
        } else {
            Discount::create($data);
            session()->flash('message', 'Discount created successfully.');
        }

        return redirect()->route('admin.discounts');
    }

    public function getSpecialStatusesProperty()
    {
        return \App\Models\SpecialStatus::where('code', '!=', 'UMUM')->get();
    }

    public function render()
    {
        return view('livewire.discount-form')->layout('layouts.admin');
    }
}
