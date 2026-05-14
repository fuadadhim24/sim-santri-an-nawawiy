<?php

namespace App\Livewire;

use App\Models\Billing;
use App\Models\Student;
use App\Services\PaymentService;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class BillingIndex extends Component
{
    use WithPagination;

    public $search = '';
    public $statusFilter = '';
    public $unitFilter = '';       // Jenjang: 01, 02, 03
    public $classFilter = '';      // Kelas
    public $specialFilter = '';    // Golongan: UMUM, ANAK_GURU, YATIM

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingStatusFilter()
    {
        $this->resetPage();
    }

    public function updatingUnitFilter()
    {
        $this->classFilter = ''; // Reset kelas saat jenjang berubah
        $this->resetPage();
    }

    public function updatingClassFilter()
    {
        $this->resetPage();
    }

    public function updatingSpecialFilter()
    {
        $this->resetPage();
    }

    /**
     * Get daftar kelas berdasarkan unit yang dipilih.
     */
    public function getClassOptionsProperty(): array
    {
        $query = Student::query();

        if ($this->unitFilter) {
            $query->where('unit_code', $this->unitFilter);
        }

        return $query->whereNotNull('class_name')
            ->distinct()
            ->orderBy('class_name')
            ->pluck('class_name')
            ->toArray();
    }

    public function processCashPayment($billingId)
    {
        if (!Auth::check() || !in_array(Auth::user()->role, ['ADMIN_TU', 'SUPER_ADMIN'])) {
            session()->flash('error', 'Unauthorized action.');
            $this->showPaymentModal = false;
            return;
        }

        $billing = Billing::find($billingId);

        if (!$billing || $billing->status !== 'UNPAID') {
            session()->flash('error', 'Tagihan tidak valid untuk dibayar.');
            $this->showPaymentModal = false;
            return;
        }

        try {
            $paymentService = new PaymentService();
            $paymentService->processCashPayment($billing, Auth::id());

            session()->flash('message', 'Pembayaran berhasil diproses.');
        } catch (\Exception $e) {
            session()->flash('error', 'Gagal memproses pembayaran: ' . $e->getMessage());
        }

        $this->showPaymentModal = false;
        $this->selectedBilling = null;
    }

    public function render()
    {
        $query = Billing::with(['student', 'feeMaster', 'payments'])->where('visible_to_wali', true);

        if ($this->search) {
            $query->where(function ($q) {
                $q->whereHas('student', function ($sq) {
                    $sq->where('full_name', 'like', '%' . $this->search . '%')
                        ->orWhere('nis', 'like', '%' . $this->search . '%');
                })->orWhere('title', 'like', '%' . $this->search . '%');
            });
        }

        if ($this->statusFilter) {
            $query->where('status', $this->statusFilter);
        }

        // Filter by jenjang (unit_code)
        if ($this->unitFilter) {
            $query->whereHas('student', function ($sq) {
                $sq->where('unit_code', $this->unitFilter);
            });
        }

        // Filter by kelas
        if ($this->classFilter) {
            $query->whereHas('student', function ($sq) {
                $sq->where('class_name', $this->classFilter);
            });
        }

        // Filter by golongan (special_status)
        if ($this->specialFilter) {
            $query->whereHas('student', function ($sq) {
                $sq->where('special_status', $this->specialFilter);
            });
        }

        $billings = $query->orderBy('created_at', 'desc')->paginate(10);

        // Summary stats
        $summaryQuery = Billing::where('visible_to_wali', true);

        // Apply same filters to summary
        if ($this->unitFilter || $this->classFilter || $this->specialFilter) {
            $summaryQuery->whereHas('student', function ($sq) {
                if ($this->unitFilter) {
                    $sq->where('unit_code', $this->unitFilter);
                }
                if ($this->classFilter) {
                    $sq->where('class_name', $this->classFilter);
                }
                if ($this->specialFilter) {
                    $sq->where('special_status', $this->specialFilter);
                }
            });
        }

        $totalUnpaid = (clone $summaryQuery)->where('status', 'UNPAID')->sum('final_amount');
        $totalPaid = (clone $summaryQuery)->where('status', 'PAID')->sum('final_amount');
        $countUnpaid = (clone $summaryQuery)->where('status', 'UNPAID')->count();
        $countPaid = (clone $summaryQuery)->where('status', 'PAID')->count();

        return view('livewire.billing-index', [
            'billings' => $billings,
            'totalUnpaid' => $totalUnpaid,
            'totalPaid' => $totalPaid,
            'countUnpaid' => $countUnpaid,
            'countPaid' => $countPaid,
        ])->layout('layouts.admin');
    }

    public function delete($id)
    {
        $billing = Billing::find($id);
        if ($billing) {
            try {
                $billing->delete();
                session()->flash('message', 'Tagihan berhasil diarsipkan secara sementara (soft delete).');
            } catch (\Exception $e) {
                // To catch errors like trying to delete paid billing
                session()->flash('error', $e->getMessage());
            }
        }
    }
}
