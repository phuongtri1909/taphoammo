<?php

namespace App\Enums;

enum WithdrawalStatus: string
{
    case PENDING_OTP = 'pending_otp';
    case PENDING = 'pending';
    case PROCESSING = 'processing';
    case COMPLETED = 'completed';
    case REJECTED = 'rejected';
    case CANCELLED = 'cancelled';

    public function label(): string
    {
        return match ($this) {
            self::PENDING_OTP => 'Chờ xác thực OTP',
            self::PENDING => 'Chờ xử lý',
            self::PROCESSING => 'Đang xử lý',
            self::COMPLETED => 'Hoàn thành',
            self::REJECTED => 'Từ chối',
            self::CANCELLED => 'Đã hủy',
        };
    }

    public function badgeColor(): string
    {
        return match ($this) {
            self::PENDING_OTP => 'info',
            self::PENDING => 'warning',
            self::PROCESSING => 'primary',
            self::COMPLETED => 'success',
            self::REJECTED => 'danger',
            self::CANCELLED => 'secondary',
        };
    }

    public function canTransitionTo(self $to): bool
    {
        return match ($this) {
            self::PENDING_OTP => in_array($to, [self::PENDING, self::CANCELLED]),
            self::PENDING => in_array($to, [self::PROCESSING, self::REJECTED, self::CANCELLED]),
            self::PROCESSING => in_array($to, [self::COMPLETED, self::REJECTED]),
            self::COMPLETED, self::REJECTED, self::CANCELLED => false,
        };
    }
}
