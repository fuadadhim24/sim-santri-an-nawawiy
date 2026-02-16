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

    public function render()
    {
        $query = FeeMaster::query();

        if ($this->search) {
            $query->where('item_name', 'like', '%' . $this->search . '%');
        }

        if ($this->categoryFilter) {
            $query->where('category', $this->categoryFilter);
        }

        return view('livewire.fee-master-index', [
            'fees' => $query->orderBy('category')->orderBy('unit_target')->paginate(10),
        ])->layout('layouts.admin');
    }
}
