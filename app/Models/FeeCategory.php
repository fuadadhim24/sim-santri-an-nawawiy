<?php

namespace App\Models;

use App\Enums\ActivationMode;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FeeCategory extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'is_locked',
        'activation_mode',
        'can_generate_before_acceptance',
    ];

    protected $casts = [
        'is_locked' => 'boolean',
        'can_generate_before_acceptance' => 'boolean',
    ];

    public function fees(): HasMany
    {
        return $this->hasMany(FeeMaster::class);
    }

    public function isLocked(): bool
    {
        return (bool) $this->is_locked;
    }

    public function isManualOnly(): bool
    {
        return $this->activation_mode === ActivationMode::MANUAL_ONLY->value;
    }

    public function isSingleActivePerKey(): bool
    {
        return $this->activation_mode === ActivationMode::SINGLE_ACTIVE_PER_KEY->value;
    }

    public function isMultiActive(): bool
    {
        return $this->activation_mode === ActivationMode::MULTI_ACTIVE->value;
    }

    public function canGenerateBeforeAcceptance(): bool
    {
        return (bool) $this->can_generate_before_acceptance;
    }

    public function requiresAcceptance(): bool
    {
        return !$this->can_generate_before_acceptance;
    }

    public static function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'code' => ['required', 'string', 'max:50', 'unique:fee_categories,code'],
            'is_locked' => ['boolean'],
            'activation_mode' => ['required', 'in:' . implode(',', ActivationMode::values())],
            'can_generate_before_acceptance' => ['boolean'],
        ];
    }
}
