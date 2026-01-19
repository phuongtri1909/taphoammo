<?php

namespace App\Enums;

enum DisputeStatus: string
{
    case OPEN = 'open';
    case REVIEWING = 'reviewing';
    case APPROVED = 'approved';
    case REJECTED = 'rejected';
    case WITHDRAWN = 'withdrawn';

    public function label(): string
    {
        return match ($this) {
            self::OPEN => 'Mở',
            self::REVIEWING => 'Đang xem xét',
            self::APPROVED => 'Chấp nhận',
            self::REJECTED => 'Từ chối',
            self::WITHDRAWN => 'Đã rút',
        };
    }

    public function badgeColor(): string
    {
        return match ($this) {
            self::OPEN => 'warning',
            self::REVIEWING => 'info',
            self::APPROVED => 'success',
            self::REJECTED => 'danger',
            self::WITHDRAWN => 'secondary',
        };
    }

    public function canTransitionTo(self $to): bool
    {
        return match ($this) {
            self::OPEN => in_array($to, [self::REVIEWING, self::APPROVED, self::WITHDRAWN]),
            self::REVIEWING => in_array($to, [self::APPROVED, self::REJECTED, self::WITHDRAWN]),
            default => false,
        };
    }
}
