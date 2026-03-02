<?php

namespace App\Livewire;

use App\Models\Billing;
use Livewire\WithPagination;
use Livewire\Component;

class BillingArchive extends Component
{
    use WithPagination;

    public $search = '';

    public function render()
    {
        // For Billing, we want to show anything soft-deleted OR not visible to wali (if that logic is preferred, but soft delete is the standard here)
        $query = Billing::with(['student', 'feeMaster'])->onlyTrashed();

        if ($this->search) {
            $query->where(function ($q) {
                $q->whereHas('student', function ($sq) {
                    $sq->where('full_name', 'like', '%' . $this->search . '%')
                        ->orWhere('nis', 'like', '%' . $this->search . '%');
                })->orWhere('title', 'like', '%' . $this->search . '%');
            });
        }

        $billings = $query->orderBy('deleted_at', 'desc')->paginate(10);

        return view('livewire.billing-archive', [
            'billings' => $billings
        ])->layout('layouts.admin');
    }

    public function restore($id)
    {
        $billing = Billing::onlyTrashed()->find($id);
        if ($billing) {
            $billing->restore();
            session()->flash('message', 'Tagihan berhasil dipulihkan.');
        }
    }

    public function forceDelete($id)
    {
        $billing = Billing::onlyTrashed()->find($id);
        if ($billing) {
            try {
                $billing->forceDelete();
                session()->flash('message', 'Tagihan dihapus secara permanen.');
            } catch (\Exception $e) {
                session()->flash('error', $e->getMessage());
            }
        }
    }
}
