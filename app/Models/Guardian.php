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
        'phone',
        'email',
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
}
