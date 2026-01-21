<?php

namespace App\Enums;

enum ManualAdjustmentType: string
{
    case ADD = 'add';
    case SUBTRACT = 'subtract';

    public function label(): string
    {
        return match ($this) {
            self::ADD => 'Cộng tiền',
            self::SUBTRACT => 'Trừ tiền',
        };
    }

    public function badgeColor(): string
    {
        return match ($this) {
            self::ADD => 'success',
            self::SUBTRACT => 'danger',
        };
    }

    public function icon(): string
    {
        return match ($this) {
            self::ADD => 'plus',
            self::SUBTRACT => 'minus',
        };
    }
}
