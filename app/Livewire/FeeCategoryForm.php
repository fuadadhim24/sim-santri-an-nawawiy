<?php

namespace App\Livewire;

use App\Enums\ActivationMode;
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

    public $is_locked = false;
    public $activation_mode = 'multi_active';
    public $can_generate_before_acceptance = true;

    public $isEdit = false;

    public function mount(FeeCategory $feeCategory = null)
    {
        if ($feeCategory && $feeCategory->exists) {
            $this->feeCategory = $feeCategory;
            $this->name = $feeCategory->name;
            $this->code = $feeCategory->code;
            $this->is_locked = $feeCategory->is_locked;
            $this->activation_mode = $feeCategory->activation_mode;
            $this->can_generate_before_acceptance = $feeCategory->can_generate_before_acceptance;
            $this->isEdit = true;
        }
    }

    public function save()
    {
        $codeRule = 'required|alpha_dash|unique:fee_categories,code' . ($this->isEdit ? ',' . $this->feeCategory->id : '');

        $this->validate([
            'name' => 'required|min:3',
            'code' => $codeRule,
            'is_locked' => 'boolean',
            'activation_mode' => 'required|in:' . implode(',', ActivationMode::values()),
            'can_generate_before_acceptance' => 'boolean',
        ]);

        $data = [
            'name' => $this->name,
            'code' => strtoupper($this->code),
            'is_locked' => $this->is_locked,
            'activation_mode' => $this->activation_mode,
            'can_generate_before_acceptance' => $this->can_generate_before_acceptance,
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

    public function getActivationModeOptionsProperty(): array
    {
        return [
            'single_active_per_key' => 'Single Active Per Key',
            'multi_active' => 'Multi Active',
            'manual_only' => 'Manual Only',
        ];
    }

    public function render()
    {
        return view('livewire.fee-category-form')->layout('layouts.admin');
    }
}
