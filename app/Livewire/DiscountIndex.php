<?php

namespace App\Livewire;

use App\Models\Discount;
use Livewire\Component;
use Livewire\WithPagination;

class DiscountIndex extends Component
{
    use WithPagination;

    public $search = '';

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function confirmDelete($id)
    {
        $discount = Discount::findOrFail($id);
        
        // Count unpaid billings of that fee master that match the target status
        $feeMaster = $discount->feeMaster;
        $affectedCount = 0;
        if ($feeMaster) {
            $affectedCount = \App\Models\Billing::where('fee_master_id', $feeMaster->id)
                ->where('status', 'UNPAID')
                ->whereHas('student', function ($q) use ($discount) {
                    $q->whereHas('specialStatuses', function ($ssq) use ($discount) {
                        $ssq->where('special_statuses.code', $discount->target_status);
                    });
                })
                ->count();
        }

        if ($affectedCount > 0) {
            $this->dispatch('show-delete-discount-confirmation', [
                'id' => $id,
                'affectedCount' => $affectedCount,
                'feeMasterName' => $feeMaster ? $feeMaster->item_name : '',
                'targetStatus' => $discount->target_status,
            ]);
        } else {
            // Simple delete if no billings affected
            $this->dispatch('show-simple-delete-discount-confirmation', [
                'id' => $id,
            ]);
        }
    }

    #[\Livewire\Attributes\On('execute-delete-discount')]
    public function executeDelete($id)
    {
        $discount = Discount::findOrFail($id);

        // Delete silently without recalculating (keep current discounts on existing billings)
        Discount::withoutEvents(function () use ($discount) {
            $discount->delete();
        });

        session()->flash('message', 'Diskon berhasil dihapus.');
    }

    public function delete($id)
    {
        $discount = Discount::findOrFail($id);
        $discount->delete();
        session()->flash('message', 'Diskon berhasil dihapus.');
    }

    public function render()
    {
        return view('livewire.discount-index', [
            'discounts' => Discount::with('feeMaster')
                ->whereHas('feeMaster', function ($query) {
                    $query->whereNull('fee_masters.deleted_at');
                })
                ->when($this->search, function ($query) {
                    $query->whereHas('feeMaster', function ($subQuery) {
                        $subQuery->where('item_name', 'like', '%' . $this->search . '%');
                    });
                })
                ->orderBy('id', 'desc')
                ->paginate(10),
        ])->layout('layouts.admin');
    }
}
