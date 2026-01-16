<?php

namespace App\Enums;

enum OrderStatus: string
{
    case PENDING = 'pending';
    case PAID = 'paid';
    case COMPLETED = 'completed';
    case DISPUTED = 'disputed';
    case PARTIAL_REFUNDED = 'partial_refunded';
    case REFUNDED = 'refunded';

    public function label(): string
    {
        return match ($this) {
            self::PENDING => 'Chờ thanh toán',
            self::PAID => 'Đã thanh toán',
            self::COMPLETED => 'Hoàn thành',
            self::DISPUTED => 'Đang tranh chấp',
            self::PARTIAL_REFUNDED => 'Hoàn tiền một phần',
            self::REFUNDED => 'Đã hoàn tiền',
        };
    }

    public function badgeColor(): string
    {
        return match ($this) {
            self::PENDING => 'warning',
            self::PAID => 'info',
            self::COMPLETED => 'success',
            self::DISPUTED => 'danger',
            self::PARTIAL_REFUNDED => 'warning',
            self::REFUNDED => 'secondary',
        };
    }

    public function canTransitionTo(self $to): bool
    {
        return match ($this) {
            self::PENDING => in_array($to, [self::PAID]),
            self::PAID => in_array($to, [self::COMPLETED, self::DISPUTED]),
            self::DISPUTED => in_array($to, [self::PARTIAL_REFUNDED, self::REFUNDED]),
            self::PARTIAL_REFUNDED => in_array($to, [self::COMPLETED]),
            default => false,
        };
    }
}
