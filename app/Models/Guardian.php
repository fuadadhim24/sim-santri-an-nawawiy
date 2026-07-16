<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Log;
use Exception;

class Guardian extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'full_name',
        'whatsapp',
        'address',
    ];

    protected static function booted(): void
    {
        static::deleting(function (Guardian $guardian) {
            if ($guardian->isForceDeleting()) {
                $hasStudents = Student::withTrashed()
                    ->where('guardian_id', $guardian->id)
                    ->exists();

                if ($hasStudents) {
                    throw new Exception('Tidak dapat menghapus wali yang memiliki santri terdaftar');
                }
            }

            Log::info('Guardian deleted', [
                'id' => $guardian->id,
                'name' => $guardian->full_name,
                'deleted_by' => auth()->id(),
                'deleted_at' => now(),
                'force_delete' => $guardian->isForceDeleting(),
            ]);
        });
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function students(): HasMany
    {
        return $this->hasMany(Student::class);
    }

    /**
     * Normalize WhatsApp number to international format for wa.me link.
     * Handles: 08xxx → 628xxx, +628xxx → 628xxx, 628xxx → 628xxx
     */
    public function getWaLinkAttribute(): ?string
    {
        if (!$this->whatsapp) return null;

        $number = preg_replace('/\D/', '', $this->whatsapp); // strip non-digits

        if (str_starts_with($number, '62')) {
            return $number; // already correct
        }
        if (str_starts_with($number, '0')) {
            return '62' . substr($number, 1); // 08xxx → 628xxx
        }
        return '62' . $number; // fallback: prepend 62
    }

    public function setWhatsappAttribute($value)
    {
        if (empty($value)) {
            $this->attributes['whatsapp'] = null;
            return;
        }

        $number = preg_replace('/\D/', '', $value);

        if (str_starts_with($number, '62')) {
            $this->attributes['whatsapp'] = $number;
        } elseif (str_starts_with($number, '0')) {
            $this->attributes['whatsapp'] = '62' . substr($number, 1);
        } else {
            $this->attributes['whatsapp'] = '62' . $number;
        }
    }
}
