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
    public $showPaymentModal = false;
    public $statusFilter = '';
    public $unitFilter = '';       // Jenjang: 01, 02, 03
    public $classFilter = '';      // Kelas
    public $specialFilter = '';    // Golongan: UMUM, ANAK_GURU, YATIM
    public $overdueFilter = false; // Filter tagihan terlambat

    public array $overdueReminderItems = [];

    // Split Billing / Installment Properties
    public $showSplitModal = false;
    public $billingToSplit = null;
    public $splitCount = 2;
    public $splitAmounts = [];
    public $splitTitles = [];

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

    public function updatingOverdueFilter()
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

    private function loadOverdueReminderItems(): void
    {
        $query = Billing::with(['student.guardian', 'feeMaster', 'payments'])->where('visible_to_wali', true);

        if ($this->search) {
            $query->where(function ($q) {
                $q->whereHas('student', function ($sq) {
                    $sq->where('full_name', 'like', '%' . $this->search . '%')
                        ->orWhere('nis', 'like', '%' . $this->search . '%')
                        ->orWhere('registration_number', 'like', '%' . $this->search . '%');
                })->orWhere('title', 'like', '%' . $this->search . '%');
            });
        }

        if ($this->statusFilter) {
            $query->where('status', $this->statusFilter);
        }

        if ($this->unitFilter) {
            $query->whereHas('student', function ($sq) {
                $sq->where('unit_code', $this->unitFilter);
            });
        }

        if ($this->classFilter) {
            $query->whereHas('student', function ($sq) {
                $sq->where('class_name', $this->classFilter);
            });
        }

        if ($this->specialFilter) {
            $query->whereHas('student', function ($sq) {
                $sq->where('special_status', $this->specialFilter);
            });
        }

        $this->overdueReminderItems = [];

        if ($this->overdueFilter) {
            $query->where('status', 'UNPAID')
                ->where(function ($q) {
                    $q->where('due_date', '<', now()->toDateString())
                        ->orWhereNull('due_date');
                });

            $this->overdueReminderItems = $query->orderBy('created_at', 'desc')
                ->get()
                ->filter(fn ($billing) => $billing->student)
                ->groupBy(fn ($billing) => $billing->student_id)
                ->map(function ($items) {
                    $firstBilling = $items->first();
                    $student = $firstBilling->student;
                    $guardian = $student?->guardian;

                    return [
                        'student_id' => $student?->id,
                        'student_name' => $student?->full_name ?? 'Santri',
                        'guardian_name' => $guardian?->full_name ?? 'Wali Santri',
                        'guardian_phone' => $guardian?->whatsapp ?? '',
                        'count' => $items->count(),
                        'billings' => $items->map(function ($billing) {
                            return [
                                'title' => $billing->title,
                                'amount' => (int) $billing->final_amount,
                            ];
                        })->values()->all(),
                    ];
                })
                ->values()
                ->all();
        }
    }

    public function processCashPayment($billingId)
    {
        if (!Auth::check() || !in_array(Auth::user()->role, ['BENDAHARA', 'SUPER_ADMIN', 'ADMINISTRASI'])) {
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
        $query = Billing::with(['student.guardian', 'feeMaster', 'payments'])->where('visible_to_wali', true);

        if ($this->search) {
            $query->where(function ($q) {
                $q->whereHas('student', function ($sq) {
                    $sq->where('full_name', 'like', '%' . $this->search . '%')
                        ->orWhere('nis', 'like', '%' . $this->search . '%')
                        ->orWhere('registration_number', 'like', '%' . $this->search . '%');
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

        // Filter tagihan terlambat (UNPAID + due_date sudah lewat)
        if ($this->overdueFilter) {
            $query->where('status', 'UNPAID')
                  ->where(function ($q) {
                      $q->where('due_date', '<', now()->toDateString())
                        ->orWhereNull('due_date'); // fallback: anggap terlambat jika tidak ada due_date
                  });
        }

        $billings = $query->orderBy('created_at', 'desc')->paginate(10);

        if ($this->overdueFilter) {
            $this->loadOverdueReminderItems();
        } else {
            $this->overdueReminderItems = [];
        }

        // Hitung tagihan terlambat untuk badge
        $countOverdue = Billing::where('visible_to_wali', true)
            ->where('status', 'UNPAID')
            ->where('due_date', '<', now()->toDateString())
            ->count();

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
            'countOverdue' => $countOverdue,
            'overdueReminderItems' => $this->overdueReminderItems,
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

    public function openSplitModal($billingId)
    {
        $billing = Billing::with('student')->find($billingId);
        if (!$billing || $billing->status !== 'UNPAID') {
            session()->flash('error', 'Tagihan tidak valid untuk dicicil.');
            return;
        }

        $this->billingToSplit = $billing;
        $this->splitCount = 2;
        $this->calculateSplits();
        $this->showSplitModal = true;
    }

    public function updatedSplitCount()
    {
        $this->calculateSplits();
    }

    public function calculateSplits()
    {
        if (!$this->billingToSplit) {
            return;
        }

        $total = (float) $this->billingToSplit->final_amount;
        $count = (int) $this->splitCount;

        if ($count < 2 || $count > 12) {
            $count = 2;
            $this->splitCount = 2;
        }

        $baseAmount = floor($total / $count);
        $remainder = $total - ($baseAmount * $count);

        $this->splitAmounts = [];
        $this->splitTitles = [];

        for ($i = 1; $i <= $count; $i++) {
            $amount = $baseAmount;
            if ($i === $count) {
                $amount += $remainder;
            }
            $this->splitAmounts[$i - 1] = $amount;
            $this->splitTitles[$i - 1] = $this->billingToSplit->title . " - Cicilan " . $i;
        }
    }

    public function processSplit()
    {
        if (!$this->billingToSplit) {
            return;
        }

        $totalExpected = (float) $this->billingToSplit->final_amount;
        $totalActual = array_sum(array_map('floatval', $this->splitAmounts));

        if (abs($totalExpected - $totalActual) > 0.01) {
            session()->flash('error', 'Jumlah total cicilan (' . number_format($totalActual, 0, ',', '.') . ') harus sama dengan jumlah tagihan asli (' . number_format($totalExpected, 0, ',', '.') . ').');
            return;
        }

        try {
            \Illuminate\Support\Facades\DB::transaction(function () {
                // 1. Mark original billing as VOID
                $this->billingToSplit->update([
                    'status' => 'VOID',
                    'archive_reason' => 'Dipecah menjadi cicilan oleh ' . auth()->user()->name
                ]);

                // 2. Create new installment billings
                foreach ($this->splitAmounts as $index => $amount) {
                    $title = $this->splitTitles[$index] ?? ($this->billingToSplit->title . ' - Cicilan ' . ($index + 1));
                    
                    $priceSnapshot = [
                        [
                            'item_name' => $title,
                            'amount' => (float)$amount,
                            'fee_master_id' => $this->billingToSplit->fee_master_id,
                        ]
                    ];

                    Billing::create([
                        'student_id' => $this->billingToSplit->student_id,
                        'fee_master_id' => $this->billingToSplit->fee_master_id,
                        'title' => $title,
                        'original_amount' => (float)$amount,
                        'discount_applied' => 0,
                        'final_amount' => (float)$amount,
                        'status' => 'UNPAID',
                        'price_snapshot' => $priceSnapshot,
                        'billing_period_start' => $this->billingToSplit->billing_period_start,
                        'billing_period_end' => $this->billingToSplit->billing_period_end,
                        'visible_to_wali' => true,
                        'version' => 1,
                    ]);
                }
            });

            session()->flash('message', 'Tagihan berhasil dipecah menjadi ' . $this->splitCount . ' cicilan.');
            $this->showSplitModal = false;
            $this->billingToSplit = null;
        } catch (\Exception $e) {
            session()->flash('error', 'Gagal memecah tagihan: ' . $e->getMessage());
        }
    }
}
