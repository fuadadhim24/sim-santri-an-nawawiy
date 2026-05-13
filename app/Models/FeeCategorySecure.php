<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FeeCategory extends Model
{
    protected $guarded = [];

    protected $casts = [
        'is_single_active_per_key' => 'boolean',
    ];

    public function feeMasters(): HasMany
    {
        return $this->hasMany(FeeMaster::class);
    }

    /**
     * SECURITY: Prevent hard delete if category is used in active billings
     */
    public static function boot()
    {
        parent::boot();

        static::deleting(function (FeeCategory $category) {
            if ($category->isForceDeleting()) {
                $hasActiveBillings = Billing::where('fee_master_id', function ($query) use ($category) {
                    $query->select('id')
                        ->from('fee_masters')
                        ->where('fee_category_id', $category->id);
                })
                    ->where('status', '!=', 'CANCELLED')
                    ->exists();

                if ($hasActiveBillings) {
                    throw new \Exception(
                        "Tidak dapat menghapus kategori '{$category->name}' " .
                        "karena masih ada tagihan aktif yang menggunakan kategori ini. " .
                        "Gunakan soft delete atau hubungi Admin."
                    );
                }
            }

            \Illuminate\Support\Facades\Log::info('FeeCategory deleted', [
                'id' => $category->id,
                'name' => $category->name,
                'force_delete' => $category->isForceDeleting(),
            ]);
        });
    }

    public function isSingleActivePerKey(): bool
    {
        return $this->is_single_active_per_key ?? false;
    }

    public function requiresAcceptance(): bool
    {
        return $this->requires_acceptance ?? false;
    }

    public function isManualOnly(): bool
    {
        return $this->manual_only ?? false;
    }
}
