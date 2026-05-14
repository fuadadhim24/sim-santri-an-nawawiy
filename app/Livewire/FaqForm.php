<?php

namespace App\Livewire;

use App\Models\Faq;
use Livewire\Component;
use Livewire\WithFileUploads;

class FaqForm extends Component
{
    use WithFileUploads;

    public ?Faq $faq = null;
    public $title = '';
    public $content = '';
    public $category = 'umum';
    public $sort_order = 0;
    public $is_active = true;
    public $image = null;
    public $pdf = null;
    public $isEdit = false;

    // Existing file paths (for edit mode)
    public $existingImage = null;
    public $existingPdf = null;

    public function mount($faq = null)
    {
        if ($faq) {
            $this->faq = $faq;
            $this->title = $faq->title;
            $this->content = $faq->content;
            $this->category = $faq->category;
            $this->sort_order = $faq->sort_order;
            $this->is_active = $faq->is_active;
            $this->existingImage = $faq->image_path;
            $this->existingPdf = $faq->pdf_path;
            $this->isEdit = true;
        }
    }

    public function removeImage()
    {
        $this->existingImage = null;
    }

    public function removePdf()
    {
        $this->existingPdf = null;
    }

    public function save()
    {
        $this->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'category' => 'required|in:umum,program,biaya,fasilitas,pendaftaran',
            'sort_order' => 'required|integer|min:0',
            'is_active' => 'boolean',
            'image' => 'nullable|image|max:2048',
            'pdf' => 'nullable|mimes:pdf|max:5120',
        ]);

        $data = [
            'title' => $this->title,
            'content' => $this->content,
            'category' => $this->category,
            'sort_order' => $this->sort_order,
            'is_active' => $this->is_active,
        ];

        // Handle image upload
        if ($this->image) {
            $data['image_path'] = $this->image->store('faqs/images', 'public');
        } elseif ($this->existingImage === null && $this->isEdit) {
            $data['image_path'] = null;
        }

        // Handle PDF upload
        if ($this->pdf) {
            $data['pdf_path'] = $this->pdf->store('faqs/pdfs', 'public');
        } elseif ($this->existingPdf === null && $this->isEdit) {
            $data['pdf_path'] = null;
        }

        if ($this->isEdit) {
            $this->faq->update($data);
            session()->flash('message', 'FAQ berhasil diperbarui.');
        } else {
            Faq::create($data);
            session()->flash('message', 'FAQ berhasil ditambahkan.');
        }

        return redirect()->route('admin.faqs');
    }

    public function getCategoryOptionsProperty(): array
    {
        return [
            'umum' => 'Umum',
            'program' => 'Info Program',
            'biaya' => 'Informasi Biaya',
            'fasilitas' => 'Fasilitas',
            'pendaftaran' => 'Pendaftaran',
        ];
    }

    public function render()
    {
        return view('livewire.faq-form')->layout('layouts.admin');
    }
}
