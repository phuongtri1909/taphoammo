<?php

namespace App\Enums;

enum WalletTransactionType: string
{
    case DEPOSIT = 'deposit';
    case WITHDRAW = 'withdraw';
    case PURCHASE = 'purchase';
    case REFUND = 'refund';
    case COMMISSION = 'commission';
    case SALE = 'sale';
    case MANUAL_ADJUSTMENT = 'manual_adjustment';
    case FEATURED_PURCHASE = 'featured_purchase';

    public function label(): string
    {
        return match ($this) {
            self::DEPOSIT => 'Nạp tiền',
            self::WITHDRAW => 'Rút tiền',
            self::PURCHASE => 'Thanh toán',
            self::REFUND => 'Hoàn tiền',
            self::COMMISSION => 'Hoa hồng',
            self::SALE => 'Bán hàng',
            self::MANUAL_ADJUSTMENT => 'Điều chỉnh thủ công',
            self::FEATURED_PURCHASE => 'Đề xuất sản phẩm',
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
            self::SALE => 'success',
            self::MANUAL_ADJUSTMENT => 'dark',
            self::FEATURED_PURCHASE => 'purple',
        };
    }
}
