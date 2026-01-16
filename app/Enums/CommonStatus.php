<?php

namespace App\Enums;

enum CommonStatus: string
{
    case ACTIVE = 'active';
    case INACTIVE = 'inactive';

    public function label(): string
    {
        return match ($this) {
            self::ACTIVE => 'Hoạt động',
            self::INACTIVE => 'Không hoạt động',
        };
    }

    public function badgeColor(): string
    {
        return match ($this) {
            self::ACTIVE => 'success',
            self::INACTIVE => 'secondary',
        };
    }

    public function isVisibleToClient(): bool
    {
        return $this === self::ACTIVE;
    }

    public function canBePurchased(): bool
    {
        return $this === self::ACTIVE;
    }
}
