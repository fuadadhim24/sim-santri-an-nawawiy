<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Log;

class Student extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = [];

    protected static function booted(): void
    {
        static::deleting(function (Student $student) {
            if ($student->isForceDeleting()) {
                $hasActiveBilling = Billing::withTrashed()
                    ->where('student_id', $student->id)
                    ->where('status', 'UNPAID')
                    ->exists();

                if ($hasActiveBilling) {
                    Log::warning('Student force delete prevented: has active billings', [
                        'id' => $student->id,
                        'name' => $student->full_name,
                    ]);
                    return false;
                }
            }

            Log::info('Student deleted', [
                'id' => $student->id,
                'name' => $student->full_name,
                'deleted_by' => auth()->id(),
                'deleted_at' => now(),
                'force_delete' => $student->isForceDeleting(),
            ]);
        });
    }

    public function guardian(): BelongsTo
    {
        return $this->belongsTo(Guardian::class);
    }

    public function billings(): HasMany
    {
        return $this->hasMany(Billing::class);
    }
}
