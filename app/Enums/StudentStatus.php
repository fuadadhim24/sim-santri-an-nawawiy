<?php

namespace App\Enums;

enum StudentStatus: string
{
    case PENDING = 'menunggu';
    case ACCEPTED = 'diterima';
    case REJECTED = 'ditolak';

    public function getLabel(): string
    {
        return match ($this) {
            self::PENDING => 'Menunggu',
            self::ACCEPTED => 'Diterima',
            self::REJECTED => 'Ditolak',
        };
    }

    public function isPending(): bool
    {
        return $this === self::PENDING;
    }

    public function isAccepted(): bool
    {
        return $this === self::ACCEPTED;
    }

    public function isRejected(): bool
    {
        return $this === self::REJECTED;
    }
}
