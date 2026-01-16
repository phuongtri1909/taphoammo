<?php

namespace App\Enums;

enum WalletStatus: string
{
    case ACTIVE = 'active';
    case FROZEN = 'frozen';
    case SUSPENDED = 'suspended';

    public function label(): string
    {
        return match ($this) {
            self::ACTIVE => 'Hoạt động',
            self::FROZEN => 'Đóng băng',
            self::SUSPENDED => 'Tạm ngưng',
        };
    }

    public function badgeColor(): string
    {
        return match ($this) {
            self::ACTIVE => 'success',
            self::FROZEN => 'warning',
            self::SUSPENDED => 'danger',
        };
    }
}
