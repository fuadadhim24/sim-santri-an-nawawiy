<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Faq extends Model
{
    protected $fillable = [
        'title',
        'content',
        'image_path',
        'pdf_path',
        'category',
        'sort_order',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    /**
     * Scope: hanya FAQ aktif, urut berdasarkan sort_order.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true)->orderBy('sort_order');
    }

    /**
     * Label kategori dalam bahasa Indonesia.
     */
    public function getCategoryLabelAttribute(): string
    {
        return match ($this->category) {
            'program' => 'Info Program',
            'biaya' => 'Informasi Biaya',
            'fasilitas' => 'Fasilitas',
            'pendaftaran' => 'Pendaftaran',
            default => 'Umum',
        };
    }
}
