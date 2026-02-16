<?php

namespace App\Livewire;

use App\Models\Guardian;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Livewire\Attributes\Rule;
use Livewire\Component;

class GuardianForm extends Component
{
    public ?Guardian $guardian = null;

    #[Rule('required|min:3')]
    public $full_name = '';

    #[Rule('required|numeric|unique:guardians,whatsapp')]
    public $whatsapp = '';

    #[Rule('required|email|unique:users,email')]
    public $email = '';

    #[Rule('nullable|min:6')]
    public $password = '';

    public $isEdit = false;

    public function mount($guardian = null)
    {
        if ($guardian) {
            $this->guardian = $guardian;
            $this->full_name = $guardian->full_name;
            $this->whatsapp = $guardian->whatsapp;
            $this->email = $guardian->user->email;
            $this->isEdit = true;
        }
    }

    public function save()
    {
        $this->validate([
            'email' => 'required|email|unique:users,email,' . ($this->guardian ? $this->guardian->user_id : 'NULL'),
            'whatsapp' => 'required|numeric|unique:guardians,whatsapp,' . ($this->guardian ? $this->guardian->id : 'NULL'),
            'full_name' => 'required|min:3',
            'password' => $this->isEdit ? 'nullable|min:6' : 'required|min:6',
        ]);

        if ($this->isEdit) {
            $user = $this->guardian->user;
            $user->update([
                'name' => $this->full_name, // Sync name
                'email' => $this->email,
            ]);

            if (!empty($this->password)) {
                $user->update(['password' => Hash::make($this->password)]);
            }

            $this->guardian->update([
                'full_name' => $this->full_name,
                'whatsapp' => $this->whatsapp,
            ]);

            session()->flash('message', 'Guardian updated successfully.');
        } else {
            $user = User::create([
                'name' => $this->full_name,
                'email' => $this->email,
                'password' => Hash::make($this->password),
                'role' => 'WALI_SANTRI',
            ]);

            Guardian::create([
                'user_id' => $user->id,
                'full_name' => $this->full_name,
                'whatsapp' => $this->whatsapp,
            ]);

            session()->flash('message', 'Guardian created successfully.');
        }

        return redirect()->route('admin.guardians');
    }

    public function render()
    {
        return view('livewire.guardian-form')->layout('layouts.admin');
    }
}
