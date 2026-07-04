<?php

namespace App\Livewire;

use App\Models\Billing;
use App\Models\Guardian;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class GuardianPaymentHistory extends Component
{
    use WithPagination;

    public $studentFilter = '';
    public $statusFilter  = '';
    public $search        = '';

    public function updatingStudentFilter() { $this->resetPage(); }
    public function updatingStatusFilter()  { $this->resetPage(); }
    public function updatingSearch()        { $this->resetPage(); }

    public function render()
    {
        $guardian = Guardian::where('user_id', Auth::id())
            ->with(['students' => fn($q) => $q->where('status', 'diterima')])
            ->firstOrFail();

        $studentIds = $guardian->students->pluck('id');

        $query = Billing::with(['student', 'payments'])
            ->whereIn('student_id', $studentIds)
            ->where('visible_to_wali', true)
            ->whereIn('status', ['PAID', 'VOID']);

        if ($this->studentFilter) {
            $query->where('student_id', $this->studentFilter);
        }

        if ($this->statusFilter) {
            $query->where('status', $this->statusFilter);
        }

        if ($this->search) {
            $query->where('title', 'like', '%' . $this->search . '%');
        }

        $billings = $query->orderByDesc('created_at')->paginate(15);

        $totalPaid   = Billing::whereIn('student_id', $studentIds)->where('status', 'PAID')->sum('final_amount');
        $countPaid   = Billing::whereIn('student_id', $studentIds)->where('status', 'PAID')->count();
        $countVoid   = Billing::whereIn('student_id', $studentIds)->where('status', 'VOID')->count();

        return view('livewire.guardian-payment-history', [
            'guardian'   => $guardian,
            'billings'   => $billings,
            'students'   => $guardian->students,
            'totalPaid'  => $totalPaid,
            'countPaid'  => $countPaid,
            'countVoid'  => $countVoid,
        ])->layout('layouts.guardian');
    }
}
