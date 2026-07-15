<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class FeeMaster extends Model
{
    use HasFactory, SoftDeletes;

    protected static function booted()
    {
        static::deleting(function ($feeMaster) {
            $feeMaster->is_active = false;
            $feeMaster->saveQuietly();
        });

        static::restoring(function ($feeMaster) {
            $feeMaster->is_active = true;
        });
    }

    protected $fillable = [
        'item_name',
        'amount',
        'fee_category_id',
        'unit_target',
        'residence_target',
        'class_level_target_id',
        'recurrence_type',
        'start_date',
        'end_date',
        'billing_day',
        'due_days',
        'is_active',
        'replaced_by',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'amount' => 'integer',
        'is_active' => 'boolean',
        'class_level_target_id' => 'integer',
    ];

    public function discounts(): HasMany
    {
        return $this->hasMany(Discount::class);
    }

    public function classLevelTarget(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(ClassLevel::class, 'class_level_target_id');
    }

    public function category(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(FeeCategory::class, 'fee_category_id');
    }

    public function billings(): HasMany
    {
        return $this->hasMany(Billing::class);
    }

    public function replacedBy(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(FeeMaster::class, 'replaced_by');
    }

    public function replaces(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(FeeMaster::class, 'replaced_by');
    }

    public function isActive(): bool
    {
        return $this->is_active;
    }

    public function hasPaidBillings(): bool
    {
        return $this->billings()->where('status', 'PAID')->exists();
    }

    public function archive(int $replacedBy = null): void
    {
        $this->update([
            'is_active' => false,
            'replaced_by' => $replacedBy,
        ]);
        $this->delete();
    }

    public function scopeActiveWithinDates($query)
    {
        $today = now()->format('Y-m-d');
        return $query->where('is_active', true)
            ->where(function ($q) use ($today) {
                $q->whereNull('start_date')
                  ->orWhereDate('start_date', '<=', $today);
            })
            ->where(function ($q) use ($today) {
                $q->whereNull('end_date')
                  ->orWhereDate('end_date', '>=', $today);
            });
    }
}
