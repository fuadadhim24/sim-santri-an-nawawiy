<?php

namespace App\Livewire;

use App\Models\Student;
use App\Models\Guardian;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class StudentDetail extends Component
{
    public Student $student;

    public function mount(Student $student)
    {
        $this->student = $student->load(['guardian.user', 'billings']);

        // Check if the current user is a guardian and owns this student
        if (Auth::user()->role === 'WALI_SANTRI') {
            $guardian = Guardian::where('user_id', Auth::id())->first();
            if (!$guardian || $student->guardian_id !== $guardian->id) {
                abort(403, 'Anda tidak memiliki akses ke data santri ini.');
            }
        }
    }

    public function render()
    {
        // Use different layout based on user role
        $layout = Auth::user()->role === 'WALI_SANTRI' ? 'layouts.guardian' : 'layouts.admin';

        return view('livewire.student-detail')->layout($layout);
    }
}
