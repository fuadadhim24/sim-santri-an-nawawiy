<?php

namespace App\Livewire;

use App\Models\User;
use Livewire\Component;
use Livewire\WithPagination;

class UserIndex extends Component
{
    use WithPagination;

    public $search = '';

    public function render()
    {
        return view('livewire.user-index', [
            'users' => User::where('role', '!=', 'WALI_SANTRI')
                ->where(function ($query) {
                    $query->where('name', 'like', '%' . $this->search . '%')
                          ->orWhere('email', 'like', '%' . $this->search . '%');
                })
                ->paginate(10),
        ])->layout('layouts.admin');
    }
}
