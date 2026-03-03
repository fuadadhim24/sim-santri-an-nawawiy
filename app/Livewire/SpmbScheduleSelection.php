<?php

namespace App\Livewire;

use App\Models\Guardian;
use App\Models\Student;
use App\Models\SpmbSchedule;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class SpmbScheduleSelection extends Component
{
    public $schedules;
    public $selectedSchedule;

    public function mount()
    {
        $this->loadActiveSchedules();
    }

    public function loadActiveSchedules()
    {
        $guardian = Guardian::where('user_id', Auth::id())->first();

        $this->schedules = SpmbSchedule::where('is_active', true)
            ->get()
            ->filter(function ($schedule) {
                return $schedule->isOpen();
            })
            ->map(function ($schedule) use ($guardian) {
                $schedule->registered_students = Student::where('guardian_id', $guardian->id ?? 0)
                    ->where('spmb_schedule_id', $schedule->id)
                    ->get();

                return $schedule;
            });
    }

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
        $this->loadActiveSchedules();

        return view('livewire.spmb-schedule-selection')->layout('layouts.guardian');
    }
}
