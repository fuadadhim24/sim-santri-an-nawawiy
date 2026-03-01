<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AuditLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'log_type',
        'subject_type',
        'subject_id',
        'performed_by',
        'old_values',
        'new_values',
        'ip_address',
        'description',
    ];

    protected $casts = [
        'old_values' => 'array',
        'new_values' => 'array',
    ];

    public function subject()
    {
        return $this->morphTo();
    }

    public function performer()
    {
        return $this->belongsTo(User::class, 'performed_by');
    }

    public static function log(string $logType, Model $subject, array $oldValues = [], array $newValues = [], string $description = null): self
    {
        return static::create([
            'log_type' => $logType,
            'subject_type' => get_class($subject),
            'subject_id' => $subject->id,
            'performed_by' => auth()->id(),
            'old_values' => empty($oldValues) ? null : $oldValues,
            'new_values' => empty($newValues) ? null : $newValues,
            'ip_address' => request()->ip(),
            'description' => $description,
        ]);
    }
}
