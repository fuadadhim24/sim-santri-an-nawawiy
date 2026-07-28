<?php

namespace App\Observers;

use App\Enums\StudentStatus;
use App\Models\Student;
use App\Services\BillingService;
use Illuminate\Support\Facades\Log;

class StudentObserver
{
    protected BillingService $billingService;

    public function __construct(BillingService $billingService)
    {
        $this->billingService = $billingService;
    }

    public function updated(Student $student): void
    {
        // Perubahan special statuses kini dihandle langsung oleh StudentForm
        // via $student->specialStatuses()->sync() setelah save.
        // Observer ini tidak perlu deteksi perubahan pivot (ManyToMany tidak trigger wasChanged).

        if ($student->wasChanged('status')) {
            $oldStatus = $student->getOriginal('status');
            $newStatus = $student->status;

            $this->handleStatusChange($student, $oldStatus, $newStatus);
        }
    }

    private function handleStatusChange(Student $student, string $oldStatus, string $newStatus): void
    {
        $oldEnum = StudentStatus::tryFrom($oldStatus);
        $newEnum = StudentStatus::tryFrom($newStatus);

        if (!$oldEnum || !$newEnum) {
            Log::warning('Invalid status values in StudentObserver', [
                'student_id' => $student->id,
                'old_status' => $oldStatus,
                'new_status' => $newStatus,
            ]);
            return;
        }

        if ($newEnum === StudentStatus::ACCEPTED && $oldEnum !== StudentStatus::ACCEPTED) {
            $this->handleStudentAccepted($student, $oldStatus);
        } elseif ($oldEnum === StudentStatus::ACCEPTED && $newEnum !== StudentStatus::ACCEPTED) {
            $this->handleStudentNoLongerAccepted($student, $newStatus);
        } else {
            Log::info('Student status changed', [
                'student_id' => $student->id,
                'student_name' => $student->full_name,
                'old_status' => $oldEnum->getLabel(),
                'new_status' => $newEnum->getLabel(),
            ]);
        }
    }

    private function handleStudentAccepted(Student $student, string $oldStatus): void
    {
        Log::info('Student accepted - generating required billings', [
            'student_id' => $student->id,
            'student_name' => $student->full_name,
            'previous_status' => $oldStatus,
            'new_status' => 'Diterima',
            'timestamp' => now(),
        ]);

        // First, recalculate any existing billings
        $recalculatedCount = $this->billingService->recalculateStudentBillings($student);

        // Then, generate new billings for categories that require acceptance
        $generatedCount = $this->billingService->generateBillingsForAcceptedStudent($student);

        Log::info('Processed billings for accepted student', [
            'student_id' => $student->id,
            'billings_recalculated' => $recalculatedCount,
            'billings_generated' => $generatedCount,
        ]);
    }

    private function handleStudentNoLongerAccepted(Student $student, string $newStatus): void
    {
        Log::info('Student no longer accepted', [
            'student_id' => $student->id,
            'student_name' => $student->full_name,
            'previous_status' => 'Diterima',
            'new_status' => $newStatus,
            'timestamp' => now(),
        ]);

        $unpaidBillings = $student->billings()
            ->where('status', 'UNPAID')
            ->count();

        if ($unpaidBillings > 0) {
            Log::info('Student has unpaid billings after status change', [
                'student_id' => $student->id,
                'unpaid_billings_count' => $unpaidBillings,
                'note' => 'Billings remain active for payment processing',
            ]);
        }
    }
}
