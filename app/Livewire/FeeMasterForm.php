<?php

namespace App\Livewire;

use App\Models\FeeMaster;
use Livewire\Component;

class FeeMasterForm extends Component
{
    public ?FeeMaster $feeMaster = null;

    public $item_name = '';

    public $amount = '';

    public $fee_category_id = '';

    public $unit_target = '';

    public $residence_target = '';

    public $start_date = '';

    public $end_date = '';

    public $isEdit = false;

    protected function rules(): array
    {
        return [
            'item_name' => 'required|string|min:3',
            'amount' => 'required|numeric|min:0',
            'fee_category_id' => 'required|exists:fee_categories,id',
            'unit_target' => 'nullable|in:01,02,03',
            'residence_target' => 'nullable|in:MONDOK,NON_MONDOK',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
        ];
    }

    public function mount(FeeMaster $feeMaster = null)
    {
        if ($feeMaster && $feeMaster->exists) {
            $this->feeMaster = $feeMaster;
            $this->item_name = $feeMaster->item_name;
            $this->amount = $feeMaster->amount;
            $this->fee_category_id = $feeMaster->fee_category_id;
            $this->unit_target = $feeMaster->unit_target;
            $this->residence_target = $feeMaster->residence_target;
            $this->start_date = $feeMaster->start_date ? $feeMaster->start_date->format('Y-m-d') : '';
            $this->end_date = $feeMaster->end_date ? $feeMaster->end_date->format('Y-m-d') : '';
            $this->isEdit = true;
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
