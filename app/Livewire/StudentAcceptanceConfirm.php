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

    public function mount(Student $student)
    {
        $this->student = $student->load(['guardian', 'spmbSchedule']);
        $this->loadAvailableBillings();
    }

    private function loadAvailableBillings()
    {
        // Get fee categories based on student's unit and domicile
        $query = FeeCategory::query()->where('is_active', true);

        // Filter by student's residence status (domicile)
        $query->where(function ($q) {
            $q->where('domicile_target', $this->student->residence_status)
              ->orWhereNull('domicile_target'); // Include general categories
        });

        // Filter by student's unit code
        $query->where(function ($q) {
            $q->where('unit_target', $this->student->unit_code)
              ->orWhereNull('unit_target'); // Include general categories
        });

        $this->availableBillings = $query->where('is_locked', false)
            ->orderBy('name')
            ->get()
            ->map(function ($category) {
                return [
                    'id' => $category->id,
                    'name' => $category->name,
                    'description' => $category->description,
                    'unit' => $category->unit_target,
                    'domicile' => $category->domicile_target,
                ];
            })
            ->toArray();

        // By default, select all available billings
        $this->selectedBillings = array_map(fn($b) => $b['id'], $this->availableBillings);
    }

    public function confirmAcceptance(BillingService $billingService)
    {
        try {
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

            session()->flash('success', 'Santri berhasil diterima dan tagihan telah dibuat.');
            return redirect()->route('admin.student-acceptance');
        } catch (\Exception $e) {
            session()->flash('error', 'Gagal menerima santri: ' . $e->getMessage());
            \Log::error('Error confirming student acceptance', [
                'studentId' => $this->student->id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    public function cancel()
    {
        return redirect()->route('admin.student-acceptance');
    }

    public function render()
    {
        return view('livewire.student-acceptance-confirm')->layout('layouts.admin');
    }
}
