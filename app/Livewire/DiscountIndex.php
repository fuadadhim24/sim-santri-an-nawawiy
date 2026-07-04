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
