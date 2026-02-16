<?php

namespace App\Livewire;

use App\Models\Student;
use Livewire\Component;

class StudentDetail extends Component
{
    public Student $student;

    public function mount(Student $student)
    {
        $this->student = $student->load(['guardian.user', 'billings']);
    }

    public function render()
    {
        return view('livewire.student-detail')->layout('layouts.admin');
    }
}
