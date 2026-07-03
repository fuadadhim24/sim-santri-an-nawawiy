<?php

namespace App\Livewire;

use App\Models\Guardian;
use Livewire\Component;
use Livewire\WithPagination;

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
        if ($guardian->students()->count() === 0) {
            $user = $guardian->user;
            $guardian->forceDelete();
            if ($user) {
                $user->delete();
            }
            session()->flash('message', "Wali santri {$guardian->full_name} berhasil dihapus.");
        } else {
            session()->flash('error', "Tidak dapat menghapus wali santri {$guardian->full_name} karena masih memiliki santri terdaftar.");
        }
    }

    public function deleteAllWithoutStudents()
    {
        $guardians = Guardian::doesntHave('students')->get();
        $deletedCount = 0;
        foreach ($guardians as $guardian) {
            $user = $guardian->user;
            $guardian->forceDelete();
            if ($user) {
                $user->delete();
            }
            $deletedCount++;
        }

        session()->flash('message', "Berhasil menghapus {$deletedCount} wali santri tanpa santri.");
    }

    public function getHasGuardiansWithoutStudentsProperty()
    {
        return Guardian::doesntHave('students')->exists();
    }

    public function render()
    {
        $query = Guardian::with(['user', 'students']);

        if ($this->filterNoStudents) {
            $query->doesntHave('students');
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
