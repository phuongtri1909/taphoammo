<?php

namespace App\Enums;

enum ServiceStatus: string
{
    case PENDING = 'pending';
    case APPROVED = 'approved';
    case REJECTED = 'rejected';
    case HIDDEN = 'hidden';
    case BANNED = 'banned';

    public function label(): string
    {
        return match ($this) {
            self::PENDING => 'Chờ duyệt',
            self::APPROVED => 'Đã duyệt',
            self::REJECTED => 'Từ chối',
            self::HIDDEN => 'Đã ẩn',
            self::BANNED => 'Bị cấm',
        };
    }

    public function badgeColor(): string
    {
        return match ($this) {
            self::PENDING => 'warning',
            self::APPROVED => 'success',
            self::REJECTED => 'danger',
            self::HIDDEN => 'secondary',
            self::BANNED => 'danger',
        };
    }

    public function canTransitionTo(self $to): bool
    {
        return match ($this) {
            self::PENDING => in_array($to, [self::APPROVED, self::REJECTED]),
            self::APPROVED => in_array($to, [self::HIDDEN, self::BANNED]),
            self::REJECTED => in_array($to, [self::PENDING]),
            self::HIDDEN => in_array($to, [self::APPROVED, self::BANNED]),
            self::BANNED => in_array($to, [self::PENDING]),
        };
    }

    public function isVisibleToClient(): bool
    {
        return $this === self::APPROVED;
    }

    public function canBePurchased(): bool
    {
        return $this === self::APPROVED;
    }
}
