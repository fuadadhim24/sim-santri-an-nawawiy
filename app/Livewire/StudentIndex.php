<?php

namespace App\Livewire;

use App\Enums\StudentStatus;
use App\Models\Student;
use Livewire\Component;
use Livewire\WithPagination;

class StudentIndex extends Component
{
    use WithPagination;

    public $search = '';

    public function render()
    {
        $query = Student::with('guardian')
            ->whereNotIn('status', [StudentStatus::PENDING->value, StudentStatus::REJECTED->value]);

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('full_name', 'like', '%' . $this->search . '%')
                    ->orWhere('nis', 'like', '%' . $this->search . '%')
                    ->orWhere('registration_number', 'like', '%' . $this->search . '%');
            });
        }

        return view('livewire.student-index', [
            'students' => $query->latest()->paginate(5),
        ])->layout('layouts.admin');
    }
}
