<?php

namespace App\Livewire;

use App\Models\FeeMaster;
use App\Models\Billing;
use Livewire\WithPagination;
use Livewire\Component;

class FeeMasterArchive extends Component
{
    use WithPagination;

    public $search = '';
    public $filter = 'all';

    protected $paginationTheme = 'tailwind';

    public function render()
    {
        $query = FeeMaster::with('category')
            ->onlyTrashed();

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('item_name', 'like', '%' . $this->search . '%')
                    ->orWhereHas('category', function ($c) {
                        $c->where('name', 'like', '%' . $this->search . '%');
                    });
            });
        }

        if ($this->filter !== 'all') {
            if ($this->filter === 'active') {
                $query->where('is_active', true);
            } elseif ($this->filter === 'archived') {
                $query->where('is_active', false);
            }
        }

        $feeMasters = $query->orderBy('deleted_at', 'desc')->paginate(10);

        return view('livewire.fee-master-archive', [
            'feeMasters' => $feeMasters,
        ])->layout('layouts.admin');
    }

    public function getFeeMastersProperty()
    {
        return FeeMaster::with('category')->onlyTrashed();
    }

    public function restore(FeeMaster $feeMaster)
    {
        $this->authorize('restore', $feeMaster);

        $feeMaster->restore();

        session()->flash('message', 'Fee Master restored successfully.');
    }

    public function forceDelete(FeeMaster $feeMaster)
    {
        $this->authorize('forceDelete', $feeMaster);

        $feeMaster->forceDelete();

        session()->flash('message', 'Fee Master permanently deleted.');
    }
}
