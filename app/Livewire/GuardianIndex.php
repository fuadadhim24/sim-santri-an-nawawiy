<?php

namespace App\Livewire;

use App\Models\Guardian;
use Livewire\Component;
use Livewire\WithPagination;

class GuardianIndex extends Component
{
    use WithPagination;

    public $search = '';

    public function render()
    {
        return view('livewire.guardian-index', [
            'guardians' => Guardian::with('user')
                ->where('full_name', 'like', '%' . $this->search . '%')
                ->orWhere('whatsapp', 'like', '%' . $this->search . '%')
                ->paginate(5),
        ])->layout('layouts.admin');
    }
}
