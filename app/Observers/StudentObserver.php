<?php

namespace App\Observers;

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
        if ($student->wasChanged('special_status')) {
            $oldStatus = $student->getOriginal('special_status');
            $newStatus = $student->special_status;

            $count = $this->billingService->recalculateStudentBillings($student);

            Log::info('Student special_status changed: recalculated billings', [
                'student_id' => $student->id,
                'old_status' => $oldStatus,
                'new_status' => $newStatus,
                'billings_updated' => $count,
            ]);
        }
    }
}
