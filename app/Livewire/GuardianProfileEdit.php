<?php

namespace App\Livewire;

use App\Models\Guardian;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Rule;
use Livewire\Component;

class GuardianProfileEdit extends Component
{
    public ?Guardian $guardian = null;
    
    #[Rule('required|min:3|max:100')]
    public $full_name = '';

    #[Rule('required|numeric|unique:guardians,whatsapp')]
    public $whatsapp = '';

    #[Rule('required|numeric|min_digits:10')]
    public $phone = '';

    #[Rule('required|min:10')]
    public $address = '';

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
        $this->phone = $this->guardian->phone;
        $this->address = $this->guardian->address;
    }

    public function save()
    {
        // Validate whatsapp uniqueness excluding current guardian
        $this->validate([
            'full_name' => 'required|min:3|max:100',
            'whatsapp' => 'required|numeric|unique:guardians,whatsapp,' . $this->guardian->id,
            'phone' => 'required|numeric|min_digits:10',
            'address' => 'required|min:10',
        ]);

        try {
            $this->guardian->update([
                'full_name' => $this->full_name,
                'whatsapp' => $this->whatsapp,
                'phone' => $this->phone,
                'address' => $this->address,
            ]);

            session()->flash('message', 'Profil Anda berhasil diperbarui!');
            return redirect()->route('wali.dashboard');
        } catch (\Exception $e) {
            session()->flash('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function cancel()
    {
        return redirect()->route('wali.dashboard');
    }

    public function render()
    {
        return view('livewire.guardian-profile-edit')->layout('layouts.app');
    }
}
