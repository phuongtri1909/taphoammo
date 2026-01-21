<?php

namespace App\Enums;

enum ServiceOrderStatus: string
{
    case PENDING = 'pending';
    case PAID = 'paid';
    case SELLER_CONFIRMED = 'seller_confirmed';
    case COMPLETED = 'completed';
    case DISPUTED = 'disputed';
    case PARTIAL_REFUNDED = 'partial_refunded';
    case REFUNDED = 'refunded';

    public function label(): string
    {
        return match ($this) {
            self::PENDING => 'Chờ thanh toán',
            self::PAID => 'Đã thanh toán',
            self::SELLER_CONFIRMED => 'Seller đã xác nhận',
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
            self::SELLER_CONFIRMED => 'success',
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
            self::PAID => in_array($to, [self::SELLER_CONFIRMED, self::REFUNDED, self::DISPUTED]),
            self::SELLER_CONFIRMED => in_array($to, [self::COMPLETED, self::DISPUTED]),
            self::DISPUTED => in_array($to, [self::PARTIAL_REFUNDED, self::REFUNDED, self::COMPLETED, self::PAID]),
            self::PARTIAL_REFUNDED => in_array($to, [self::COMPLETED]),
            default => false,
        };
    }
}
