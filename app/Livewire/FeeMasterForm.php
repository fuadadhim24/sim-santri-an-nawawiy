<?php

namespace App\Livewire;

use App\Models\FeeMaster;
use Livewire\Attributes\Rule;
use Livewire\Component;

class FeeMasterForm extends Component
{
    public ?FeeMaster $feeMaster = null;

    #[Rule('required|string|min:3')]
    public $item_name = '';

    #[Rule('required|numeric|min:0')]
    public $amount = '';

    #[Rule('required|exists:fee_categories,id')]
    public $fee_category_id = '';

    #[Rule('nullable|in:01,02,03')]
    public $unit_target = '';

    #[Rule('nullable|in:MONDOK,NON_MONDOK')]
    public $residence_target = '';

    #[Rule('required|in:ONCE,MONTHLY,YEARLY')]
    public $billing_interval = 'MONTHLY';

    #[Rule('nullable|date')]
    public $start_date = '';

    #[Rule('nullable|date|after_or_equal:start_date')]
    public $end_date = '';

    #[Rule('nullable|integer|min:1|max:28')]
    public $billing_day = 10;

    public $isEdit = false;

    public function mount(FeeMaster $feeMaster = null)
    {
        if ($feeMaster && $feeMaster->exists) {
            $this->feeMaster = $feeMaster;
            $this->item_name = $feeMaster->item_name;
            $this->amount = $feeMaster->amount;
            $this->fee_category_id = $feeMaster->fee_category_id;
            $this->unit_target = $feeMaster->unit_target;
            $this->residence_target = $feeMaster->residence_target;
            $this->billing_interval = $feeMaster->category?->billing_interval ?? 'MONTHLY';
            $this->start_date = $feeMaster->start_date ? $feeMaster->start_date->format('Y-m-d') : '';
            $this->end_date = $feeMaster->end_date ? $feeMaster->end_date->format('Y-m-d') : '';
            $this->billing_day = $feeMaster->billing_day;
            $this->isEdit = true;
        }
    }

    public function updatedFeeCategoryId($value)
    {
        if ($value) {
            $category = \App\Models\FeeCategory::find($value);
            if ($category) {
                $this->billing_interval = $category->billing_interval;
            }
        }
    }

    public function save()
    {
        $this->validate();

        $data = [
            'item_name' => $this->item_name,
            'amount' => $this->amount,
            'fee_category_id' => $this->fee_category_id,
            'unit_target' => $this->unit_target ?: null,
            'residence_target' => $this->residence_target ?: null,
            'start_date' => $this->start_date ?: null,
            'end_date' => $this->end_date ?: null,
            'billing_day' => $this->billing_day ?: null,
        ];

        if ($this->isEdit) {
            $this->feeMaster->update($data);
            session()->flash('message', 'Fee Master updated successfully.');
        } else {
            FeeMaster::create($data);
            session()->flash('message', 'Fee Master created successfully.');
        }

        return redirect()->route('admin.fee-masters');
    }

    public function getFeeCategoriesProperty()
    {
        return \App\Models\FeeCategory::orderBy('name')->get();
    }

    public function render()
    {
        return view('livewire.fee-master-form')->layout('layouts.admin');
    }
}
