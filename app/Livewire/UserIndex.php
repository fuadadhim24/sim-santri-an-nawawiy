<?php

namespace App\Livewire;

use App\Models\User;
use Livewire\Component;
use Livewire\WithPagination;

class UserIndex extends Component
{
    use WithPagination;

    public $search = '';

    public function toggleActive($userId)
    {
        // Prevent users from deactivating themselves
        if (auth()->id() === (int) $userId) {
            session()->flash('error', 'Anda tidak dapat menonaktifkan akun Anda sendiri.');
            return;
        }

        $user = User::findOrFail($userId);
        $user->is_active = !$user->is_active;
        $user->save();

        session()->flash('message', 'Status pengguna ' . $user->name . ' berhasil diperbarui.');
    }

    public function render()
    {
        return view('livewire.user-index', [
            'users' => User::where('role', '!=', 'WALI_SANTRI')
                ->where(function ($query) {
                    $query->where('name', 'like', '%' . $this->search . '%')
                          ->orWhere('email', 'like', '%' . $this->search . '%');
                })
                ->paginate(5),
        ])->layout('layouts.admin');
    }
}
