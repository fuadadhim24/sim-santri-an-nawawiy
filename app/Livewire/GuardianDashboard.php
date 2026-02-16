<?php

namespace App\Livewire;

use App\Models\Guardian;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class GuardianDashboard extends Component
{
    public function render()
    {
        $user = Auth::user();

        $guardian = Guardian::where('user_id', $user->id)->first();

        if (!$guardian) {
            abort(403, 'Guardian data not found for this user.');
        }

        $guardian->load(['students.billings' => function ($query) {
            $query->orderBy('created_at', 'desc');
        }]);

        $totalUnpaid = 0;
        foreach ($guardian->students as $student) {
            foreach ($student->billings as $bill) {
                if ($bill->status === 'UNPAID') {
                    $totalUnpaid += $bill->final_amount;
                }
            }
        }

        return view('livewire.guardian-dashboard', [
            'guardian' => $guardian,
            'totalUnpaid' => $totalUnpaid
        ])->layout('layouts.app');
    }
}
