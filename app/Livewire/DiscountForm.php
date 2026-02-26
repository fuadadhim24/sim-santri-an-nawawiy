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

    #[Rule('required|in:ANAK_GURU,YATIM')]
    public $target_status = 'ANAK_GURU';

    #[Rule('required|numeric|min:0')]
    public $discount_amount = '';

    public $isEdit = false;

    // Computed property for fee masters
    public function getFeeMastersProperty()
    {
        return FeeMaster::with('category')->orderBy('fee_category_id')->orderBy('item_name')->get();
    }

    public function mount(Discount $discount = null)
    {
        if ($discount && $discount->exists) {
            $this->discount = $discount;
            $this->fee_master_id = $discount->fee_master_id;
            $this->target_status = $discount->target_status;
            $this->discount_amount = $discount->discount_amount;
            $this->isEdit = true;
        }
    }

    public function save()
    {
        $this->validate();

        $data = [
            'fee_master_id' => $this->fee_master_id,
            'target_status' => $this->target_status,
            'discount_amount' => $this->discount_amount,
        ];

        if ($this->isEdit) {
            $this->discount->update($data);
            session()->flash('message', 'Discount updated successfully.');
        } else {
            Discount::create($data);
            session()->flash('message', 'Discount created successfully.');
        }

        return redirect()->route('admin.discounts');
    }

    public function render()
    {
        return view('livewire.discount-form')->layout('layouts.admin');
    }
}
