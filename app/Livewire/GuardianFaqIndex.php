<?php

namespace App\Livewire;

use App\Models\Faq;
use Livewire\Component;

class GuardianFaqIndex extends Component
{
    public function render()
    {
        $faqs = Faq::active()->get()->groupBy('category');
        return view('livewire.guardian-faq-index', [
            'faqs' => $faqs
        ])->layout('layouts.guardian', ['header' => 'FAQ & Informasi']);
    }
}
