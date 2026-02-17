<?php

namespace App\Livewire;

use App\Models\Billing;
use App\Models\Guardian;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class GuardianDashboard extends Component
{
    public function pay($billingId)
    {
        $bill = Billing::find($billingId);

        if ($bill && $bill->status == 'UNPAID') {
            // Check if this bill belongs to a student of the current guardian
            $user = Auth::user();
            $guardian = Guardian::where('user_id', $user->id)->first();

            if (!$guardian) {
                 return;
            }

            // Verify ownership
            $studentIds = $guardian->students->pluck('id')->toArray();
            if (in_array($bill->student_id, $studentIds)) {
                 $bill->update(['status' => 'PAID']);
                 session()->flash('message', 'Payment successful for invoice: ' . $bill->title);
            }
        }
    }

    public function render()
    {
        $user = Auth::user();

        $guardian = Guardian::where('user_id', $user->id)->first();

        if (!$guardian) {
            abort(403, 'Guardian data not found for this user.');
        }

        $guardian->load(['user', 'students.billings' => function ($query) {
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
        ])->layout('layouts.guardian');
    }
}
