<?php

namespace App\Livewire;

use App\Models\FeeMaster;
use App\Models\Guardian;
use App\Models\Student;
use App\Services\NisGeneratorService;
use Livewire\Attributes\Rule;
use Livewire\Component;

use Livewire\WithFileUploads;

class StudentForm extends Component
{
    use WithFileUploads;

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

    #[Rule('nullable|numeric|digits:10')]
    public $nisn = '';

    // File properties
    public $kk_file;
    public $foto_file;
    public $akta_file;
    public $ijazah_file;
    public $nisn_document_file;

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

    public function toggleSelectAllFees()
    {
        $matchingIds = $this->matchingFeeMasters->pluck('id')->map(fn($id) => (string) $id)->toArray();
        $selectedStr = array_map('strval', $this->selectedFeeMasters);

        if (count(array_intersect($selectedStr, $matchingIds)) === count($matchingIds)) {
            $this->selectedFeeMasters = [];
        } else {
            $this->selectedFeeMasters = $matchingIds;
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
        $kkRule = ($this->isEdit && $this->student && $this->student->kk) ? 'nullable' : 'required';
        $fotoRule = ($this->isEdit && $this->student && $this->student->foto) ? 'nullable' : 'required';
        $aktaRule = ($this->isEdit && $this->student && $this->student->akta) ? 'nullable' : 'required';

        $this->validate([
            'kk_file' => "$kkRule|file|mimes:jpg,jpeg,png,webp,pdf|max:2048",
            'foto_file' => "$fotoRule|file|mimes:jpg,jpeg,png,webp|max:2048",
            'akta_file' => "$aktaRule|file|mimes:jpg,jpeg,png,webp,pdf|max:2048",
            'ijazah_file' => 'nullable|file|mimes:jpg,jpeg,png,webp,pdf|max:2048',
            'nisn_document_file' => 'nullable|file|mimes:jpg,jpeg,png,webp,pdf|max:2048',
        ]);

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

        // File handling
        if ($this->kk_file) {
            if ($this->isEdit && $this->student->kk) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($this->student->kk);
            }
            $data['kk'] = $this->kk_file->store('student-documents/kk', 'public');
        }

        if ($this->foto_file) {
            if ($this->isEdit && $this->student->foto) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($this->student->foto);
            }
            $data['foto'] = $this->foto_file->store('student-documents/foto', 'public');
        }

        if ($this->akta_file) {
            if ($this->isEdit && $this->student->akta) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($this->student->akta);
            }
            $data['akta'] = $this->akta_file->store('student-documents/akta', 'public');
        }

        if ($this->ijazah_file) {
            if ($this->isEdit && $this->student->ijazah) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($this->student->ijazah);
            }
            $data['ijazah'] = $this->ijazah_file->store('student-documents/ijazah', 'public');
        }

        if ($this->nisn_document_file) {
            if ($this->isEdit && $this->student->nisn_document) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($this->student->nisn_document);
            }
            $data['nisn_document'] = $this->nisn_document_file->store('student-documents/nisn', 'public');
        }

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
