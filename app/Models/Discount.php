<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Discount extends Model
{
    use HasFactory;

    protected $fillable = [
        'fee_master_id',
        'target_status',
        'discount_amount',
        'description',
    ];

    public function feeMaster(): BelongsTo
    {
        return $this->belongsTo(FeeMaster::class)->withTrashed();
    }

    public function specialStatus(): BelongsTo
    {
        return $this->belongsTo(SpecialStatus::class, 'target_status', 'code');
    }
}
