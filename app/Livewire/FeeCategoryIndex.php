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

    public function delete($id)
    {
        $category = FeeCategory::findOrFail($id);

        if ($category->is_locked) {
            session()->flash('error', 'Kategori ini terkunci dan tidak dapat dihapus.');
            return;
        }

        if ($category->fees()->exists()) {
            session()->flash('error', "Kategori '{$category->name}' tidak dapat dihapus karena masih digunakan oleh data master biaya. Silakan non-aktifkan kategori ini.");
            return;
        }

        try {
            $category->delete();
            session()->flash('message', 'Kategori biaya berhasil dihapus.');
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
