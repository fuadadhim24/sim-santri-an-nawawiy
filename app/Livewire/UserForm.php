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

    #[Rule('required|string|max:20')]
    public $whatsapp = '';

    #[Rule('nullable|email|unique:users,email')]
    public $email = '';

    #[Rule('nullable|min:6')]
    public $password = '';

    #[Rule('required|in:SUPER_ADMIN,ADMINISTRASI,BENDAHARA')]
    public $role = 'ADMINISTRASI';

    public $is_active = true;

    public $isEdit = false;

    public function mount($user = null)
    {
        if ($user) {
            $this->user = $user;
            $this->name = $user->name;
            $this->whatsapp = $user->whatsapp;
            $this->email = $user->email;
            $this->role = $user->role;
            $this->is_active = $user->is_active !== false;
            $this->isEdit = true;
        }
    }

    public function save()
    {
        $this->validate([
            'name' => 'required|min:3',
            'whatsapp' => 'required|string|max:20|unique:users,whatsapp,' . ($this->user ? $this->user->id : 'NULL'),
            'email' => 'nullable|email|unique:users,email,' . ($this->user ? $this->user->id : 'NULL'),
            'password' => $this->isEdit ? 'nullable|min:6' : 'required|min:6',
            'role' => 'required|in:SUPER_ADMIN,ADMINISTRASI,BENDAHARA',
            'is_active' => 'boolean',
        ]);

        if ($this->isEdit && auth()->id() === $this->user->id && !$this->is_active) {
            $this->addError('is_active', 'Anda tidak dapat menonaktifkan akun Anda sendiri.');
            return;
        }

        $data = [
            'name' => $this->name,
            'whatsapp' => $this->whatsapp,
            'email' => $this->email ?: null,
            'role' => $this->role,
            'is_active' => $this->is_active,
        ];

        if (!empty($this->password)) {
            $data['password'] = Hash::make($this->password);
        }

        if ($this->isEdit) {
            $this->user->update($data);
            session()->flash('message', 'User berhasil diperbarui.');
        } else {
            $data['password'] = Hash::make($this->password);
            User::create($data);
            session()->flash('message', 'User berhasil ditambahkan.');
        }

        return redirect()->route('admin.users');
    }

    public function updatedWhatsapp($value)
    {
        if (!empty($value)) {
            $number = preg_replace('/\D/', '', $value);
            if (str_starts_with($number, '0')) {
                $this->whatsapp = '62' . substr($number, 1);
            } elseif (!str_starts_with($number, '62')) {
                $this->whatsapp = '62' . $number;
            } else {
                $this->whatsapp = $number;
            }
        }
    }

    public function render()
    {
        return view('livewire.user-form')->layout('layouts.admin');
    }
}
