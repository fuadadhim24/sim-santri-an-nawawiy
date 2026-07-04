<?php

namespace App\Livewire;

use App\Enums\StudentStatus;
use App\Models\Guardian;
use App\Models\SpmbSchedule;
use App\Models\Student;
use App\Services\NisGeneratorService;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Rule;
use Livewire\Component;
use Livewire\WithFileUploads;

class SpmbStudentRegistration extends Component
{
    use WithFileUploads;
    public $selectedSchedule;
    public $scheduleId;

    #[Rule('required|min:3')]
    public $full_name = '';

    #[Rule('required|in:01,02,03')]
    public $unit_code = '01';

    #[Rule('required|in:MONDOK,NON_MONDOK,NGAJI_ONLY')]
    public $residence_status = 'MONDOK';

    #[Rule('required|in:UMUM,ANAK_GURU,YATIM')]
    public $special_status = 'UMUM';

    #[Rule('required|exists:class_levels,id')]
    public $class_level_id = '';

    #[Rule('nullable|string')]
    public $address = '';

    #[Rule('required|file|mimes:jpg,jpeg,png,webp,pdf|max:2048')]
    public $kk = null;

    #[Rule('required|file|mimes:jpg,jpeg,png,webp|max:1024')]
    public $foto = null;

    #[Rule('nullable|file|mimes:jpg,jpeg,png,webp,pdf|max:2048')]
    public $nisn_document = null;

    #[Rule('required|file|mimes:jpg,jpeg,png,webp,pdf|max:2048')]
    public $akta = null;

    #[Rule('required|file|mimes:jpg,jpeg,png,webp,pdf|max:2048')]
    public $ijazah = null;

    public function mount()
    {
        $this->scheduleId = session('selected_spmb_schedule_id');

        if (!$this->scheduleId) {
            return redirect()->route('wali.spmb-schedules')
                ->with('error', 'Silakan pilih jadwal pendaftaran terlebih dahulu.');
        }

        $this->selectedSchedule = SpmbSchedule::find($this->scheduleId);

        if (!$this->selectedSchedule || !$this->selectedSchedule->isOpen()) {
            return redirect()->route('wali.spmb-schedules')
                ->with('error', 'Jadwal pendaftaran tidak tersedia atau sudah ditutup.');
        }
    }

    /**
     * Reset specific file input field
     */
    public function removeFile($field)
    {
        $this->$field = null;
    }

    public function getGuardianProperty()
    {
        $user = Auth::user();
        return Guardian::where('user_id', $user->id)->first();
    }

    public function getClassLevelsProperty()
    {
        return \App\Models\ClassLevel::orderBy('level_order')->get();
    }

    public function save(NisGeneratorService $nisService)
    {
        $this->validate();

        $guardian = $this->guardian;

        if (!$guardian) {
            session()->flash('error', 'Data wali santri tidak ditemukan.');
            return;
        }

        $year = date('Y');
        $regNumber = $nisService->generateRegistrationNumber($year);

        $kkPath = $this->kk ? $this->kk->store('student-documents/kk', 'public') : null;
        $fotoPath = $this->foto ? $this->foto->store('student-documents/foto', 'public') : null;
        $nisnDocPath = $this->nisn_document ? $this->nisn_document->store('student-documents/nisn', 'public') : null;
        $aktaPath = $this->akta ? $this->akta->store('student-documents/akta', 'public') : null;
        $ijazahPath = $this->ijazah ? $this->ijazah->store('student-documents/ijazah', 'public') : null;

        $data = [
            'guardian_id' => $guardian->id,
            'spmb_schedule_id' => $this->scheduleId,
            'full_name' => $this->full_name,
            'unit_code' => $this->unit_code,
            'residence_status' => $this->residence_status,
            'special_status' => $this->special_status,
            'class_level_id' => $this->class_level_id,
            'address' => $this->address,
            'nis' => null,
            'registration_number' => $regNumber,
            'is_active' => false,
            'status' => StudentStatus::PENDING->value,
            'kk' => $kkPath,
            'foto' => $fotoPath,
            'nisn_document' => $nisnDocPath,
            'akta' => $aktaPath,
            'ijazah' => $ijazahPath,
        ];

        $newStudent = Student::create($data);

        session()->forget('selected_spmb_schedule_id');
        session()->forget('selected_spmb_schedule_name');

        session()->flash('message', 'Pendaftaran santri baru berhasil! No. Pendaftaran: ' . $regNumber . '. Silakan tunggu konfirmasi dari admin.');

        return redirect()->route('wali.dashboard');
    }

    public function render()
    {
        return view('livewire.spmb-student-registration')->layout('layouts.guardian');
    }
}
