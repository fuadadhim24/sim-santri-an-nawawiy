<?php

namespace App\Livewire;

use App\Models\Faq;
use Livewire\Component;
use Livewire\WithPagination;

class FaqIndex extends Component
{
    use WithPagination;

    public $search = '';

    public function delete($id)
    {
        Faq::findOrFail($id)->delete();
        session()->flash('message', 'FAQ berhasil dihapus.');
    }

    public function toggleActive($id)
    {
        $faq = Faq::findOrFail($id);
        $faq->update(['is_active' => !$faq->is_active]);
        session()->flash('message', 'Status FAQ berhasil diubah.');
    }

    public function render()
    {
        $query = Faq::orderBy('sort_order');

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('title', 'like', '%' . $this->search . '%')
                    ->orWhere('content', 'like', '%' . $this->search . '%');
            });
        }

        return view('livewire.faq-index', [
            'faqs' => $query->paginate(10),
        ])->layout('layouts.admin');
    }
}
