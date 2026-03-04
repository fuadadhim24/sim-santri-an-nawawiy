<?php

namespace App\Models;

use App\Enums\StudentStatus;
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

    protected $casts = [
        'status' => 'string',
        'is_active' => 'boolean',
    ];

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

    public function spmbSchedule(): BelongsTo
    {
        return $this->belongsTo(SpmbSchedule::class);
    }

    public function billings(): HasMany
    {
        return $this->hasMany(Billing::class);
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function getStatusEnum(): ?StudentStatus
    {
        return StudentStatus::tryFrom($this->status);
    }

    public function isPending(): bool
    {
        return $this->status === StudentStatus::PENDING->value;
    }

    public function isAccepted(): bool
    {
        return $this->status === StudentStatus::ACCEPTED->value;
    }

    public function isRejected(): bool
    {
        return $this->status === StudentStatus::REJECTED->value;
    }

    public function markAsAccepted(): void
    {
        // when a student is accepted, also make them active
        $this->update([
            'status' => StudentStatus::ACCEPTED->value,
            'is_active' => true,
        ]);
    }

    public function markAsPending(): void
    {
        $this->update([
            'status' => StudentStatus::PENDING->value,
            'is_active' => false,
        ]);
    }

    public function markAsRejected(): void
    {
        $this->update([
            'status' => StudentStatus::REJECTED->value,
            'is_active' => false,
        ]);
    }

    public function setStatus(string $status): void
    {
        $validStatus = StudentStatus::tryFrom($status);
        if (!$validStatus) {
            throw new \InvalidArgumentException("Invalid status: {$status}");
        }
        $this->update(['status' => $status]);
    }
}
