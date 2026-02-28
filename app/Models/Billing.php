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
    ];

    protected static function booted(): void
    {
        static::deleting(function (Billing $billing) {
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
}
