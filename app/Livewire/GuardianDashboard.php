<?php

namespace App\Livewire;

use App\Models\Billing;
use App\Models\Guardian;
use App\Models\FeeMaster;
use App\Models\FeeCategory;
use App\Models\SpmbSchedule;
use App\Models\Student;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class GuardianDashboard extends Component
{
    protected $listeners = ['guardian-updated' => '$refresh'];
    public function pay($billingId)
    {
        $bill = Billing::find($billingId);

        if ($bill && $bill->status == 'UNPAID') {
            $user = Auth::user();
            $guardian = Guardian::where('user_id', $user->id)->first();

            if (!$guardian) {
                 return;
            }

            $studentIds = $guardian->students->pluck('id')->toArray();
            if (in_array($bill->student_id, $studentIds)) {
                 $bill->update(['status' => 'PAID']);
                 session()->flash('message', 'Pembayaran berhasil untuk tagihan: ' . $bill->title);
            }
        }
    }

    public function render()
    {
        $user = Auth::user();

        $guardian = Guardian::where('user_id', $user->id)->first();

        if (!$guardian) {
            abort(403, 'Data wali santri tidak ditemukan untuk pengguna ini.');
        }

        $guardian->load(['user', 'students.billings' => function ($query) {
            $query->orderBy('created_at', 'desc');
        }]);

        $hasStudents = $guardian->students->isNotEmpty();
        $hasActiveStudents = $guardian->students->where('status', 'diterima')->isNotEmpty();
        $hasPendingStudents = $guardian->students->where('status', 'menunggu')->isNotEmpty();
        $totalUnpaid = 0;

        if ($hasActiveStudents) {
            foreach ($guardian->students as $student) {
                foreach ($student->billings as $bill) {
                    if ($bill->status === 'UNPAID') {
                        $totalUnpaid += $bill->final_amount;
                    }
                }
            }
        }

        $spmbCategory = FeeCategory::where('code', 'SPMB')->first();
        $spmbFeeMasters = collect();

        if ($spmbCategory) {
            $spmbFeeMasters = FeeMaster::where('fee_category_id', $spmbCategory->id)
                ->where('is_active', true)
                ->get();
        }

        $activeSpmbSchedules = SpmbSchedule::where('is_active', true)
            ->orderBy('registration_start', 'desc')
            ->get()
            ->unique('name');

        $schedulesWithStudents = [];
        foreach ($activeSpmbSchedules as $schedule) {
            $studentsInSchedule = Student::where('guardian_id', $guardian->id)
                ->where('created_at', '>=', $schedule->registration_start)
                ->where('created_at', '<=', $schedule->registration_end->addDay()) // Include end date
                ->get();

            $schedulesWithStudents[] = [
                'schedule' => $schedule,
                'students' => $studentsInSchedule
            ];
        }

        $hasRejectedStudents = $guardian->students->where('status', 'ditolak')->isNotEmpty();

        return view('livewire.guardian-dashboard', [
            'guardian' => $guardian,
            'totalUnpaid' => $totalUnpaid,
            'hasStudents' => $hasStudents,
            'hasActiveStudents' => $hasActiveStudents,
            'hasPendingStudents' => $hasPendingStudents,
            'hasRejectedStudents' => $hasRejectedStudents,
            'spmbFeeMasters' => $spmbFeeMasters,
            'schedulesWithStudents' => $schedulesWithStudents,
        ])->layout('layouts.guardian');
    }
}
