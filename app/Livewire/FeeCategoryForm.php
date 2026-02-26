<?php

namespace App\Livewire;

use App\Models\FeeCategory;
use Livewire\Attributes\Rule;
use Livewire\Component;

class FeeCategoryForm extends Component
{
    public ?FeeCategory $feeCategory = null;

    #[Rule('required|min:3')]
    public $name = '';

    #[Rule('required|alpha_dash')]
    public $code = '';

    #[Rule('required|in:ONCE,MONTHLY,YEARLY')]
    public $billing_interval = 'MONTHLY';

    public $isEdit = false;

    public function mount(FeeCategory $feeCategory = null)
    {
        if ($feeCategory && $feeCategory->exists) {
            $this->feeCategory = $feeCategory;
            $this->name = $feeCategory->name;
            $this->code = $feeCategory->code;
            $this->billing_interval = $feeCategory->billing_interval;
            $this->isEdit = true;
        }
    }

    public function save()
    {
        // Adjust unique rule for edit
        $codeRule = 'required|alpha_dash|unique:fee_categories,code' . ($this->isEdit ? ',' . $this->feeCategory->id : '');

        $this->validate([
            'name' => 'required|min:3',
            'code' => $codeRule,
            'billing_interval' => 'required|in:ONCE,MONTHLY,YEARLY',
        ]);

        $data = [
            'name' => $this->name,
            'code' => strtoupper($this->code),
            'billing_interval' => $this->billing_interval,
        ];

        if ($this->isEdit) {
            $this->feeCategory->update($data);
            session()->flash('message', 'Kategori biaya berhasil diperbarui.');
        } else {
            FeeCategory::create($data);
            session()->flash('message', 'Kategori biaya berhasil ditambahkan.');
        }

        return redirect()->route('admin.fee-categories');
    }

    public function render()
    {
        return view('livewire.fee-category-form')->layout('layouts.admin');
    }
}
