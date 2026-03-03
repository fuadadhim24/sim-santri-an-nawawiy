<?php

namespace App\Enums;

enum ActivationMode: string
{
    case SINGLE_ACTIVE_PER_KEY = 'single_active_per_key';
    case MULTI_ACTIVE = 'multi_active';
    case MANUAL_ONLY = 'manual_only';

    public function value(): string
    {
        return $this->value;
    }

    public static function values(): array
    {
        return array_map(fn($case) => $case->value, self::cases());
    }
}
