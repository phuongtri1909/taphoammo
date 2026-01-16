<?php

namespace App\Enums;

enum ProductValueStatus: string
{
    case AVAILABLE = 'available';
    case SOLD = 'sold';
    case REFUNDED = 'refunded';
    case INVALID = 'invalid';

    public function label(): string
    {
        return match ($this) {
            self::AVAILABLE => 'Có sẵn',
            self::SOLD => 'Đã bán',
            self::REFUNDED => 'Hoàn tiền',
            self::INVALID => 'Không hợp lệ',
        };
    }

    public function badgeColor(): string
    {
        return match ($this) {
            self::AVAILABLE => 'success',
            self::SOLD => 'info',
            self::REFUNDED => 'warning',
            self::INVALID => 'danger',
        };
    }

    public function canTransitionTo(self $to): bool
    {
        return match ($this) {
            self::AVAILABLE => $to === self::SOLD,
            self::SOLD => in_array($to, [self::REFUNDED, self::INVALID]),
            self::REFUNDED => in_array($to, [self::AVAILABLE]),
            self::INVALID => in_array($to, [self::AVAILABLE]),
            default => false,
        };
    }

    public function canBePurchased(): bool
    {
        return $this === self::AVAILABLE;
    }
}
