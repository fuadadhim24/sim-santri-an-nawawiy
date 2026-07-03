<?php

namespace App\Livewire;

use App\Models\SpmbSchedule;
use Livewire\Component;
use SweetAlert2\Laravel\Swal;

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
            Swal::error([
                'title' => 'Gagal Menghapus',
                'text' => "Jadwal SPMB '{$schedule->name}' tidak dapat dihapus karena sudah memiliki santri yang terdaftar.",
            ]);
            return;
        }

        $schedule->delete();
        Swal::success([
            'title' => 'Berhasil',
            'text' => 'Jadwal SPMB berhasil dihapus.',
        ]);
    }

    public function toggleActive($id)
    {
        $schedule = SpmbSchedule::findOrFail($id);
        $schedule->update(['is_active' => !$schedule->is_active]);
        Swal::success([
            'title' => 'Berhasil',
            'text' => 'Status jadwal SPMB berhasil diperbarui.',
        ]);
    }
}
