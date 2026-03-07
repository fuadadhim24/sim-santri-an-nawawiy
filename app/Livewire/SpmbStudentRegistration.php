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

class SpmbStudentRegistration extends Component
{
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

    #[Rule('nullable|string')]
    public $address = '';

    #[Rule('required|file|mimes:jpg,jpeg,png,pdf|max:2048')]
    public $kk = null;

    #[Rule('required|file|mimes:jpg,jpeg,png|max:1024')]
    public $foto = null;

    #[Rule('required|file|mimes:jpg,jpeg,png,pdf|max:2048')]
    public $nisn_document = null;

    #[Rule('required|file|mimes:jpg,jpeg,png,pdf|max:2048')]
    public $akta = null;

    #[Rule('required|file|mimes:jpg,jpeg,png,pdf|max:2048')]
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

    public function save(NisGeneratorService $nisService)
    {
        $this->validate();

        $guardian = $this->guardian;

        if (!$guardian) {
            session()->flash('error', 'Data wali santri tidak ditemukan.');
            return;
        }

        $year = date('Y');
        $nis = $nisService->generate($this->unit_code, $year);

        $data = [
            'guardian_id' => $guardian->id,
            'spmb_schedule_id' => $this->scheduleId,
            'full_name' => $this->full_name,
            'unit_code' => $this->unit_code,
            'residence_status' => $this->residence_status,
            'special_status' => $this->special_status,
            'address' => $this->address,
            'nis' => $nis,
            'is_active' => false,
            'status' => StudentStatus::PENDING->value,
            'kk' => $this->kk,
            'foto' => $this->foto,
            'nisn_document' => $this->nisn_document,
            'akta' => $this->akta,
            'ijazah' => $this->ijazah,
        ];

        $newStudent = Student::create($data);

        session()->forget('selected_spmb_schedule_id');
        session()->forget('selected_spmb_schedule_name');

        session()->flash('message', 'Pendaftaran santri baru berhasil! NIS: ' . $nis . '. Silakan tunggu konfirmasi dari admin.');

        return redirect()->route('wali.dashboard');
    }

    public function render()
    {
        return view('livewire.spmb-student-registration')->layout('layouts.guardian');
    }
}
