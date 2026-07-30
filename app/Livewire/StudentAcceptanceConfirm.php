<?php

namespace App\Livewire;

use App\Models\Student;
use App\Models\FeeCategory;
use App\Services\BillingService;
use Livewire\Component;

class StudentAcceptanceConfirm extends Component
{
    public $student;
    public $selectedBillings = [];
    public $availableBillings = [];

    public $full_name;
    public $nis;
    public $nisn;
    public $unit_code;
    public $residence_status;
    public $spmb_schedule_id;
    public $class_level_id;
    public array $special_statuses = [];

    public $nisCheckStatus = null; // 'available', 'taken', 'empty'
    public $nisCheckMessage = '';

    public function mount(Student $student)
    {
        $this->student = $student->load(['guardian', 'spmbSchedule', 'specialStatuses']);
        
        $this->full_name = $student->full_name;
        $this->nisn = $student->nisn;
        $this->unit_code = $student->unit_code;
        $this->residence_status = $student->residence_status;
        $this->spmb_schedule_id = $student->spmb_schedule_id;
        $this->class_level_id = $student->class_level_id;
        $this->special_statuses = $this->student->specialStatuses->pluck('code')->toArray();

        $this->nis = $student->nis;
        if (!$this->nis) {
            $year = date('Y');
            $nisService = app(\App\Services\NisGeneratorService::class);
            $this->nis = $nisService->generate($this->unit_code, $year);
        }

        $this->loadAvailableBillings();
    }

    public function updated($propertyName)
    {
        if (in_array($propertyName, ['unit_code', 'residence_status', 'class_level_id', 'special_statuses'])) {
            $this->loadAvailableBillings();
        }

        if ($propertyName === 'unit_code') {
            $year = date('Y');
            $nisService = app(\App\Services\NisGeneratorService::class);
            $this->nis = $nisService->generate($this->unit_code, $year);
            $this->nisCheckStatus = null;
            $this->nisCheckMessage = '';
        }

        if ($propertyName === 'nis') {
            $this->nisCheckStatus = null;
            $this->nisCheckMessage = '';
        }
    }

    private function loadAvailableBillings()
    {
        $query = FeeCategory::query()->where('is_active', true);

        $query->where(function ($q) {
            $q->where('domicile_target', $this->residence_status)
              ->orWhereNull('domicile_target'); 
        });

        $query->where(function ($q) {
            $q->where('unit_target', $this->unit_code)
              ->orWhereNull('unit_target'); 
        });

        $this->availableBillings = $query->where('is_locked', false)
            ->with(['fees' => function ($q) {
                $q->with('classLevelTarget')
                  ->where('is_active', true)
                  ->where(function ($sq) {
                      $sq->whereNull('unit_target')
                         ->orWhere('unit_target', $this->unit_code);
                  })
                  ->where(function ($sq) {
                      $sq->whereNull('residence_target')
                         ->orWhere('residence_target', $this->residence_status);
                  })
                  ->where(function ($sq) {
                      $sq->whereNull('class_level_target_id')
                         ->orWhere('class_level_target_id', $this->class_level_id ?: null);
                  });
            }])
            ->orderBy('name')
            ->get()
            ->map(function ($category) {
                $statusCodes = $this->special_statuses ?: [];
                $feeIds = $category->fees->pluck('id')->toArray();
                
                $discounts = [];
                if (!empty($statusCodes) && !empty($feeIds)) {
                    $discounts = \App\Models\Discount::whereIn('fee_master_id', $feeIds)
                        ->whereIn('target_status', $statusCodes)
                        ->get()
                        ->groupBy('fee_master_id');
                }

                $feesWithDiscount = $category->fees->map(function ($fee) use ($discounts) {
                    $amount = (float)$fee->amount;
                    $discountAmount = 0;
                    
                    if (isset($discounts[$fee->id])) {
                        foreach ($discounts[$fee->id] as $d) {
                            $discountAmount += (float)$d->discount_amount;
                        }
                    }
                    
                    $discountAmount = min($discountAmount, $amount);
                    $finalAmount = max(0.0, $amount - $discountAmount);

                    return [
                        'item_name' => $fee->item_name,
                        'amount' => $amount,
                        'discount_applied' => $discountAmount,
                        'final_amount' => $finalAmount,
                        'recurrence_type' => $fee->recurrence_type,
                        'due_days' => $fee->due_days,
                        'unit' => $fee->unit_target,
                        'domicile' => $fee->residence_target,
                        'class_level_target_id' => $fee->class_level_target_id,
                        'class_level_target_name' => $fee->classLevelTarget?->name ?? 'SEMUA',
                    ];
                });

                $totalAmount = $feesWithDiscount->sum('final_amount');
                $totalDiscount = $feesWithDiscount->sum('discount_applied');
                $totalOriginal = $feesWithDiscount->sum('amount');

                return [
                    'id' => (string) $category->id,
                    'name' => $category->name,
                    'description' => $category->description,
                    'unit' => $category->unit_target,
                    'domicile' => $category->domicile_target,
                    'fees' => $feesWithDiscount->toArray(),
                    'total_amount' => $totalAmount,
                    'total_discount' => $totalDiscount,
                    'total_original' => $totalOriginal,
                ];
            })
            ->toArray();

        $this->selectedBillings = array_map(fn($b) => $b['id'], $this->availableBillings);
    }

    public function confirmAcceptance(BillingService $billingService)
    {
        try {
            $this->validate([
                'full_name' => 'required|string|min:3|max:255',
                'nis' => 'required|string|unique:students,nis,' . $this->student->id,
                'nisn' => 'nullable|numeric|digits:10',
                'unit_code' => 'required|in:01,02,03',
                'residence_status' => 'required|in:MONDOK,NON_MONDOK,NGAJI_ONLY',
                'spmb_schedule_id' => 'nullable|exists:spmb_schedules,id',
                'class_level_id' => 'required|exists:class_levels,id',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            $this->dispatch('swal:validation-error', [
                'errors' => $e->validator->errors()->all()
            ]);
            throw $e;
        }

        try {
            $this->student->update([
                'full_name' => $this->full_name,
                'nis' => $this->nis,
                'nisn' => $this->nisn ?: null,
                'unit_code' => $this->unit_code,
                'residence_status' => $this->residence_status,
                'spmb_schedule_id' => $this->spmb_schedule_id ?: null,
                'class_level_id' => $this->class_level_id,
            ]);

            // Sync status khusus dengan is_approved = true (tanda disetujui admin)
            $statusCodes = collect($this->special_statuses)->filter(fn($c) => !empty($c))->unique()->toArray();
            $this->student->specialStatuses()->sync(
                collect($statusCodes)->mapWithKeys(fn($code) => [$code => ['is_approved' => true]])->toArray()
            );

            $this->student->update(['is_active' => true]);
            $this->student->markAsAccepted();
            $this->student->refresh();

            if (!empty($this->selectedBillings)) {
                $billingService->generateBillingsForStudentWithCategories(
                    $this->student,
                    $this->selectedBillings
                );
            }

            session()->flash('success', 'Santri berhasil diterima, data telah dikoreksi, dan tagihan telah dibuat.');
            return redirect()->route('admin.student-acceptance');
        } catch (\Exception $e) {
            session()->flash('error', 'Gagal menerima santri: ' . $e->getMessage());
            \Log::error('Error confirming student acceptance', [
                'studentId' => $this->student->id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    public function checkNis()
    {
        $this->nis = trim($this->nis);
        
        if (empty($this->nis)) {
            $this->nisCheckStatus = 'empty';
            $this->nisCheckMessage = 'NIS tidak boleh kosong.';
            return;
        }

        $query = Student::where('nis', $this->nis);
        if ($this->student && $this->student->id) {
            $query->where('id', '!=', $this->student->id);
        }
        $existingStudent = $query->first();

        if ($existingStudent) {
            $this->nisCheckStatus = 'taken';
            $this->nisCheckMessage = 'NIS sudah digunakan oleh ' . $existingStudent->full_name . ' (' . ($existingStudent->classLevel?->name ?? 'Kelas tidak diketahui') . ').';
        } else {
            $this->nisCheckStatus = 'available';
            $this->nisCheckMessage = 'NIS tersedia dan belum digunakan.';
        }
    }

    public function toggleSelectAll()
    {
        if (count($this->selectedBillings) === count($this->availableBillings)) {
            $this->selectedBillings = [];
        } else {
            $this->selectedBillings = array_map(fn($b) => $b['id'], $this->availableBillings);
        }
    }

    public function cancel()
    {
        return redirect()->route('admin.student-acceptance');
    }

    public function rejectAcceptance(string $reason = '')
    {
        try {
            $this->student->markAsRejected($reason ?: null);
            session()->flash('success', 'Pendaftaran santri telah ditolak.');
            return redirect()->route('admin.student-acceptance');
        } catch (\Exception $e) {
            session()->flash('error', 'Gagal menolak santri: ' . $e->getMessage());
        }
    }

    public function getSpecialStatusesProperty()
    {
        return \App\Models\SpecialStatus::where('code', '!=', 'UMUM')->orderBy('name')->get();
    }

    public function render()
    {
        $schedules = \App\Models\SpmbSchedule::where('is_active', true)->get();
        
        $classLevels = \App\Models\ClassLevel::orderBy('level_order')->get();

        return view('livewire.student-acceptance-confirm', [
            'schedules' => $schedules,
            'classLevels' => $classLevels,
        ])->layout('layouts.admin');
    }
}
