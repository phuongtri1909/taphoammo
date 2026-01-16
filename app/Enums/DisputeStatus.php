<?php

namespace App\Enums;

enum DisputeStatus: string
{
    case OPEN = 'open';
    case REVIEWING = 'reviewing';
    case APPROVED = 'approved';
    case REJECTED = 'rejected';

    public function label(): string
    {
        return match ($this) {
            self::OPEN => 'Mở',
            self::REVIEWING => 'Đang xem xét',
            self::APPROVED => 'Chấp nhận',
            self::REJECTED => 'Từ chối',
        };
    }

    public function badgeColor(): string
    {
        return match ($this) {
            self::OPEN => 'warning',
            self::REVIEWING => 'info',
            self::APPROVED => 'success',
            self::REJECTED => 'danger',
        };
    }

    public function canTransitionTo(self $to): bool
    {
        return match ($this) {
            self::OPEN => $to === self::REVIEWING,
            self::REVIEWING => in_array($to, [self::APPROVED, self::REJECTED]),
            default => false,
        };
    }
}
