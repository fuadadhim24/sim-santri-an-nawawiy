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
}
