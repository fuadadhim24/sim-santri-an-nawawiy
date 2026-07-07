<?php

namespace App\Livewire;

use App\Models\Billing;
use App\Models\FeeMaster;
use App\Models\Student;
use App\Services\EnhancedBillingService;
use Livewire\Attributes\Rule;
use Livewire\Component;

class BillingForm extends Component
{
    #[Rule('required|exists:students,id')]
    public $student_id = '';

    #[Rule('required|exists:fee_masters,id')]
    public $fee_master_id = '';

    #[Rule('required|string|max:255')]
    public $title = '';

    #[Rule('required|numeric|min:0')]
    public $original_amount = '';

    #[Rule('nullable|numeric|min:0')]
    public $discount_applied = 0;

    #[Rule('required|numeric|min:0')]
    public $final_amount = '';

    #[Rule('nullable|date')]
    public $period_start = null;

    #[Rule('nullable|date|after_or_equal:period_start')]
    public $period_end = null;

    public $isEdit = false;
    public $student_search = '';

    public function mount($billing = null)
    {
        if ($billing && $billing->exists) {
            $this->isEdit = true;
            $this->student_id = $billing->student_id;
            $this->fee_master_id = $billing->fee_master_id;
            $this->title = $billing->title;
            $this->original_amount = $billing->original_amount;
            $this->discount_applied = $billing->discount_applied;
            $this->final_amount = $billing->final_amount;
            $this->period_start = $billing->billing_period_start?->format('Y-m-d');
            $this->period_end = $billing->billing_period_end?->format('Y-m-d');

            $student = Student::find($this->student_id);
            if ($student) {
                $this->student_search = $student->full_name . ' (' . $student->nis . ')';
            }
        }
    }

    public function updatedFeeMasterId($value)
    {
        if ($value) {
            $fee = FeeMaster::find($value);
            if ($fee) {
                $this->original_amount = $fee->amount;
                $this->title = $fee->item_name;
                $this->calculateFinalAmount();
            }
        }
    }

    public function updatedDiscountApplied()
    {
        $this->calculateFinalAmount();
    }

    private function calculateFinalAmount()
    {
        $this->final_amount = max(0, (float)$this->original_amount - (float)$this->discount_applied);
    }

    public function save()
    {
        abort_unless(auth()->user()->role === 'SUPER_ADMIN' || auth()->user()->role === 'BENDAHARA', 403);
        
        $this->validate();

        try {
            $student = Student::findOrFail($this->student_id);
            $feeMaster = FeeMaster::findOrFail($this->fee_master_id);

            if ($student->status !== 'ACTIVE' && $student->status !== \App\Enums\StudentStatus::ACCEPTED->value) {
                throw new \Exception("Tidak bisa membuat tagihan. Status santri: {$student->status}. Hanya santri ACTIVE yang dapat ditagih.");
            }

            $existingBilling = Billing::where('student_id', $student->id)
                ->where('fee_master_id', $this->fee_master_id)
                ->where('status', '!=', 'VOID')
                ->first();

            if ($existingBilling && (!$existingBilling->expires_at || $existingBilling->expires_at->isFuture())) {
                throw new \Exception('Tagihan untuk jenis biaya ini sudah ada untuk santri tersebut. Tidak bisa membuat duplikat.');
            }

            $priceSnapshot = [
                [
                    'item_name' => $this->title,
                    'amount' => (float)$this->original_amount,
                    'fee_master_id' => $feeMaster->id,
                ]
            ];

            $finalAmount = max(0, (float)$this->original_amount - (float)$this->discount_applied);

            $billing = Billing::create([
                'student_id' => $student->id,
                'fee_master_id' => $feeMaster->id,
                'title' => $this->title,
                'original_amount' => (float)$this->original_amount,
                'discount_applied' => (float)$this->discount_applied,
                'final_amount' => $finalAmount,
                'status' => 'UNPAID',
                'price_snapshot' => $priceSnapshot,
                'billing_period_start' => $this->period_start ? \Carbon\Carbon::parse($this->period_start) : null,
                'billing_period_end' => $this->period_end ? \Carbon\Carbon::parse($this->period_end) : null,
                'visible_to_wali' => true,
                'version' => 1,
            ]);

            session()->flash('message', 'Tagihan manual berhasil dibuat.');
            return redirect()->route('admin.billings');
        } catch (\Exception $e) {
            session()->flash('error', $e->getMessage());
        }
    }

    public function selectStudent($id, $name, $nis)
    {
        $this->student_id = $id;
        $this->student_search = $name . ' (' . $nis . ')';
    }

    public function clearStudentSelection()
    {
        $this->student_id = '';
        $this->student_search = '';
    }

    public function getSearchResultsProperty()
    {
        if (strlen($this->student_search) < 2) {
            return [];
        }

        return Student::where('is_active', true)
            ->where(function ($query) {
                $query->where('full_name', 'like', '%' . $this->student_search . '%')
                      ->orWhere('nis', 'like', '%' . $this->student_search . '%')
                      ->orWhere('registration_number', 'like', '%' . $this->student_search . '%');
            })
            ->orderBy('full_name')
            ->take(10)
            ->get();
    }

    public function getFeeMastersProperty()
    {
        $query = FeeMaster::with('category')->where('is_active', true);
        
        if ($this->student_id) {
            $student = Student::find($this->student_id);
            if ($student) {
                $query->where(function ($q) use ($student) {
                    $q->whereNull('unit_target')
                      ->orWhere('unit_target', $student->unit_code);
                })
                ->where(function ($q) use ($student) {
                    $q->whereNull('residence_target')
                      ->orWhere('residence_target', $student->residence_status);
                })
                ->where(function ($q) use ($student) {
                    $q->whereNull('class_level_target_id')
                      ->orWhere('class_level_target_id', $student->class_level_id);
                });
            }
        }
        
        return $query->orderBy('item_name')->get();
    }

    public function render()
    {
        return view('livewire.billing-form')->layout('layouts.admin');
    }
}
