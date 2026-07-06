<?php

namespace App\Livewire;

use App\Models\Guardian;
use App\Models\Student;
use App\Models\SpmbSchedule;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class SpmbScheduleSelection extends Component
{
    public function selectSchedule($id)
    {
        $schedule = SpmbSchedule::find($id);

        if (!$schedule) {
            session()->flash('error', 'Jadwal tidak ditemukan.');
            return;
        }

        if (!$schedule->isActive() || !$schedule->isOpen()) {
            session()->flash('error', 'Jadwal tidak aktif atau sudah ditutup.');
            return;
        }

        session()->put('selected_spmb_schedule_id', $schedule->id);
        session()->put('selected_spmb_schedule_name', $schedule->name);

        return redirect()->route('wali.spmb.register');
    }

    public function render()
    {
        $guardian = Guardian::where('user_id', Auth::id())->first();
        $guardianId = $guardian?->id ?? 0;

        $schedules = SpmbSchedule::where('is_active', true)
            ->get()
            ->filter(fn($s) => $s->isOpen())
            ->values() // reindex agar tidak ada gap
            ->map(function ($schedule) use ($guardianId) {
                $schedule->registered_students = Student::where('guardian_id', $guardianId)
                    ->where('spmb_schedule_id', $schedule->id)
                    ->get();
                return $schedule;
            });

        return view('livewire.spmb-schedule-selection', [
            'schedules' => $schedules,
        ])->layout('layouts.guardian');
    }
}
