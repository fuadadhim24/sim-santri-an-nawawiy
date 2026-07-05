<?php

namespace App\Livewire;

use App\Enums\StudentStatus;
use App\Models\Student;
use App\Models\SpmbSchedule;
use Livewire\Component;
use Livewire\WithPagination;

class StudentAcceptance extends Component
{
    use WithPagination;

    protected $listeners = ['refreshStudentAcceptance' => '$refresh'];

    public $search = '';

    public function render()
    {
        $query = Student::with(['guardian', 'spmbSchedule'])
            ->where('status', StudentStatus::PENDING->value)
            ->where('is_active', false); // only show those not yet activated

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('full_name', 'like', '%' . $this->search . '%')
                    ->orWhere('registration_number', 'like', '%' . $this->search . '%');
            });
        }

        $students = $query->latest()->get();

        // Group students by SPMB schedule and convert to array
        $studentsBySchedule = $students->groupBy('spmb_schedule_id')->map(function ($group) {
            return $group->all();
        })->all();

        // Get all SPMB schedules
        $spmbSchedules = SpmbSchedule::whereIn('id', array_keys($studentsBySchedule))->get();

        return view('livewire.student-acceptance', [
            'studentsBySchedule' => $studentsBySchedule,
            'spmbSchedules' => $spmbSchedules,
        ])->layout('layouts.admin');
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }
}
