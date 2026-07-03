<?php

namespace App\Livewire;

use App\Models\SpmbSchedule;
use Livewire\Component;

class SpmbScheduleIndex extends Component
{
    public $schedules;

    public function render()
    {
        $this->schedules = SpmbSchedule::orderBy('registration_start', 'desc')->get();
        return view('livewire.spmb-schedule-index')->layout('layouts.admin');
    }

    public function delete($id)
    {
        $schedule = SpmbSchedule::findOrFail($id);

        if ($schedule->students()->exists()) {
            session()->flash('error', "Jadwal SPMB '{$schedule->name}' tidak dapat dihapus karena sudah memiliki santri yang terdaftar.");
            return;
        }

        $schedule->delete();
        session()->flash('message', 'Jadwal SPMB berhasil dihapus.');
    }

    public function toggleActive($id)
    {
        $schedule = SpmbSchedule::findOrFail($id);
        $schedule->update(['is_active' => !$schedule->is_active]);
        session()->flash('message', 'Status jadwal SPMB berhasil diperbarui.');
    }
}
