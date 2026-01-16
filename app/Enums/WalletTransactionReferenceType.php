<?php

namespace App\Enums;

enum WalletTransactionReferenceType: string
{
    case ORDER = 'orders';
    case REFUND = 'refunds';
    case WITHDRAWAL = 'withdrawals';
    case DEPOSIT = 'deposits';

    public function label(): string
    {
        return match ($this) {
            self::ORDER => 'Đơn hàng',
            self::REFUND => 'Hoàn tiền',
            self::WITHDRAWAL => 'Rút tiền',
            self::DEPOSIT => 'Nạp tiền',
        };
    }

    public function badgeColor(): string
    {
        return match ($this) {
            self::ORDER => 'primary',
            self::REFUND => 'warning',
            self::WITHDRAWAL => 'info',
            self::DEPOSIT => 'success',
        };
    }

    public function canTransitionTo(self $to): bool
    {
        return match ($this) {
            self::ORDER => in_array($to, [self::REFUND, self::WITHDRAWAL, self::DEPOSIT]),
            self::REFUND => in_array($to, [self::ORDER, self::WITHDRAWAL, self::DEPOSIT]),
            self::WITHDRAWAL => in_array($to, [self::ORDER, self::REFUND, self::DEPOSIT]),
            self::DEPOSIT => in_array($to, [self::ORDER, self::REFUND, self::WITHDRAWAL]),
            default => false,
        };
    }
}
