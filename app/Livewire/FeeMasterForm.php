<?php

namespace App\Livewire;

use App\Models\FeeMaster;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use SweetAlert2\Laravel\Swal;

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

    public $recurrence_type = 'ONE_TIME';
    public $due_days = 14;
    public $billing_day = 1;
    public $class_level_target_id = '';
    public $update_policy = 'none';

    protected function rules(): array
    {
        return [
            'item_name' => 'required|string|min:3',
            'amount' => 'required|numeric|min:0',
            'fee_category_id' => 'required|exists:fee_categories,id',
            'unit_target' => 'nullable|in:01,02,03',
            'residence_target' => 'nullable|in:MONDOK,NON_MONDOK,NGAJI_ONLY',
            'class_level_target_id' => 'nullable|exists:class_levels,id',
            'recurrence_type' => 'required|in:ONE_TIME,MONTHLY,EVERY_6_MONTHS,YEARLY',
            'due_days' => 'required|integer|min:0',
            'billing_day' => 'nullable|integer|min:1|max:31',
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
            $this->class_level_target_id = $feeMaster->class_level_target_id ?? '';
            $this->recurrence_type = $feeMaster->recurrence_type ?? 'ONE_TIME';
            $this->due_days = $feeMaster->due_days ?? 14;
            $this->billing_day = $feeMaster->billing_day ?? 1;
            $this->start_date = $feeMaster->start_date ? $feeMaster->start_date->format('Y-m-d') : '';
            $this->end_date = $feeMaster->end_date ? $feeMaster->end_date->format('Y-m-d') : '';
            $this->isEdit = true;
        }
    }

    public function save()
    {
        $this->validate();

        if (!$this->isEdit) {
            $query = \App\Models\Student::where('is_active', true)->where('status', 'diterima');
            if ($this->unit_target) {
                $query->where('unit_code', $this->unit_target);
            }
            if ($this->residence_target) {
                $query->where('residence_status', $this->residence_target);
            }
            if ($this->class_level_target_id) {
                $query->where('class_level_id', $this->class_level_target_id);
            }
            $studentCount = $query->count();

            $feeCategoryName = $this->feeCategories->find($this->fee_category_id)?->name ?? '';

            $infoRecurrence = $this->recurrence_type === 'MONTHLY' ? ' (Bulanan)' : ($this->recurrence_type === 'EVERY_6_MONTHS' ? ' (Per 6 Bulan)' : ($this->recurrence_type === 'YEARLY' ? ' (Tahunan)' : ' (Sekali Bayar)'));

            $classTargetName = $this->class_level_target_id 
                ? (\App\Models\ClassLevel::find($this->class_level_target_id)?->name ?? 'Semua Kelas')
                : 'Semua Kelas';

            $this->dispatch('confirm-fee-creation',
                studentCount: $studentCount,
                itemName: $this->item_name . $infoRecurrence,
                amount: number_format($this->amount, 0, ',', '.'),
                category: $feeCategoryName,
                unitTarget: $this->unit_target ?? 'Semua Unit',
                residenceTarget: $this->residence_target ?? 'Semua Status Domisili',
                classTarget: $classTargetName,
                startDate: $this->start_date ?: '-',
                endDate: $this->end_date ?: '-'
            );
            return;
        }

        $this->processSave();
    }

    #[\Livewire\Attributes\On('confirmedSave')]
    public function processSave()
    {
        $this->validate();

        if ($this->isEdit && (!$this->feeMaster || !$this->feeMaster->exists)) {
            session()->flash('error', 'Invalid fee master data.');
            return redirect()->route('admin.fee-masters');
        }

        if (!$this->isEdit && $this->feeMaster) {
            session()->flash('error', 'Invalid state for creating new fee master.');
            return redirect()->route('admin.fee-masters');
        }

        $data = [
            'item_name' => $this->item_name,
            'amount' => $this->amount,
            'fee_category_id' => $this->fee_category_id,
            'unit_target' => $this->unit_target ?: null,
            'residence_target' => $this->residence_target ?: null,
            'class_level_target_id' => $this->class_level_target_id ?: null,
            'recurrence_type' => $this->recurrence_type,
            'due_days' => $this->due_days,
            'billing_day' => in_array($this->recurrence_type, ['MONTHLY', 'EVERY_6_MONTHS', 'YEARLY']) ? $this->billing_day : null,
            'start_date' => $this->start_date ?: null,
            'end_date' => $this->end_date ?: null,
        ];

        if (!$this->isEdit) {
            DB::transaction(function () use ($data) {
                $feeMaster = FeeMaster::create($data);

                $query = \App\Models\Student::where('is_active', true)->where('status', 'diterima');
                if ($this->unit_target) {
                    $query->where('unit_code', $this->unit_target);
                }
                if ($this->residence_target) {
                    $query->where('residence_status', $this->residence_target);
                }
                if ($this->class_level_target_id) {
                    $query->where('class_level_id', $this->class_level_target_id);
                }

                $students = $query->get();
                foreach ($students as $student) {
                    \App\Models\Billing::create([
                        'student_id' => $student->id,
                        'fee_master_id' => $feeMaster->id,
                        'title' => $this->item_name,
                        'original_amount' => $this->amount,
                        'discount_applied' => 0,
                        'final_amount' => $this->amount,
                        'status' => 'UNPAID',
                        'version' => 1,
                        'visible_to_wali' => true,
                    ]);
                }

                Swal::success([
                    'title' => 'Data Master Biaya dan ' . $students->count() . ' Tagihan berhasil dibuat.',
                ]);
            });
        } else {
            $oldAmount = $this->feeMaster->amount;
            $oldItemName = $this->feeMaster->item_name;

            if ($oldAmount != $this->amount || $oldItemName != $this->item_name) {
                DB::transaction(function () use ($data) {
                    // 1. Create the new active fee master first
                    $newFeeMaster = FeeMaster::create($data);

                    // 2. Archive the old fee master, pointing its replaced_by to the new active one
                    $this->feeMaster->archive($newFeeMaster->id);

                    // 3. Copy discounts from the old fee master to the new fee master
                    $oldDiscounts = \App\Models\Discount::where('fee_master_id', $this->feeMaster->id)->get();
                    foreach ($oldDiscounts as $discount) {
                        \App\Models\Discount::create([
                            'fee_master_id' => $newFeeMaster->id,
                            'target_status' => $discount->target_status,
                            'discount_amount' => $discount->discount_amount,
                        ]);
                    }

                    // 4. Do NOT update or touch any existing unpaid billings (tidak berlaku surut)

                    Swal::success([
                        'title' => 'Fee Master updated successfully.',
                    ]);
                });
            } else {
                $this->feeMaster->update($data);

                Swal::success([
                    'title' => 'Fee Master updated successfully.',
                ]);
            }
        }

        return redirect()->route('admin.fee-masters');
    }

    public function getFeeCategoriesProperty()
    {
        return \App\Models\FeeCategory::query()
            ->where('is_active', true)
            ->when($this->isEdit && $this->feeMaster?->fee_category_id, function ($query) {
                $query->orWhere('id', $this->feeMaster->fee_category_id);
            })
            ->orderBy('name')
            ->get();
    }

    public function getClassLevelsProperty()
    {
        return \App\Models\ClassLevel::orderBy('level_order')->get();
    }

    public function getAffectedBillingsProperty()
    {
        return collect();
    }

    public function render()
    {
        return view('livewire.fee-master-form')->layout('layouts.admin');
    }
}
