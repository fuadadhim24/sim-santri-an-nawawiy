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
    public $can_generate_before_acceptance = false;
    public $is_active = true;

    public $isEdit = false;
    public $archivedFees = [];
    public $restore_fees = true;
    public $showRestoreOption = false;

    public function mount(FeeCategory $feeCategory = null)
    {
        if ($feeCategory && $feeCategory->exists) {
            $this->feeCategory = $feeCategory;
            $this->name = $feeCategory->name;
            $this->code = $feeCategory->code;
            $this->is_locked = $feeCategory->is_locked;
            $this->activation_mode = $feeCategory->activation_mode;
            $this->can_generate_before_acceptance = $feeCategory->can_generate_before_acceptance;
            $this->is_active = $feeCategory->is_active;
            $this->isEdit = true;

            $this->archivedFees = $feeCategory->fees()->onlyTrashed()->get()->toArray();
            if (!$this->is_active && count($this->archivedFees) > 0) {
                $this->showRestoreOption = true;
            }
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
            'is_active' => 'boolean',
        ]);

        $data = [
            'name' => $this->name,
            'code' => strtoupper($this->code),
            'is_locked' => $this->is_locked,
            'activation_mode' => $this->activation_mode,
            'can_generate_before_acceptance' => $this->can_generate_before_acceptance,
            'is_active' => $this->is_active,
        ];

        if ($this->isEdit) {
            $wasActive = $this->feeCategory->is_active;
            $this->feeCategory->update($data);

            if ($wasActive && !$this->is_active) {
                $activeFees = $this->feeCategory->fees()->where('is_active', true)->get();
                foreach ($activeFees as $fee) {
                    $fee->archive();
                }
                session()->flash('message', 'Kategori dinonaktifkan. Seluruh master biaya yang terhubung telah otomatis diarsipkan.');
            } elseif (!$wasActive && $this->is_active && $this->restore_fees) {
                $archivedFees = $this->feeCategory->fees()->onlyTrashed()->get();
                foreach ($archivedFees as $fee) {
                    $fee->restore();
                    $fee->update(['is_active' => true]);
                }
                session()->flash('message', 'Kategori diaktifkan kembali. Seluruh master biaya terkait telah dipulihkan.');
            } else {
                session()->flash('message', 'Kategori biaya berhasil diperbarui.');
            }
        } else {
            FeeCategory::create($data);
            session()->flash('message', 'Kategori biaya berhasil ditambahkan.');
        }

        return redirect()->route('admin.fee-categories');
    }

    public function getActivationModeOptionsProperty(): array
    {
        return [
            'single_active_per_key' => 'Otomatis (1 tagihan aktif per periode)',
            'multi_active' => 'Otomatis (boleh banyak tagihan)',
            'manual_only' => 'Tidak Otomatis (dibuat manual oleh admin)',
        ];
    }

    public function render()
    {
        return view('livewire.fee-category-form')->layout('layouts.admin');
    }
}
