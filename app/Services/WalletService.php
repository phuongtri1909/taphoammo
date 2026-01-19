<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Config;
use App\Models\Refund;
use App\Models\Wallet;
use App\Models\WalletTransaction;
use App\Enums\WalletTransactionType;
use App\Enums\WalletTransactionStatus;
use App\Enums\WalletTransactionReferenceType;

class WalletService
{
    public static function refund(int $userId, Refund $refund): void
    {
        $wallet = Wallet::where('user_id', $userId)
            ->lockForUpdate()
            ->firstOrFail();

        $exists = WalletTransaction::where('reference_type', WalletTransactionReferenceType::REFUND->value)
            ->where('reference_id', $refund->id)
            ->where('type', WalletTransactionType::REFUND->value)
            ->exists();

        if ($exists) {
            throw new \Exception('Refund đã được xử lý');
        }

        $before = $wallet->balance;
        $after  = $before + $refund->total_amount;

        $wallet->update([
            'balance' => $after,
        ]);

        WalletTransaction::create([
            'wallet_id' => $wallet->id,
            'type' => WalletTransactionType::REFUND->value,
            'amount' => $refund->total_amount,
            'balance_before' => $before,
            'balance_after' => $after,
            'reference_type' => WalletTransactionReferenceType::REFUND->value,
            'reference_id' => $refund->id,
            'description' => "Hoàn trả đơn hàng #{$refund->order_id}",
            'status' => WalletTransactionStatus::COMPLETED->value,
        ]);
    }

    /**
     * Mua hàng - lock và trừ tiền từ ví
     * @deprecated Use purchaseWithWallet() instead for better performance
     */
    public static function purchase(int $userId, Order $order): void
    {
        $wallet = Wallet::where('user_id', $userId)
            ->lockForUpdate()
            ->firstOrFail();

        self::purchaseWithWallet($wallet, $order);
    }

    /**
     * Mua hàng - trừ tiền từ ví đã được lock
     * Sử dụng khi wallet đã được lock trong transaction trước đó
     * 
     * @param Wallet $wallet Wallet đã được lock
     * @param Order $order Đơn hàng
     * @throws \Exception
     */
    public static function purchaseWithWallet(Wallet $wallet, Order $order): void
    {
        if ($wallet->balance < $order->total_amount) {
            $formatted = number_format($order->total_amount, 0, ',', '.');
            $balance = number_format($wallet->balance, 0, ',', '.');
            throw new \Exception("Số dư không đủ. Cần {$formatted}đ, hiện có {$balance}đ");
        }

        $exists = WalletTransaction::where('reference_type', WalletTransactionReferenceType::ORDER->value)
            ->where('reference_id', $order->id)
            ->where('type', WalletTransactionType::PURCHASE->value)
            ->exists();

        if ($exists) {
            throw new \Exception('Đơn hàng đã được thanh toán');
        }

        $before = $wallet->balance;
        $after  = $before - $order->total_amount;

        $wallet->update([
            'balance' => $after,
        ]);

        WalletTransaction::create([
            'wallet_id' => $wallet->id,
            'type' => WalletTransactionType::PURCHASE->value,
            'amount' => $order->total_amount,
            'balance_before' => $before,
            'balance_after' => $after,
            'reference_type' => WalletTransactionReferenceType::ORDER->value,
            'reference_id' => $order->id,
            'description' => "Mua hàng đơn hàng #{$order->slug}",
            'status' => WalletTransactionStatus::COMPLETED->value,
        ]);
    }

    /**
     * Thanh toán tiền bán hàng cho seller (đã trừ phí sàn)
     * 
     * @param Order $order Đơn hàng đã hoàn thành
     * @throws \Exception
     */
    public static function paySellerForOrder(Order $order): void
    {
        $wallet = Wallet::firstOrCreate(
            ['user_id' => $order->seller_id],
            ['balance' => 0, 'status' => \App\Enums\WalletStatus::ACTIVE]
        );
        
        $wallet = Wallet::where('id', $wallet->id)
            ->lockForUpdate()
            ->first();

        $exists = WalletTransaction::where('reference_type', WalletTransactionReferenceType::ORDER->value)
            ->where('reference_id', $order->id)
            ->where('type', WalletTransactionType::SALE->value)
            ->exists();

        if ($exists) {
            throw new \Exception('Đơn hàng đã được thanh toán cho seller');
        }

        $commissionRate = (float) Config::getConfig('commission_rate', 10) / 100;
        $sellerAmount = $order->total_amount * (1 - $commissionRate);
        $commissionAmount = $order->total_amount * $commissionRate;

        $before = $wallet->balance;
        $after = $before + $sellerAmount;

        $wallet->update([
            'balance' => $after,
        ]);

        WalletTransaction::create([
            'wallet_id' => $wallet->id,
            'type' => WalletTransactionType::SALE->value,
            'amount' => $sellerAmount,
            'balance_before' => $before,
            'balance_after' => $after,
            'reference_type' => WalletTransactionReferenceType::ORDER->value,
            'reference_id' => $order->id,
            'description' => "Tiền bán hàng đơn #{$order->slug} (Phí sàn -" . number_format($commissionAmount, 0, ',', '.') . "đ)",
            'status' => WalletTransactionStatus::COMPLETED->value,
        ]);
    }
}
