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
        $category->delete();
        session()->flash('message', 'Kategori biaya berhasil dihapus.');
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
