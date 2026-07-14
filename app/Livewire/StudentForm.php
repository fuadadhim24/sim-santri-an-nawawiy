<?php

namespace App\Livewire;

use App\Models\FeeCategory;
use App\Models\FeeMaster;
use App\Models\Guardian;
use App\Models\Student;
use App\Models\ClassLevel;
use App\Services\NisGeneratorService;
use Livewire\Attributes\Rule;
use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\DB;

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

    #[Rule('required|in:UMUM,ANAK_GURU,YATIM,PRESTASI,LINGKUNGAN')]
    public $special_status = 'UMUM';

    #[Rule('required|exists:class_levels,id')]
    public $class_level_id = '';

    #[Rule('nullable|string')]
    public $address = '';

    #[Rule('nullable|numeric|digits:10')]
    public $nisn = '';

    public $nis = '';
    public $nisCheckStatus = null; // 'available', 'taken', 'empty'
    public $nisCheckMessage = '';

    public $kk_file;
    public $foto_file;
    public $akta_file;
    public $ijazah_file;
    public $nisn_document_file;

    public $availableBillings = [];
    public $selectedBillings = [];
    public $generatedNis = null;
    public $isEdit = false;
    public $is_active = true;
    public $autoGenerateBillings = true;

    public $isAcademicLocked = false;
    public $showUnlockModal = false;
    public $academicChangesConfirmed = true;

    public $showTransitionModal = false; // Controls display of Before-After Preview card
    public $oldUnpaidPolicy = 'keep_all'; // 'delete_all', 'delete_except_current_month', 'keep_all', 'delete_selected'
    public $oldBillings = [];
    public $oldBillingsToDelete = [];
    public $newCategoriesToGenerate = [];
    public $availableNewBillings = [];

    public function getGuardiansProperty()
    {
        return Guardian::orderBy('full_name')->get();
    }

    public function getClassLevelsProperty()
    {
        return ClassLevel::orderBy('level_order')->get();
    }

    public function loadAvailableBillings()
    {
        if ($this->isEdit) {
            $this->availableBillings = [];
            return;
        }

        $query = FeeCategory::where('is_active', true);

        $query->where(function ($q) {
            $q->where('domicile_target', $this->residence_status)
              ->orWhereNull('domicile_target');
        });

        $query->where(function ($q) {
            $q->where('unit_target', $this->unit_code)
              ->orWhereNull('unit_target');
        });

        $this->availableBillings = $query->where('is_locked', false)
            ->with(['fees' => function ($q) {
                $q->with('classLevelTarget')
                  ->where('is_active', true)
                  ->where(function ($sq) {
                      $sq->whereNull('unit_target')
                         ->orWhere('unit_target', $this->unit_code);
                  })
                  ->where(function ($sq) {
                      $sq->whereNull('residence_target')
                         ->orWhere('residence_target', $this->residence_status);
                  })
                  ->where(function ($sq) {
                      $sq->whereNull('class_level_target_id')
                         ->orWhere('class_level_target_id', $this->class_level_id ?: null);
                  });
            }])
            ->orderBy('name')
            ->get()
            ->map(function ($category) {
                return [
                    'id' => (string) $category->id,
                    'name' => $category->name,
                    'description' => $category->description,
                    'unit' => $category->unit_target,
                    'domicile' => $category->domicile_target,
                    'fees' => $category->fees->map(function ($fee) {
                        return [
                            'item_name' => $fee->item_name,
                            'amount' => $fee->amount,
                            'recurrence_type' => $fee->recurrence_type,
                            'due_days' => $fee->due_days,
                            'unit' => $fee->unit_target,
                            'domicile' => $fee->residence_target,
                            'class_level_target_id' => $fee->class_level_target_id,
                            'class_level_target_name' => $fee->classLevelTarget?->name ?? 'SEMUA',
                        ];
                    })->toArray(),
                    'total_amount' => $category->fees->sum('amount')
                ];
            })
            ->toArray();

        $this->selectedBillings = array_map(fn($b) => $b['id'], $this->availableBillings);
    }

    public function loadAvailableNewBillings()
    {
        $query = FeeCategory::where('is_active', true);

        $query->where(function ($q) {
            $q->where('domicile_target', $this->residence_status)
              ->orWhereNull('domicile_target');
        });

        $query->where(function ($q) {
            $q->where('unit_target', $this->unit_code)
              ->orWhereNull('unit_target');
        });

        $this->availableNewBillings = $query->where('is_locked', false)
            ->with(['fees' => function ($q) {
                $q->with('classLevelTarget')
                  ->where('is_active', true)
                  ->where(function ($sq) {
                      $sq->whereNull('unit_target')
                         ->orWhere('unit_target', $this->unit_code);
                  })
                  ->where(function ($sq) {
                      $sq->whereNull('residence_target')
                         ->orWhere('residence_target', $this->residence_status);
                  })
                  ->where(function ($sq) {
                      $sq->whereNull('class_level_target_id')
                         ->orWhere('class_level_target_id', $this->class_level_id ?: null);
                  });
            }])
            ->orderBy('name')
            ->get()
            ->map(function ($category) {
                return [
                    'id' => (string) $category->id,
                    'name' => $category->name,
                    'description' => $category->description,
                    'unit' => $category->unit_target,
                    'domicile' => $category->domicile_target,
                    'fees' => $category->fees->map(function ($fee) {
                        return [
                            'item_name' => $fee->item_name,
                            'amount' => $fee->amount,
                            'recurrence_type' => $fee->recurrence_type,
                            'due_days' => $fee->due_days,
                            'unit' => $fee->unit_target,
                            'domicile' => $fee->residence_target,
                            'class_level_target_id' => $fee->class_level_target_id,
                            'class_level_target_name' => $fee->classLevelTarget?->name ?? 'SEMUA',
                        ];
                    })->toArray(),
                    'total_amount' => $category->fees->sum('amount')
                ];
            })
            ->toArray();

        $this->newCategoriesToGenerate = array_map(fn($b) => $b['id'], $this->availableNewBillings);
    }

    public function getIncompleteAcademicFieldsProperty()
    {
        $incomplete = [];
        if (empty($this->unit_code)) {
            $incomplete[] = 'Unit Sekolah';
        }
        if (empty($this->residence_status)) {
            $incomplete[] = 'Status Domisili';
        }
        if (empty($this->special_status)) {
            $incomplete[] = 'Status Khusus';
        }
        if (empty($this->class_level_id)) {
            $incomplete[] = 'Tingkat Kelas';
        }
        return $incomplete;
    }

    public function checkIfProfileChanged()
    {
        if (!$this->isEdit || !$this->student) {
            return;
        }

        $isProfileChanged = (
            $this->student->unit_code !== $this->unit_code ||
            $this->student->residence_status !== $this->residence_status ||
            $this->student->class_level_id !== ($this->class_level_id ? (int)$this->class_level_id : null) ||
            $this->student->special_status !== $this->special_status
        );

        $incomplete = $this->incompleteAcademicFields;

        if ($isProfileChanged && $this->academicChangesConfirmed && empty($incomplete)) {
            $this->showTransitionModal = true;
            $this->loadAvailableNewBillings();
        } else {
            $this->showTransitionModal = false;
            $this->availableNewBillings = [];
        }
    }

    public function triggerUnlock()
    {
        $this->oldBillings = app(\App\Services\BillingService::class)->getUnpaidBillings($this->student)->toArray();
        $this->dispatch('swal:choose-unlock-policy', ['oldBillings' => $this->oldBillings]);
    }

    public function confirmUnlock()
    {
        $this->isAcademicLocked = false;
        $this->academicChangesConfirmed = true;
        $this->showUnlockModal = false;
        
        $this->oldBillings = app(\App\Services\BillingService::class)->getUnpaidBillings($this->student)->toArray();
        $this->checkIfProfileChanged();
    }

    public function isBillingDeleted($billingId, $dueDate, $createdAt)
    {
        if ($this->oldUnpaidPolicy === 'delete_all') {
            return true;
        }
        if ($this->oldUnpaidPolicy === 'delete_except_current_month') {
            $dueDateCarbon = $dueDate ? \Carbon\Carbon::parse($dueDate) : null;
            $createdAtCarbon = \Carbon\Carbon::parse($createdAt);
            $startOfMonth = now()->startOfMonth();
            $endOfMonth = now()->endOfMonth();
            if (($dueDateCarbon && $dueDateCarbon->between($startOfMonth, $endOfMonth)) || $createdAtCarbon->between($startOfMonth, $endOfMonth)) {
                return false;
            }
            return true;
        }
        if ($this->oldUnpaidPolicy === 'delete_selected') {
            return in_array((string)$billingId, array_map('strval', $this->oldBillingsToDelete));
        }
        return false;
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
            $this->class_level_id = $student->class_level_id ?? '';
            $this->address = $student->address;
            $this->nisn = $student->nisn;
            $this->generatedNis = $student->nis;
            $this->nis = $student->nis;
            $this->isEdit = true;
            $this->isAcademicLocked = false;
            $this->academicChangesConfirmed = true;
        } else {
            $this->isAcademicLocked = false;
            $this->academicChangesConfirmed = true;
            $this->loadAvailableBillings();

            $year = date('Y');
            $nisService = app(\App\Services\NisGeneratorService::class);
            $this->nis = $nisService->generate($this->unit_code, $year);
        }
    }

    public function updatedUnitCode()
    {
        if (!$this->isEdit) {
            $this->loadAvailableBillings();

            $year = date('Y');
            $nisService = app(\App\Services\NisGeneratorService::class);
            $this->nis = $nisService->generate($this->unit_code, $year);
            $this->nisCheckStatus = null;
            $this->nisCheckMessage = '';
        } else {
            $this->checkIfProfileChanged();
        }
    }

    public function updatedResidenceStatus()
    {
        if (!$this->isEdit) {
            $this->loadAvailableBillings();
        } else {
            $this->checkIfProfileChanged();
        }
    }

    public function updatedClassLevelId()
    {
        if (!$this->isEdit) {
            $this->loadAvailableBillings();
        } else {
            $this->checkIfProfileChanged();
        }
    }

    public function updatedSpecialStatus()
    {
        if ($this->isEdit) {
            $this->checkIfProfileChanged();
        }
    }

    public function toggleSelectAllFees()
    {
        if (count($this->selectedBillings) === count($this->availableBillings)) {
            $this->selectedBillings = [];
        } else {
            $this->selectedBillings = array_map(fn($b) => $b['id'], $this->availableBillings);
        }
    }

    public function updatedAutoGenerateBillings()
    {
        if (!$this->isEdit) {
            if (!$this->autoGenerateBillings) {
                $this->selectedBillings = [];
            } else {
                $this->selectedBillings = array_map(fn($b) => $b['id'], $this->availableBillings);
            }
        }
    }

    public function save(NisGeneratorService $nisService, \App\Services\BillingService $billingService)
    {
        try {
            $this->validate();
            $nisRule = 'required|string|unique:students,nis' . ($this->isEdit ? ',' . $this->student->id : '');
            $this->validate([
                'nis' => $nisRule,
            ]);
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
        } catch (\Illuminate\Validation\ValidationException $e) {
            $this->dispatch('swal:validation-error', [
                'errors' => $e->validator->errors()->all()
            ]);
            throw $e;
        }

        $data = [
            'guardian_id' => $this->guardian_id,
            'full_name' => $this->full_name,
            'nis' => $this->nis,
            'unit_code' => $this->unit_code,
            'residence_status' => $this->residence_status,
            'special_status' => $this->special_status,
            'class_level_id' => $this->class_level_id ?: null,
            'address' => $this->address,
            'nisn' => $this->nisn,
            'is_active' => $this->is_active,
        ];

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
            $data['registration_number'] = $nisService->generateRegistrationNumber($year);
            $data['status'] = \App\Enums\StudentStatus::ACCEPTED->value;
            $data['joined_at'] = now();

            $newStudent = Student::create($data);

            if ($this->autoGenerateBillings && !empty($this->selectedBillings)) {
                $billingService->generateBillingsForStudentWithCategories($newStudent, $this->selectedBillings);
            }

            session()->flash('message', 'Student created successfully with NIS: ' . $this->nis);
        }

        return redirect()->route('admin.students');
    }

    public function checkNis()
    {
        $this->nis = trim($this->nis);
        
        if (empty($this->nis)) {
            $this->nisCheckStatus = 'empty';
            $this->nisCheckMessage = 'NIS tidak boleh kosong.';
            return;
        }

        $query = Student::where('nis', $this->nis);
        if ($this->student && $this->student->id) {
            $query->where('id', '!=', $this->student->id);
        }
        $existingStudent = $query->first();

        if ($existingStudent) {
            $this->nisCheckStatus = 'taken';
            $this->nisCheckMessage = 'NIS sudah digunakan oleh ' . $existingStudent->full_name . ' (' . ($existingStudent->classLevel?->name ?? 'Kelas tidak diketahui') . ').';
        } else {
            $this->nisCheckStatus = 'available';
            $this->nisCheckMessage = 'NIS tersedia dan belum digunakan.';
        }
    }

    public function updatedNis()
    {
        $this->nisCheckStatus = null;
        $this->nisCheckMessage = '';
    }

    public function render()
    {
        return view('livewire.student-form')->layout('layouts.admin');
    }
}
