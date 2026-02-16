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

    #[Rule('required|in:PENDAFTARAN,DAFTAR_ULANG,BULANAN,SEMESTERAN,AKHIR_SEKOLAH')]
    public $category = 'BULANAN';

    #[Rule('nullable|in:01,02,03')]
    public $unit_target = '';

    #[Rule('nullable|in:MONDOK,NON_MONDOK')]
    public $residence_target = '';

    public $isEdit = false;

    public function mount(FeeMaster $feeMaster = null)
    {
        if ($feeMaster && $feeMaster->exists) {
            $this->feeMaster = $feeMaster;
            $this->item_name = $feeMaster->item_name;
            $this->amount = $feeMaster->amount;
            $this->category = $feeMaster->category;
            $this->unit_target = $feeMaster->unit_target;
            $this->residence_target = $feeMaster->residence_target;
            $this->isEdit = true;
        }
    }

    public function save()
    {
        $this->validate();

        $data = [
            'item_name' => $this->item_name,
            'amount' => $this->amount,
            'category' => $this->category,
            'unit_target' => $this->unit_target ?: null,
            'residence_target' => $this->residence_target ?: null,
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

    public function render()
    {
        return view('livewire.fee-master-form')->layout('layouts.admin');
    }
}
