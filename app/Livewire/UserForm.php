<?php

namespace App\Livewire;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Livewire\Attributes\Rule;
use Livewire\Component;

class UserForm extends Component
{
    public ?User $user = null;

    #[Rule('required|min:3')]
    public $name = '';

    #[Rule('required|email|unique:users,email')]
    public $email = '';

    #[Rule('nullable|min:6')]
    public $password = '';

    #[Rule('required|in:SUPER_ADMIN,ADMIN_TU,WALI_SANTRI')]
    public $role = 'ADMIN_TU';

    public $isEdit = false;

    public function mount($user = null)
    {
        if ($user) {
            $this->user = $user;
            $this->name = $user->name;
            $this->email = $user->email;
            $this->role = $user->role;
            $this->isEdit = true;
        }
    }

    public function save()
    {
        $this->validate([
            'email' => 'required|email|unique:users,email,' . ($this->user ? $this->user->id : 'NULL'),
            'name' => 'required|min:3',
            'password' => $this->isEdit ? 'nullable|min:6' : 'required|min:6',
            'role' => 'required|in:SUPER_ADMIN,ADMIN_TU,WALI_SANTRI',
        ]);

        $data = [
            'name' => $this->name,
            'email' => $this->email,
            'role' => $this->role,
        ];

        if (!empty($this->password)) {
            $data['password'] = Hash::make($this->password);
        }

        if ($this->isEdit) {
            $this->user->update($data);
            session()->flash('message', 'User updated successfully.');
        } else {
            $data['password'] = Hash::make($this->password);
            User::create($data);
            session()->flash('message', 'User created successfully.');
        }

        return redirect()->route('admin.users');
    }

    public function render()
    {
        return view('livewire.user-form')->layout('layouts.admin');
    }
}
