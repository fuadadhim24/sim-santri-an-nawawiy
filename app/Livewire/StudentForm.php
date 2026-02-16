<?php

namespace App\Livewire;

use App\Models\Guardian;
use App\Models\Student;
use App\Services\NisGeneratorService;
use Livewire\Attributes\Rule;
use Livewire\Component;

class StudentForm extends Component
{
    public ?Student $student = null;

    #[Rule('required|exists:guardians,id')]
    public $guardian_id = '';

    #[Rule('required|min:3')]
    public $full_name = '';

    #[Rule('required|in:01,02,03')]
    public $unit_code = '01';

    #[Rule('required|in:MONDOK,NON_MONDOK,NGAJI_ONLY')]
    public $residence_status = 'MONDOK';

    #[Rule('required|in:UMUM,ANAK_GURU,YATIM')]
    public $special_status = 'UMUM';

    #[Rule('nullable|string')]
    public $class_name = '';

    #[Rule('nullable|string')]
    public $address = '';

    #[Rule('boolean')]
    public $is_active = true;

    public $generatedNis = null;
    public $isEdit = false;

    // Computed property for guardians to avoid passing it to view every time
    public function getGuardiansProperty()
    {
        return Guardian::orderBy('full_name')->get();
    }

    public function mount(Student $student = null)
    {
        if ($student && $student->exists) {
            $this->student = $student;
            $this->guardian_id = $student->guardian_id;
            $this->full_name = $student->full_name;
            $this->unit_code = $student->unit_code;
            $this->residence_status = $student->residence_status;
            $this->special_status = $student->special_status;
            $this->class_name = $student->class_name;
            $this->address = $student->address;
            $this->is_active = $student->is_active;
            $this->generatedNis = $student->nis;
            $this->isEdit = true;
        }
    }

    public function save(NisGeneratorService $nisService)
    {
        $this->validate();

        $data = [
            'guardian_id' => $this->guardian_id,
            'full_name' => $this->full_name,
            'unit_code' => $this->unit_code,
            'residence_status' => $this->residence_status,
            'special_status' => $this->special_status,
            'class_name' => $this->class_name,
            'address' => $this->address,
            'is_active' => $this->is_active,
        ];

        if ($this->isEdit) {
            $this->student->update($data);
            session()->flash('message', 'Student updated successfully.');
        } else {
            // Generate NIS
            // Year based on current year for now, or could be input. Assuming 2026 as per examples.
            // In a real app, this might come from an academic year setting.
            // Using current year for simplicity or 2026 if hardcoded in examples.
            // Let's use current year or 2026. user prompt used 2026.
            $year = 2026;
            $nis = $nisService->generate($this->unit_code, $year);
            $data['nis'] = $nis;

            Student::create($data);
            session()->flash('message', 'Student created successfully with NIS: ' . $nis);
        }

        return redirect()->route('admin.students');
    }

    public function render()
    {
        return view('livewire.student-form')->layout('layouts.admin');
    }
}
