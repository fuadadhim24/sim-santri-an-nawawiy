<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Log;
use Exception;

class Billing extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'student_id',
        'fee_master_id',
        'title',
        'original_amount',
        'discount_applied',
        'final_amount',
        'status',
        'payment_url',
        'payment_reference',
        'version_of',
        'version',
        'visible_to_wali',
        'archived_by',
        'archived_at',
        'archive_reason',
    ];

    protected $casts = [
        'original_amount' => 'decimal:2',
        'discount_applied' => 'decimal:2',
        'final_amount' => 'decimal:2',
        'visible_to_wali' => 'boolean',
        'archived_at' => 'datetime',
    ];

    protected static function booted(): void
    {
        static::updating(function (Billing $billing) {
            if ($billing->isPaid() && $billing->isDirty()) {
                throw new Exception('Tagihan yang sudah dibayar tidak dapat diubah. Data bersifat read-only.');
            }
        });

        static::deleting(function (Billing $billing) {
            if ($billing->isPaid()) {
                throw new Exception('Tidak dapat menghapus tagihan yang sudah dibayar. Data bersifat read-only.');
            }

            if ($billing->isForceDeleting()) {
                $hasPayments = Payment::withTrashed()
                    ->where('billing_id', $billing->id)
                    ->exists();

                if ($hasPayments) {
                    throw new Exception('Tidak dapat menghapus tagihan yang sudah memiliki pembayaran');
                }
            }

            Log::info('Billing deleted', [
                'id' => $billing->id,
                'title' => $billing->title,
                'student_id' => $billing->student_id,
                'deleted_by' => auth()->id(),
                'deleted_at' => now(),
                'force_delete' => $billing->isForceDeleting(),
            ]);
        });
    }

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function feeMaster()
    {
        return $this->belongsTo(FeeMaster::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function originalBilling()
    {
        return $this->belongsTo(Billing::class, 'version_of');
    }

    public function versions()
    {
        return $this->hasMany(Billing::class, 'version_of');
    }

    public function archivedBy()
    {
        return $this->belongsTo(User::class, 'archived_by');
    }

    public function isPaid(): bool
    {
        return $this->status === 'PAID';
    }

    public function isEditable(): bool
    {
        return !$this->isPaid();
    }

    public function archive(int $userId, string $reason = null): void
    {
        $this->update([
            'visible_to_wali' => false,
            'archived_by' => $userId,
            'archived_at' => now(),
            'archive_reason' => $reason,
        ]);
    }
}
