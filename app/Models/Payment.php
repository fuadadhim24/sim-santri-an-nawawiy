<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Log;

class Payment extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = [];

    protected $casts = [
        'paid_at' => 'datetime',
    ];

    protected static function booted(): void
    {
        static::deleting(function (Payment $payment) {
            Log::info('Payment deleted', [
                'id' => $payment->id,
                'billing_id' => $payment->billing_id,
                'amount' => $payment->amount,
                'deleted_by' => auth()->id(),
                'deleted_at' => now(),
                'force_delete' => $payment->isForceDeleting(),
            ]);
        });
    }

    public function billing(): BelongsTo
    {
        return $this->belongsTo(Billing::class);
    }
}
