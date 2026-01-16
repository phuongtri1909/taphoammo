<?php

namespace App\Enums;

enum WalletTransactionType: string
{
    case DEPOSIT = 'deposit';
    case WITHDRAW = 'withdraw';
    case PURCHASE = 'purchase';
    case REFUND = 'refund';
    case COMMISSION = 'commission';

    public function label(): string
    {
        return match ($this) {
            self::DEPOSIT => 'Nạp tiền',
            self::WITHDRAW => 'Rút tiền',
            self::PURCHASE => 'Thanh toán',
            self::REFUND => 'Hoàn tiền',
            self::COMMISSION => 'Hoa hồng',
        };
    }

    public function badgeColor(): string
    {
        return match ($this) {
            self::DEPOSIT => 'success',
            self::WITHDRAW => 'info',
            self::PURCHASE => 'primary',
            self::REFUND => 'warning',
            self::COMMISSION => 'secondary',
        };
    }
}
