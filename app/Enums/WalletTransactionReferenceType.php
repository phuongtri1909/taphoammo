<?php

namespace App\Enums;

enum WalletTransactionReferenceType: string
{
    case ORDER = 'orders';
    case SERVICE_ORDER = 'service_orders';
    case REFUND = 'refunds';
    case WITHDRAWAL = 'withdrawals';
    case DEPOSIT = 'deposits';
    case MANUAL_ADJUSTMENT = 'manual_wallet_adjustments';
    case FEATURED_HISTORY = 'featured_histories';
    case AUCTION_BID = 'auction_bids';

    public function label(): string
    {
        return match ($this) {
            self::ORDER => 'Đơn hàng',
            self::SERVICE_ORDER => 'Đơn hàng dịch vụ',
            self::REFUND => 'Hoàn tiền',
            self::WITHDRAWAL => 'Rút tiền',
            self::DEPOSIT => 'Nạp tiền',
            self::MANUAL_ADJUSTMENT => 'Điều chỉnh thủ công',
            self::FEATURED_HISTORY => 'Đề xuất sản phẩm',
            self::AUCTION_BID => 'Đấu giá banner',
        };
    }

    public function badgeColor(): string
    {
        return match ($this) {
            self::ORDER => 'primary',
            self::SERVICE_ORDER => 'primary',
            self::REFUND => 'warning',
            self::WITHDRAWAL => 'info',
            self::DEPOSIT => 'success',
            self::MANUAL_ADJUSTMENT => 'dark',
            self::FEATURED_HISTORY => 'purple',
            self::AUCTION_BID => 'warning',
        };
    }

    public function canTransitionTo(self $to): bool
    {
        return match ($this) {
            self::ORDER => in_array($to, [self::REFUND, self::WITHDRAWAL, self::DEPOSIT, self::SERVICE_ORDER]),
            self::SERVICE_ORDER => in_array($to, [self::REFUND, self::WITHDRAWAL, self::DEPOSIT, self::ORDER]),
            self::REFUND => in_array($to, [self::ORDER, self::SERVICE_ORDER, self::WITHDRAWAL, self::DEPOSIT]),
            self::WITHDRAWAL => in_array($to, [self::ORDER, self::SERVICE_ORDER, self::REFUND, self::DEPOSIT]),
            self::DEPOSIT => in_array($to, [self::ORDER, self::SERVICE_ORDER, self::REFUND, self::WITHDRAWAL]),
            default => false,
        };
    }
}
