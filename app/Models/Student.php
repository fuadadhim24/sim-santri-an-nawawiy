<?php

namespace App\Models;

use App\Enums\StudentStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\UploadedFile;

class Student extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'full_name',
        'gender',
        'place_of_birth',
        'date_of_birth',
        'address',
        'village',
        'district',
        'city',
        'province',
        'postal_code',
        'phone',
        'email',
        'previous_school',
        'father_name',
        'father_occupation',
        'mother_name',
        'mother_occupation',
        'guardian_name',
        'guardian_occupation',
        'guardian_address',
        'guardian_phone',
        'guardian_relationship',
        'status',
        'is_active',
        'guardian_id',
        'spmb_schedule_id',
        'school_class_id',
        'kk',
        'foto',
        'nisn',
        'nisn_document',
        'akta',
        'ijazah',
    ];

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

                // Delete files when force deleting
                $student->deleteFiles();
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

    /**
     * File upload mutator for KK field
     */
    public function setKkAttribute($value)
    {
        if ($value instanceof UploadedFile) {
            // Delete old file if exists
            if ($this->kk && Storage::disk('public')->exists($this->kk)) {
                Storage::disk('public')->delete($this->kk);
            }

            $path = $value->store('students/kk', 'public');
            $this->attributes['kk'] = $path;
        } elseif (is_string($value)) {
            $this->attributes['kk'] = $value;
        }
    }

    /**
     * File upload mutator for Foto field
     */
    public function setFotoAttribute($value)
    {
        if ($value instanceof UploadedFile) {
            // Delete old file if exists
            if ($this->foto && Storage::disk('public')->exists($this->foto)) {
                Storage::disk('public')->delete($this->foto);
            }

            $path = $value->store('students/foto', 'public');
            $this->attributes['foto'] = $path;
        } elseif (is_string($value)) {
            $this->attributes['foto'] = $value;
        }
    }

    /**
     * File upload mutator for NISN document field
     */
    public function setNisnDocumentAttribute($value)
    {
        if ($value instanceof UploadedFile) {
            // Delete old file if exists
            if ($this->nisn_document && Storage::disk('public')->exists($this->nisn_document)) {
                Storage::disk('public')->delete($this->nisn_document);
            }

            $path = $value->store('students/nisn', 'public');
            $this->attributes['nisn_document'] = $path;
        } elseif (is_string($value)) {
            $this->attributes['nisn_document'] = $value;
        }
    }

    /**
     * File upload mutator for Akta field
     */
    public function setAktaAttribute($value)
    {
        if ($value instanceof UploadedFile) {
            // Delete old file if exists
            if ($this->akta && Storage::disk('public')->exists($this->akta)) {
                Storage::disk('public')->delete($this->akta);
            }

            $path = $value->store('students/akta', 'public');
            $this->attributes['akta'] = $path;
        } elseif (is_string($value)) {
            $this->attributes['akta'] = $value;
        }
    }

    /**
     * File upload mutator for Ijazah field
     */
    public function setIjazahAttribute($value)
    {
        if ($value instanceof UploadedFile) {
            // Delete old file if exists
            if ($this->ijazah && Storage::disk('public')->exists($this->ijazah)) {
                Storage::disk('public')->delete($this->ijazah);
            }

            $path = $value->store('students/ijazah', 'public');
            $this->attributes['ijazah'] = $path;
        } elseif (is_string($value)) {
            $this->attributes['ijazah'] = $value;
        }
    }

    /**
     * Accessor to get full URL for KK
     */
    public function getKkUrlAttribute()
    {
        if ($this->kk) {
            return Storage::url($this->kk);
        }
        return null;
    }

    /**
     * Accessor to get full URL for Foto
     */
    public function getFotoUrlAttribute()
    {
        if ($this->foto) {
            return Storage::url($this->foto);
        }
        return null;
    }

    /**
     * Accessor to get full URL for NISN document
     */
    public function getNisnDocumentUrlAttribute()
    {
        if ($this->nisn_document) {
            return Storage::url($this->nisn_document);
        }
        return null;
    }

    /**
     * Accessor to get full URL for Akta
     */
    public function getAktaUrlAttribute()
    {
        if ($this->akta) {
            return Storage::url($this->akta);
        }
        return null;
    }

    /**
     * Accessor to get full URL for Ijazah
     */
    public function getIjazahUrlAttribute()
    {
        if ($this->ijazah) {
            return Storage::url($this->ijazah);
        }
        return null;
    }

    /**
     * Delete all student files
     */
    public function deleteFiles()
    {
        $files = [
            $this->kk,
            $this->foto,
            $this->nisn_document,
            $this->akta,
            $this->ijazah
        ];

        foreach ($files as $file) {
            if ($file && Storage::disk('public')->exists($file)) {
                Storage::disk('public')->delete($file);
            }
        }
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
