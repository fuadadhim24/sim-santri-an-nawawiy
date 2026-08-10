<?php

namespace App\Livewire;

use App\Models\Guardian;
use App\Enums\StudentStatus;
use Livewire\Component;
use Livewire\WithPagination;
use SweetAlert2\Laravel\Swal;

class GuardianIndex extends Component
{
    use WithPagination;

    public $search = '';
    public $filterNoStudents = false;

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingFilterNoStudents()
    {
        $this->resetPage();
    }

    public function deleteSingle($id)
    {
        $guardian = Guardian::with('students')->findOrFail($id);
        $hasActiveOrPendingStudents = $guardian->students()
            ->where('status', '!=', StudentStatus::REJECTED->value)
            ->exists();

        if (!$hasActiveOrPendingStudents) {
            $user = $guardian->user;
            $guardian->forceDelete();
            if ($user) {
                $user->delete();
            }
            Swal::success([
                'title' => 'Berhasil',
                'text' => "Wali santri {$guardian->full_name} berhasil dihapus.",
            ]);
        } else {
            Swal::error([
                'title' => 'Gagal Menghapus',
                'text' => "Tidak dapat menghapus wali santri {$guardian->full_name} karena masih memiliki santri terdaftar.",
            ]);
        }
    }

    public function deleteAllWithoutStudents()
    {
        $guardians = Guardian::whereDoesntHave('students', function ($q) {
            $q->where('status', '!=', StudentStatus::REJECTED->value);
        })->get();
        $deletedCount = 0;
        foreach ($guardians as $guardian) {
            $user = $guardian->user;
            $guardian->forceDelete();
            if ($user) {
                $user->delete();
            }
            $deletedCount++;
        }

        Swal::success([
            'title' => 'Berhasil',
            'text' => "Berhasil menghapus {$deletedCount} wali santri tanpa santri.",
        ]);
    }

    public function getHasGuardiansWithoutStudentsProperty()
    {
        return Guardian::whereDoesntHave('students', function ($q) {
            $q->where('status', '!=', StudentStatus::REJECTED->value);
        })->exists();
    }

    public function render()
    {
        $query = Guardian::with(['user', 'students']);

        if ($this->filterNoStudents) {
            $query->whereDoesntHave('students', function ($q) {
                $q->where('status', '!=', StudentStatus::REJECTED->value);
            });
        }

        if (!empty($this->search)) {
            $query->where(function ($q) {
                $q->where('full_name', 'like', '%' . $this->search . '%')
                  ->orWhere('whatsapp', 'like', '%' . $this->search . '%');
            });
        }

        return view('livewire.guardian-index', [
            'guardians' => $query->paginate(10),
        ])->layout('layouts.admin');
    }
}
