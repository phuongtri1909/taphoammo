<?php

namespace App\Enums;

enum DepositStatus: string
{
    case PENDING = 'pending';
    case SUCCESS = 'success';
    case FAILED = 'failed';
    case CANCELLED = 'cancelled';
    case EXPIRED = 'expired';

    public function label(): string
    {
        return match ($this) {
            self::PENDING => 'Đang xử lý',
            self::SUCCESS => 'Thành công',
            self::FAILED => 'Thất bại',
            self::CANCELLED => 'Đã hủy',
            self::EXPIRED => 'Hết hạn',
        };
    }

    public function badgeColor(): string
    {
        return match ($this) {
            self::PENDING => 'warning',
            self::SUCCESS => 'success',
            self::FAILED => 'danger',
            self::CANCELLED => 'secondary',
            self::EXPIRED => 'dark',
        };
    }

    public function canTransitionTo(self $to): bool
    {
        return match ($this) {
            self::PENDING => in_array($to, [self::SUCCESS, self::FAILED, self::CANCELLED, self::EXPIRED]),
            self::SUCCESS, self::FAILED, self::CANCELLED, self::EXPIRED => false,
        };
    }
}
