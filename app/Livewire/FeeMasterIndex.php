<?php

namespace App\Livewire;

use App\Models\FeeMaster;
use Livewire\Component;
use Livewire\WithPagination;

class FeeMasterIndex extends Component
{
    use WithPagination;

    public $search = '';
    public $categoryFilter = '';

    #[\Livewire\Attributes\Computed]
    public function feeCategories()
    {
        return \App\Models\FeeCategory::orderBy('name')->get();
    }

    public function render()
    {
        $query = FeeMaster::with('category');

        if ($this->search) {
            $query->where('item_name', 'like', '%' . $this->search . '%');
        }

        if ($this->categoryFilter) {
            $query->where('fee_category_id', $this->categoryFilter);
        }

        return view('livewire.fee-master-index', [
            'fees' => $query->orderBy('fee_category_id')->orderBy('unit_target')->paginate(10),
        ])->layout('layouts.admin');
    }

    public function delete($id)
    {
        $feeMaster = FeeMaster::find($id);
        if ($feeMaster) {
            $feeMaster->delete();
            session()->flash('message', 'Master biaya berhasil diarsipkan secara sementara (soft delete).');
        }
    }

    public function confirmSync($feeMasterId)
    {
        $this->syncFeeMasterId = $feeMasterId;
        $feeMaster = \App\Models\FeeMaster::find($feeMasterId);
        
        if (!$feeMaster) {
            return;
        }

        // Count eligible students
        $query = \App\Models\Student::where('is_active', true)->where('status', 'diterima');
        
        if ($feeMaster->unit_target) {
            $query->where('unit_code', $feeMaster->unit_target);
        }
        if ($feeMaster->residence_target) {
            $query->where('residence_status', $feeMaster->residence_target);
        }

        $students = $query->get();
        $missingCount = 0;

        foreach ($students as $student) {
            $billingQuery = \App\Models\Billing::where('student_id', $student->id)
                ->where('fee_master_id', $feeMaster->id);

            if ($feeMaster->recurrence_type === 'MONTHLY') {
                $billingQuery->whereMonth('created_at', now()->month)
                             ->whereYear('created_at', now()->year);
            } elseif ($feeMaster->recurrence_type === 'YEARLY') {
                $billingQuery->whereYear('created_at', now()->year);
            }

            if (!$billingQuery->exists()) {
                $missingCount++;
            }
        }

        if ($missingCount == 0) {
            $this->dispatch('swal:info', [
                'title' => 'Sudah Sinkron',
                'text' => 'Semua santri aktif yang memenuhi kriteria sudah mendapatkan tagihan ini.'
            ]);
            return;
        }

        $infoRecurrence = $feeMaster->recurrence_type === 'MONTHLY' ? ' (Bulanan)' : ($feeMaster->recurrence_type === 'YEARLY' ? ' (Tahunan)' : ' (Sekali Bayar)');

        $this->dispatch('confirm-sync-billings', [
            'id' => $feeMaster->id,
            'itemName' => $feeMaster->item_name . $infoRecurrence,
            'missingCount' => $missingCount
        ]);
    }

    #[\Livewire\Attributes\On('processSync')]
    public function processSync($id)
    {
        $feeMaster = FeeMaster::find($id);
        if (!$feeMaster) return;

        $query = \App\Models\Student::where('is_active', true)->where('status', 'diterima');
        
        if ($feeMaster->unit_target) {
            $query->where('unit_code', $feeMaster->unit_target);
        }
        if ($feeMaster->residence_target) {
            $query->where('residence_status', $feeMaster->residence_target);
        }

        $students = $query->get();
        $generatedCount = 0;

        foreach ($students as $student) {
            $billingQuery = \App\Models\Billing::where('student_id', $student->id)
                ->where('fee_master_id', $feeMaster->id);

            if ($feeMaster->recurrence_type === 'MONTHLY') {
                $billingQuery->whereMonth('created_at', now()->month)
                             ->whereYear('created_at', now()->year);
            } elseif ($feeMaster->recurrence_type === 'YEARLY') {
                $billingQuery->whereYear('created_at', now()->year);
            }

            if (!$billingQuery->exists()) {
                $discountAmount = 0;
                if ($student->special_status !== 'UMUM') {
                    $discount = \App\Models\Discount::where('fee_master_id', $feeMaster->id)
                        ->where('target_status', $student->special_status)
                        ->first();
                    if ($discount) {
                        $discountAmount = $discount->discount_amount;
                    }
                }

                $finalAmount = max(0, $feeMaster->amount - $discountAmount);
                $dueDate = now()->addDays($feeMaster->due_days ?? 14)->format('Y-m-d');

                \App\Models\Billing::create([
                    'student_id' => $student->id,
                    'fee_master_id' => $feeMaster->id,
                    'title' => $feeMaster->item_name,
                    'original_amount' => $feeMaster->amount,
                    'discount_applied' => $discountAmount,
                    'final_amount' => $finalAmount,
                    'status' => 'UNPAID',
                    'due_date' => $dueDate,
                    'visible_to_wali' => true,
                    'version' => 1,
                ]);

                $generatedCount++;
            }
        }

        $this->dispatch('swal:success', [
            'title' => 'Sinkronisasi Berhasil!',
            'text' => "Berhasil men-generate $generatedCount tagihan untuk santri yang belum mendapatkannya."
        ]);
    }
}
