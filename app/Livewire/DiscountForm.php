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

    #[Rule('required|in:ANAK_GURU,YATIM,PRESTASI')]
    public $target_status = 'ANAK_GURU';

    #[Rule('required|numeric|min:0')]
    public $discount_amount = '';

    public $isEdit = false;

    public $recalculate_policy = 'all'; // 'all', 'future'

    // Computed property for fee masters
    public function getFeeMastersProperty()
    {
        return FeeMaster::with('category')->orderBy('fee_category_id')->orderBy('item_name')->get();
    }

    // Computed property for affected billings preview
    public function getAffectedBillingsProperty()
    {
        if (empty($this->fee_master_id) || empty($this->target_status)) {
            return collect();
        }

        $feeMaster = FeeMaster::find($this->fee_master_id);
        if (!$feeMaster) {
            return collect();
        }

        $newDiscountAmount = (float)($this->discount_amount ?: 0);

        $query = \App\Models\Billing::where('status', 'UNPAID')
            ->where('fee_master_id', $this->fee_master_id)
            ->whereHas('student', function ($query) {
                $query->where('special_status', $this->target_status);
            })
            ->with('student');

        if ($this->isEdit) {
            if ($this->recalculate_policy === 'future') {
                $query->where(function($q) {
                    $q->where('due_date', '>=', now()->startOfDay()->format('Y-m-d'))
                      ->orWhereNull('due_date');
                });
            } elseif ($this->recalculate_policy === 'next_month') {
                $firstDayOfNextMonth = now()->addMonth()->startOfMonth()->format('Y-m-d');
                $query->where(function($q) use ($firstDayOfNextMonth) {
                    $q->where('due_date', '>=', $firstDayOfNextMonth)
                      ->orWhereNull('due_date');
                });
            }
        }

        return $query->get()
            ->map(function ($billing) use ($feeMaster, $newDiscountAmount) {
                $newFinalAmount = max(0, $billing->original_amount - $newDiscountAmount);
                return [
                    'student_name' => $billing->student->full_name,
                    'student_nis' => $billing->student->nis,
                    'billing_title' => $billing->title,
                    'original_amount' => $billing->original_amount,
                    'current_discount' => $billing->discount_applied,
                    'current_final' => $billing->final_amount,
                    'new_discount' => $newDiscountAmount,
                    'new_final' => $newFinalAmount,
                    'diff' => $newFinalAmount - $billing->final_amount,
                ];
            });
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
            \Illuminate\Support\Facades\DB::transaction(function () use ($data) {
                // Prevent DiscountObserver from automatically running recalculateBillingsForFeeMaster
                Discount::withoutEvents(function () use ($data) {
                    $this->discount->update($data);
                });

                // Run manual filtered recalculation
                $feeMaster = $this->discount->feeMaster;
                if ($feeMaster) {
                    $billingService = app(\App\Services\BillingService::class);
                    $query = \App\Models\Billing::where('status', 'UNPAID')
                        ->where('fee_master_id', $feeMaster->id)
                        ->with('student');

                    if ($this->recalculate_policy === 'future') {
                        $query->where(function($q) {
                            $q->where('due_date', '>=', now()->startOfDay()->format('Y-m-d'))
                              ->orWhereNull('due_date');
                        });
                    } elseif ($this->recalculate_policy === 'next_month') {
                        $firstDayOfNextMonth = now()->addMonth()->startOfMonth()->format('Y-m-d');
                        $query->where(function($q) use ($firstDayOfNextMonth) {
                            $q->where('due_date', '>=', $firstDayOfNextMonth)
                              ->orWhereNull('due_date');
                        });
                    }

                    $billings = $query->get();
                    foreach ($billings as $billing) {
                        $billingService->recalculateBilling($billing, $feeMaster);
                    }
                }
            });
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
