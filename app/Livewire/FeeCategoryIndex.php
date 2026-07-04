<?php

namespace App\Livewire;

use App\Models\FeeCategory;
use Livewire\Component;
use Livewire\WithPagination;

class FeeCategoryIndex extends Component
{
    use WithPagination;

    public $search = '';

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function confirmDelete($id)
    {
        $category = FeeCategory::findOrFail($id);

        if ($category->is_locked) {
            session()->flash('error', 'Kategori ini terkunci dan tidak dapat dihapus.');
            return;
        }

        $feeCount = $category->fees()->count();
        if ($feeCount === 0) {
            $this->dispatch('swal:confirm-simple-delete', [
                'id' => $category->id,
                'name' => $category->name,
            ]);
            return;
        }

        // Check active unpaid billings using categories
        $hasActiveBillings = \App\Models\Billing::whereIn('fee_master_id', function ($query) use ($category) {
            $query->select('id')
                ->from('fee_masters')
                ->where('fee_category_id', $category->id);
        })
            ->where('status', 'UNPAID')
            ->exists();

        // Fetch master biaya names for detail list
        $feeNames = $category->fees()->pluck('item_name')->toArray();

        $this->dispatch('swal:confirm-complex-delete', [
            'id' => $category->id,
            'name' => $category->name,
            'feeCount' => $feeCount,
            'feeNames' => $feeNames,
            'hasActiveBillings' => $hasActiveBillings,
        ]);
    }

    #[\Livewire\Attributes\On('deactivateCategory')]
    public function deactivateCategory($id = null)
    {
        if (is_array($id)) {
            $id = $id['id'] ?? null;
        }
        if (!$id) return;
        
        $category = FeeCategory::findOrFail($id);
        if ($category->is_locked) return;

        try {
            $category->update(['is_active' => false]);
            // Archive active fees under this category
            $activeFees = $category->fees()->get();
            foreach ($activeFees as $fee) {
                $fee->archive();
            }
            session()->flash('message', "Kategori '{$category->name}' dan seluruh master biaya terkait berhasil dinonaktifkan.");
        } catch (\Exception $e) {
            session()->flash('error', 'Gagal menonaktifkan kategori: ' . $e->getMessage());
        }
    }

    #[\Livewire\Attributes\On('forceDeleteCategory')]
    public function forceDeleteCategory($id = null)
    {
        if (is_array($id)) {
            $id = $id['id'] ?? null;
        }
        if (!$id) return;

        $category = FeeCategory::findOrFail($id);
        if ($category->is_locked) return;

        try {
            $fees = $category->fees()->get();
            foreach ($fees as $fee) {
                $fee->archive();
            }
            $category->delete();
            session()->flash('message', "Kategori '{$category->name}' dan master biaya terkait berhasil dihapus.");
        } catch (\Exception $e) {
            session()->flash('error', 'Gagal menghapus kategori: ' . $e->getMessage());
        }
    }

    #[\Livewire\Attributes\On('deleteCategoryDirect')]
    public function deleteCategoryDirect($id = null)
    {
        if (is_array($id)) {
            $id = $id['id'] ?? null;
        }
        if (!$id) return;

        $category = FeeCategory::findOrFail($id);
        if ($category->is_locked) return;

        try {
            $category->delete();
            session()->flash('message', "Kategori '{$category->name}' berhasil dihapus.");
        } catch (\Exception $e) {
            session()->flash('error', 'Gagal menghapus kategori: ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.fee-category-index', [
            'categories' => FeeCategory::where('name', 'like', '%' . $this->search . '%')
                ->orWhere('code', 'like', '%' . $this->search . '%')
                ->paginate(10)
        ])->layout('layouts.admin');
    }
}
