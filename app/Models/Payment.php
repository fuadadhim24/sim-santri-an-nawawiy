<?php

namespace App\Models;

use Exception;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Payment extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'billing_id',
        'admin_id',
        'method',
        'amount',
        'duitku_reference',
        'status',
        'notes',
        'paid_at',
        'snapshot_billing_amount',
    ];

    protected $casts = [
        'paid_at' => 'datetime',
        'amount' => 'decimal:2',
    ];

    protected static function booted(): void
    {
        static::updating(function (Payment $payment) {
            throw new Exception('Pembayaran tidak dapat diubah. Data pembayaran bersifat immutable.');
        });

        static::deleting(function (Payment $payment) {
            throw new Exception('Pembayaran tidak dapat dihapus. Data pembayaran bersifat immutable.');
        });
    }

    public function billing(): BelongsTo
    {
        return $this->belongsTo(Billing::class);
    }

    public function admin(): BelongsTo
    {
        return $this->belongsTo(User::class, 'admin_id');
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isPaid(): bool
    {
        return $this->status === 'paid';
    }

    public function isFailed(): bool
    {
        return $this->status === 'failed';
    }

    public function isCash(): bool
    {
        return $this->method === 'cash';
    }

    public function isDuitku(): bool
    {
        return $this->method === 'duitku';
    }

    public function markAsPaidByDuitku(string $reference): bool
    {
        return $this->updateQuietly([
            'status' => 'paid',
            'duitku_reference' => $reference,
            'paid_at' => now(),
        ]);
    }

    public function markAsFailedByDuitku(?string $reference = null): bool
    {
        $data = ['status' => 'failed'];
        if ($reference) {
            $data['duitku_reference'] = $reference;
        }
        return $this->updateQuietly($data);
    }
}
