<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SpmbSchedule extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'registration_start',
        'registration_end',
        'is_active',
    ];

    protected $casts = [
        'registration_start' => 'datetime',
        'registration_end' => 'datetime',
        'is_active' => 'boolean',
    ];

    public function students(): HasMany
    {
        return $this->hasMany(Student::class);
    }

    public function isActive(): bool
    {
        return $this->is_active;
    }

    public function isOpen(): bool
    {
        $now = now();
        return $now->gte($this->registration_start) && $now->lte($this->registration_end);
    }

    public function isClosed(): bool
    {
        return now()->gt($this->registration_end);
    }
}
