<?php

namespace App\Livewire;

use App\Models\Student;
use App\Models\Guardian;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class StudentDetail extends Component
{
    public Student $student;

    // Split installment properties
    public $showSplitModal = false;
    public $billingToSplit = null;
    public $splitCount = 2;
    public $splitAmounts = [];
    public $splitTitles = [];

    public function mount(Student $student)
    {
        $this->student = $student->load(['guardian.user', 'billings']);

        // Check if the current user is a guardian and owns this student
        if (Auth::user()->role === 'WALI_SANTRI') {
            $guardian = Guardian::where('user_id', Auth::id())->first();
            if (!$guardian || $student->guardian_id !== $guardian->id) {
                abort(403, 'Anda tidak memiliki akses ke data santri ini.');
            }
        }
    }

    public function processCashPayment($billingId)
    {
        if (!Auth::check() || !in_array(Auth::user()->role, ['BENDAHARA', 'SUPER_ADMIN', 'ADMINISTRASI'])) {
            session()->flash('error', 'Aksi tidak diizinkan.');
            return;
        }

        $billing = \App\Models\Billing::find($billingId);

        if (!$billing || $billing->status !== 'UNPAID') {
            session()->flash('error', 'Tagihan tidak valid untuk dibayar.');
            return;
        }

        try {
            $paymentService = new \App\Services\PaymentService();
            $paymentService->processCashPayment($billing, Auth::id());

            session()->flash('message', 'Pembayaran cash berhasil diproses.');
            $this->student->load(['guardian.user', 'billings']); // reload data terbaru
        } catch (\Exception $e) {
            session()->flash('error', 'Gagal memproses pembayaran: ' . $e->getMessage());
        }
    }

    public function delete($id)
    {
        if (!Auth::check() || !in_array(Auth::user()->role, ['BENDAHARA', 'SUPER_ADMIN', 'ADMINISTRASI'])) {
            session()->flash('error', 'Aksi tidak diizinkan.');
            return;
        }

        $billing = \App\Models\Billing::find($id);
        if ($billing) {
            try {
                $billing->delete();
                session()->flash('message', 'Tagihan berhasil diarsipkan secara sementara (soft delete).');
                $this->student->load(['guardian.user', 'billings']); // reload
            } catch (\Exception $e) {
                session()->flash('error', $e->getMessage());
            }
        }
    }

    public function openSplitModal($billingId)
    {
        if (!Auth::check() || !in_array(Auth::user()->role, ['BENDAHARA', 'SUPER_ADMIN', 'ADMINISTRASI'])) {
            session()->flash('error', 'Aksi tidak diizinkan.');
            return;
        }

        $billing = \App\Models\Billing::find($billingId);
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
        if (!Auth::check() || !in_array(Auth::user()->role, ['BENDAHARA', 'SUPER_ADMIN', 'ADMINISTRASI'])) {
            session()->flash('error', 'Aksi tidak diizinkan.');
            return;
        }

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

                    \App\Models\Billing::create([
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
            $this->student->load(['guardian.user', 'billings']); // reload
        } catch (\Exception $e) {
            session()->flash('error', 'Gagal memecah tagihan: ' . $e->getMessage());
        }
    }

    public function render()
    {
        // Use different layout based on user role
        $layout = Auth::user()->role === 'WALI_SANTRI' ? 'layouts.guardian' : 'layouts.admin';

        return view('livewire.student-detail')->layout($layout);
    }
}
