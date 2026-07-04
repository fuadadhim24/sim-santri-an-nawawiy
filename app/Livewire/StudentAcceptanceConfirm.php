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

    // Editable form properties
    public $full_name;
    public $nisn;
    public $unit_code;
    public $residence_status;
    public $spmb_schedule_id;

    public function mount(Student $student)
    {
        $this->student = $student->load(['guardian', 'spmbSchedule']);
        
        // Populate form fields
        $this->full_name = $student->full_name;
        $this->nisn = $student->nisn;
        $this->unit_code = $student->unit_code;
        $this->residence_status = $student->residence_status;
        $this->spmb_schedule_id = $student->spmb_schedule_id;

        $this->loadAvailableBillings();
    }

    public function updated($propertyName)
    {
        if (in_array($propertyName, ['unit_code', 'residence_status'])) {
            $this->loadAvailableBillings();
        }
    }

    private function loadAvailableBillings()
    {
        // Get fee categories based on dynamically selected unit and domicile
        $query = FeeCategory::query()->where('is_active', true);

        // Filter by residence status (domicile)
        $query->where(function ($q) {
            $q->where('domicile_target', $this->residence_status)
              ->orWhereNull('domicile_target'); // Include general categories
        });

        // Filter by unit code
        $query->where(function ($q) {
            $q->where('unit_target', $this->unit_code)
              ->orWhereNull('unit_target'); // Include general categories
        });

        $this->availableBillings = $query->where('is_locked', false)
            ->with(['fees' => function ($q) {
                $q->where('is_active', true)
                  ->where(function ($sq) {
                      $sq->whereNull('unit_target')
                         ->orWhere('unit_target', $this->unit_code);
                  })
                  ->where(function ($sq) {
                      $sq->whereNull('residence_target')
                         ->orWhere('residence_target', $this->residence_status);
                  });
            }])
            ->orderBy('name')
            ->get()
            ->map(function ($category) {
                return [
                    'id' => $category->id,
                    'name' => $category->name,
                    'description' => $category->description,
                    'unit' => $category->unit_target,
                    'domicile' => $category->domicile_target,
                    'fees' => $category->fees->map(function ($fee) {
                        return [
                            'item_name' => $fee->item_name,
                            'amount' => $fee->amount,
                            'recurrence_type' => $fee->recurrence_type,
                            'due_days' => $fee->due_days,
                        ];
                    })->toArray(),
                    'total_amount' => $category->fees->sum('amount')
                ];
            })
            ->toArray();

        // Re-evaluate selected billings to match new available categories
        $this->selectedBillings = array_map(fn($b) => $b['id'], $this->availableBillings);
    }

    public function confirmAcceptance(BillingService $billingService)
    {
        $this->validate([
            'full_name' => 'required|string|min:3|max:255',
            'nisn' => 'nullable|numeric|digits:10',
            'unit_code' => 'required|in:01,02,03',
            'residence_status' => 'required|in:MONDOK,NON_MONDOK,NGAJI_ONLY',
            'spmb_schedule_id' => 'nullable|exists:spmb_schedules,id',
        ]);

        try {
            // Persist the verified/corrected data directly to the database before accepting
            $this->student->update([
                'full_name' => $this->full_name,
                'nisn' => $this->nisn ?: null,
                'unit_code' => $this->unit_code,
                'residence_status' => $this->residence_status,
                'spmb_schedule_id' => $this->spmb_schedule_id ?: null,
            ]);

            $this->student->update(['is_active' => true]);
            $this->student->markAsAccepted();
            $this->student->refresh();

            // Generate billings for selected categories
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

    public function rejectAcceptance()
    {
        try {
            $this->student->markAsRejected();
            session()->flash('success', 'Pendaftaran santri telah ditolak.');
            return redirect()->route('admin.student-acceptance');
        } catch (\Exception $e) {
            session()->flash('error', 'Gagal menolak santri: ' . $e->getMessage());
        }
    }

    public function render()
    {
        $schedules = \App\Models\SpmbSchedule::where('is_active', true)->get();

        return view('livewire.student-acceptance-confirm', [
            'schedules' => $schedules
        ])->layout('layouts.admin');
    }
}
