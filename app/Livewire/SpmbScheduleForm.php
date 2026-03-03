<?php

namespace App\Livewire;

use App\Models\SpmbSchedule;
use Livewire\Attributes\Rule;
use Livewire\Component;

class SpmbScheduleForm extends Component
{
    public ?SpmbSchedule $schedule = null;

    #[Rule('required|string|max:255')]
    public $name = '';

    #[Rule('nullable|string|max:1000')]
    public $description = '';

    #[Rule('required|date|before:registration_end')]
    public $registration_start = '';

    #[Rule('required|date|after:registration_start')]
    public $registration_end = '';

    public $is_active = false;
    public $isEdit = false;

    public function mount($id = null)
    {
        if ($id) {
            $this->schedule = SpmbSchedule::findOrFail($id);
            $this->name = $this->schedule->name;
            $this->description = $this->schedule->description;
            $this->registration_start = $this->schedule->registration_start->format('Y-m-d\TH:i');
            $this->registration_end = $this->schedule->registration_end->format('Y-m-d\TH:i');
            $this->is_active = $this->schedule->is_active;
            $this->isEdit = true;
        }
    }

    public function save()
    {
        $this->validate();

        $data = [
            'name' => $this->name,
            'description' => $this->description,
            'registration_start' => $this->registration_start,
            'registration_end' => $this->registration_end,
            'is_active' => $this->is_active,
        ];

        if ($this->isEdit) {
            $this->schedule->update($data);
            session()->flash('message', 'Jadwal SPMB berhasil diperbarui.');
        } else {
            SpmbSchedule::create($data);
            session()->flash('message', 'Jadwal SPMB berhasil dibuat.');
        }

        return redirect()->route('admin.spmb-schedules');
    }

    public function render()
    {
        return view('livewire.spmb-schedule-form')->layout('layouts.admin');
    }
}
