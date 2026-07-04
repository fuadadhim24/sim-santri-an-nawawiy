<?php

namespace App\Livewire;

use App\Models\FeeMaster;
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

    #[Rule('nullable|string|max:20')]
    public $nisn = '';

    public $selectedFeeMasters = [];
    public $generatedNis = null;
    public $isEdit = false;
    public $is_active = true;
    public $autoGenerateBillings = true;

    public function getGuardiansProperty()
    {
        return Guardian::orderBy('full_name')->get();
    }

    public function getFeeMastersProperty()
    {
        return FeeMaster::with('category')
            ->where('is_active', true)
            ->orderBy('item_name')
            ->get();
    }

    public function getMatchingFeeMastersProperty()
    {
        if ($this->isEdit) {
            return collect();
        }

        return $this->feeMasters->filter(function ($fee) {
            $unitMatch = empty($fee->unit_target) || str_contains($fee->unit_target, $this->unit_code);
            $residenceMatch = empty($fee->residence_target) || str_contains($fee->residence_target, $this->residence_status);
            return $unitMatch && $residenceMatch;
        });
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
            $this->nisn = $student->nisn;
            $this->generatedNis = $student->nis;
            $this->isEdit = true;
        } else {
            $this->selectedFeeMasters = [];
        }
    }

    public function updatedUnitCode()
    {
        if (!$this->isEdit) {
            $this->selectedFeeMasters = [];
        }
    }

    public function updatedResidenceStatus()
    {
        if (!$this->isEdit) {
            $this->selectedFeeMasters = [];
        }
    }

    public function updatedAutoGenerateBillings()
    {
        if (!$this->isEdit) {
            if (!$this->autoGenerateBillings) {
                $this->selectedFeeMasters = [];
            }
        }
    }

    public function save(NisGeneratorService $nisService, \App\Services\BillingService $billingService)
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
            'nisn' => $this->nisn,
            'is_active' => $this->is_active,
        ];

        if ($this->isEdit) {
            $this->student->update($data);
            session()->flash('message', 'Student updated successfully.');
        } else {
            $year = date('Y');
            $nis = $nisService->generate($this->unit_code, $year);
            $data['nis'] = $nis;
            $data['registration_number'] = $nisService->generateRegistrationNumber($year);
            $data['status'] = \App\Enums\StudentStatus::ACCEPTED->value;
            $data['joined_at'] = now();

            $newStudent = Student::create($data);

            $selectedFeeMasterIds = array_map('intval', $this->selectedFeeMasters);
            if (!empty($selectedFeeMasterIds)) {
                $billingService->generateOnceBillsForSelectedFees($newStudent, $selectedFeeMasterIds);
            }

            session()->flash('message', 'Student created successfully with NIS: ' . $nis);
        }

        return redirect()->route('admin.students');
    }

    public function render()
    {
        return view('livewire.student-form')->layout('layouts.admin');
    }
}
