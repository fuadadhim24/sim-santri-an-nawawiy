<?php

namespace App\Livewire;

use App\Enums\StudentStatus;
use App\Models\Student;
use App\Models\SpmbSchedule;
use App\Models\FeeCategory;
use App\Models\FeeMaster;
use App\Services\BillingService;
use Livewire\Component;
use Livewire\WithPagination;

class StudentAcceptance extends Component
{
    use WithPagination;

    public $search = '';
    public $confirmingAccept = false;
    public $confirmingReject = false;
    public $selectedStudent = null;
    public $selectedStudents = [];
    public $selectAll = false;
    public $showBillingModal = false;
    public $availableBillings = [];
    public $selectedBillings = [];
    public $billingCategories = [];
    public $billingFees = [];
    public $studentsBySchedule = [];

    protected $billingService;

    public function boot(BillingService $billingService)
    {
        $this->billingService = $billingService;
    }

    public function render()
    {
        $query = Student::with(['guardian', 'spmbSchedule'])
            ->where('status', StudentStatus::PENDING->value);

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('full_name', 'like', '%' . $this->search . '%')
                    ->orWhere('nis', 'like', '%' . $this->search . '%');
            });
        }

        $students = $query->latest()->get();

        // Group students by SPMB schedule and convert to array
        $this->studentsBySchedule = $students->groupBy('spmb_schedule_id')->map(function ($group) {
            return $group->all();
        })->all();

        // Get all SPMB schedules
        $spmbSchedules = SpmbSchedule::whereIn('id', array_keys($this->studentsBySchedule))->get();

        // Load available billing categories and fees
        $this->loadBillingOptions();

        return view('livewire.student-acceptance', [
            'studentsBySchedule' => $this->studentsBySchedule,
            'spmbSchedules' => $spmbSchedules,
            'billingCategories' => $this->billingCategories,
            'billingFees' => $this->billingFees,
        ])->layout('layouts.admin');
    }

    private function loadBillingOptions()
    {
        // Get fee categories that can be generated after acceptance
        $this->billingCategories = FeeCategory::where('can_generate_before_acceptance', false)
            ->where('is_locked', false)
            ->whereNotIn('activation_mode', ['MANUAL_ONLY'])
            ->get();

        // Get all active fee masters for these categories
        $categoryIds = $this->billingCategories->pluck('id');
        $this->billingFees = FeeMaster::whereIn('fee_category_id', $categoryIds)
            ->where('is_active', true)
            ->with('category')
            ->get()
            ->groupBy('fee_category_id');
    }

    public function toggleSelectAll()
    {
        if ($this->selectAll) {
            $this->selectedStudents = [];
            $this->selectAll = false;
        } else {
            $this->selectedStudents = [];
            foreach ($this->studentsBySchedule as $scheduleStudents) {
                foreach ($scheduleStudents as $student) {
                    $this->selectedStudents[$student->id] = true;
                }
            }
            $this->selectAll = true;
        }
    }

    public function toggleStudent($studentId)
    {
        if (isset($this->selectedStudents[$studentId])) {
            unset($this->selectedStudents[$studentId]);
        } else {
            $this->selectedStudents[$studentId] = true;
        }

        // Check if all students are selected
        $totalStudents = 0;
        foreach ($this->studentsBySchedule as $scheduleStudents) {
            $totalStudents += count($scheduleStudents);
        }

        $this->selectAll = (count($this->selectedStudents) === $totalStudents);
    }

    public function confirmAccept($studentId = null)
    {
        \Log::info('confirmAccept called', [
            'studentId' => $studentId,
            'selectedStudentsCount' => count($this->selectedStudents),
            'confirmingAccept' => $this->confirmingAccept,
            'showBillingModal' => $this->showBillingModal,
        ]);

        if ($studentId) {
            $this->selectedStudent = Student::find($studentId);
            $this->confirmingAccept = true;
            \Log::info('Single student accept confirmation', [
                'studentId' => $studentId,
                'studentName' => $this->selectedStudent?->full_name,
                'confirmingAccept' => $this->confirmingAccept,
            ]);
        } else {
            if (empty($this->selectedStudents)) {
                session()->flash('error', 'Pilih minimal satu santri untuk diterima.');
                \Log::warning('No students selected for bulk accept');
                return;
            }
            $this->showBillingModal = true;
            \Log::info('Bulk accept confirmation', [
                'selectedStudentsCount' => count($this->selectedStudents),
                'showBillingModal' => $this->showBillingModal,
            ]);
        }
    }

    public function acceptStudent()
    {
        \Log::info('acceptStudent called', [
            'selectedStudent' => $this->selectedStudent?->toArray(),
            'confirmingAccept' => $this->confirmingAccept,
        ]);

        if ($this->selectedStudent) {
            try {
                $this->selectedStudent->markAsAccepted();
                \Log::info('Student marked as accepted', [
                    'studentId' => $this->selectedStudent->id,
                    'newStatus' => $this->selectedStudent->status,
                ]);

                // Generate billings for accepted student
                $billingCount = $this->billingService->generateBillingsForAcceptedStudent($this->selectedStudent);
                \Log::info('Billings generated for accepted student', [
                    'studentId' => $this->selectedStudent->id,
                    'billingCount' => $billingCount,
                ]);

                // Update guardian information
                $this->updateGuardianInformation($this->selectedStudent->guardian);

                session()->flash('success', 'Santri berhasil diterima. Tagihan SPMB akan dibuat secara otomatis.');

                $this->confirmingAccept = false;
                $this->selectedStudent = null;
            } catch (\Exception $e) {
                \Log::error('Error accepting student', [
                    'studentId' => $this->selectedStudent->id,
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString(),
                ]);
                session()->flash('error', 'Gagal menerima santri: ' . $e->getMessage());
            }
        } else {
            \Log::warning('acceptStudent called without selected student');
        }
    }

    public function bulkAcceptStudents()
    {
        if (empty($this->selectedBillings)) {
            session()->flash('error', 'Pilih minimal satu jenis tagihan untuk dibuat.');
            return;
        }

        $studentIds = array_keys($this->selectedStudents);
        $students = Student::whereIn('id', $studentIds)->get();

        $successCount = 0;

        foreach ($students as $student) {
            $student->markAsAccepted();

            foreach ($this->selectedBillings as $feeMasterId) {
                $feeMaster = FeeMaster::find($feeMasterId);
                if ($feeMaster) {
                    try {
                        $this->billingService->generateBill($student, $feeMaster->fee_category_id, $feeMaster->item_name);
                        $successCount++;
                    } catch (\Exception $e) {
                        \Log::error('Failed to generate billing for student', [
                            'student_id' => $student->id,
                            'fee_master_id' => $feeMasterId,
                            'error' => $e->getMessage()
                        ]);
                    }
                }
            }

            $this->updateGuardianInformation($student->guardian);
        }

        session()->flash('success', "{$successCount} tagihan berhasil dibuat untuk " . count($students) . " santri yang diterima.");

        $this->showBillingModal = false;
        $this->selectedBillings = [];
        $this->selectedStudents = [];
        $this->selectAll = false;
    }

    public function confirmReject($studentId = null)
    {
        \Log::info('confirmReject called', [
            'studentId' => $studentId,
            'selectedStudentsCount' => count($this->selectedStudents),
            'confirmingReject' => $this->confirmingReject,
        ]);

        if ($studentId) {
            $this->selectedStudent = Student::find($studentId);
            $this->confirmingReject = true;
            \Log::info('Single student reject confirmation', [
                'studentId' => $studentId,
                'studentName' => $this->selectedStudent?->full_name,
                'confirmingReject' => $this->confirmingReject,
            ]);
        } else {
            if (empty($this->selectedStudents)) {
                session()->flash('error', 'Pilih minimal satu santri untuk ditolak.');
                \Log::warning('No students selected for bulk reject');
                return;
            }
            $this->confirmingReject = true;
            \Log::info('Bulk reject confirmation', [
                'selectedStudentsCount' => count($this->selectedStudents),
                'confirmingReject' => $this->confirmingReject,
                'selectedStudent' => $this->selectedStudent,
            ]);
        }
    }

    public function rejectStudent()
    {
        \Log::info('rejectStudent called', [
            'selectedStudent' => $this->selectedStudent?->toArray(),
            'confirmingReject' => $this->confirmingReject,
        ]);

        if ($this->selectedStudent) {
            try {
                // Store guardian reference before deletion
                $guardian = $this->selectedStudent->guardian;

                // Soft delete the student
                $this->selectedStudent->delete();
                \Log::info('Student deleted (rejected)', [
                    'studentId' => $this->selectedStudent->id,
                    'studentName' => $this->selectedStudent->full_name,
                ]);

                // Update guardian information
                $this->updateGuardianInformation($guardian);

                session()->flash('success', 'Santri berhasil ditolak.');

                $this->confirmingReject = false;
                $this->selectedStudent = null;
            } catch (\Exception $e) {
                \Log::error('Error rejecting student', [
                    'studentId' => $this->selectedStudent->id,
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString(),
                ]);
                session()->flash('error', 'Gagal menolak santri: ' . $e->getMessage());
            }
        } else {
            \Log::warning('rejectStudent called without selected student');
        }
    }

    public function bulkRejectStudents()
    {
        if (empty($this->selectedStudents)) {
            session()->flash('error', 'Pilih minimal satu santri untuk ditolak.');
            return;
        }

        $studentIds = array_keys($this->selectedStudents);
        $students = Student::whereIn('id', $studentIds)->get();

        foreach ($students as $student) {
            // Store guardian reference before deletion
            $guardian = $student->guardian;

            // Soft delete the student
            $student->delete();

            // Update guardian information
            $this->updateGuardianInformation($guardian);
        }

        session()->flash('success', count($students) . ' santri berhasil ditolak.');

        $this->confirmingReject = false;
        $this->selectedStudents = [];
        $this->selectAll = false;
    }

    public function cancelAction()
    {
        $this->confirmingAccept = false;
        $this->confirmingReject = false;
        $this->showBillingModal = false;
        $this->selectedStudent = null;
        $this->selectedBillings = [];
    }

    public function updatedSearch()
    {
        $this->resetPage();
        $this->selectedStudents = [];
        $this->selectAll = false;
    }

    public function toggleBillingCategory($categoryId)
    {
        $feeIds = $this->billingFees->get($categoryId, collect())->pluck('id')->toArray();

        if (empty(array_intersect($this->selectedBillings, $feeIds))) {
            // Select all fees in this category
            $this->selectedBillings = array_merge($this->selectedBillings, $feeIds);
        } else {
            // Deselect all fees in this category
            $this->selectedBillings = array_diff($this->selectedBillings, $feeIds);
        }

        $this->selectedBillings = array_unique($this->selectedBillings);
    }

    public function isCategorySelected($categoryId)
    {
        $feeIds = $this->billingFees->get($categoryId, collect())->pluck('id')->toArray();
        return !empty(array_intersect($this->selectedBillings, $feeIds));
    }

    private function updateGuardianInformation($guardian)
    {
        if (!$guardian) {
            return;
        }

        // Refresh the guardian's student data to update dashboard counts
        $guardian->load('students');

        // Dispatch event to update GuardianDashboard if it's being viewed
        $this->dispatch('guardian-updated', [
            'guardian_id' => $guardian->id,
        ]);
    }
}
