<?php

namespace App\Livewire;

use App\Models\Guardian;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Livewire\Component;

class GuardianProfileEdit extends Component
{
    public ?Guardian $guardian = null;
    
    public $full_name = '';
    public $whatsapp = '';
    public $address = '';
    public $email = '';

    public $current_password = '';
    public $new_password = '';
    public $new_password_confirmation = '';

    public function mount()
    {
        $user = Auth::user();
        $this->guardian = Guardian::where('user_id', $user->id)->first();

        if (!$this->guardian) {
            abort(403, 'Data wali santri tidak ditemukan.');
        }

        // Pre-populate form
        $this->full_name = $this->guardian->full_name;
        $this->whatsapp = $this->guardian->whatsapp;
        $this->address = $this->guardian->address ?? '';
        $this->email = $user->email ?? '';
    }

    public function updateProfile()
    {
        $user = Auth::user();

        // Validate basic info
        $this->validate([
            'whatsapp' => 'required|numeric|unique:guardians,whatsapp,' . $this->guardian->id,
            'address' => 'nullable|min:10',
            'email' => 'nullable|email|unique:users,email,' . $user->id,
        ]);

        try {
            // Update User record (name, email)
            $user->update([
                'name' => $this->full_name,
                'email' => $this->email,
            ]);

            // Update Guardian record
            $this->guardian->update([
                'full_name' => $this->full_name,
                'whatsapp' => $this->whatsapp,
                'address' => $this->address,
            ]);

            session()->flash('profile_message', 'Informasi profil Anda berhasil diperbarui!');
        } catch (\Exception $e) {
            session()->flash('profile_error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function updatePassword()
    {
        $user = Auth::user();

        // Validate password
        $this->validate([
            'current_password' => 'required|current_password',
            'new_password' => 'required|min:6|confirmed',
        ]);

        try {
            // Update User password
            $user->update([
                'password' => Hash::make($this->new_password),
            ]);

            // Reset password fields
            $this->reset(['current_password', 'new_password', 'new_password_confirmation']);

            session()->flash('password_message', 'Kata sandi Anda berhasil diperbarui!');
        } catch (\Exception $e) {
            session()->flash('password_error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function cancel()
    {
        return redirect()->route('wali.dashboard');
    }

    public function render()
    {
        return view('livewire.guardian-profile-edit')->layout('layouts.guardian');
    }
}
