<?php

namespace App\Services;

use App\Models\Config;
use App\Models\Dispute;
use App\Models\Order;
use App\Models\Refund;
use App\Models\RefundItem;
use App\Models\ProductValue;
use App\Models\Wallet;
use App\Models\WalletTransaction;
use App\Enums\DisputeStatus;
use App\Enums\OrderStatus;
use App\Enums\RefundStatus;
use App\Enums\ProductValueStatus;
use App\Enums\WalletStatus;
use App\Enums\WalletTransactionType;
use App\Enums\WalletTransactionStatus;
use App\Enums\WalletTransactionReferenceType;
use Illuminate\Support\Facades\DB;

class DisputeService
{
    /**
     * Xử lý chấp nhận khiếu nại
     * - Hoàn tiền cho buyer (các values bị khiếu nại)
     * - Cộng tiền cho seller (các values KHÔNG bị khiếu nại, sau % sàn)
     * 
     * @param Dispute $dispute
     * @param int $resolvedBy ID của người xử lý (seller hoặc admin)
     * @param string|null $adminNote Ghi chú của admin (nếu có)
     * @return Refund
     */
    public static function approveDispute(Dispute $dispute, int $resolvedBy, ?string $adminNote = null): Refund
    {
        return DB::transaction(function () use ($dispute, $resolvedBy, $adminNote) {
            $order = $dispute->order;
            $orderItem = $dispute->orderItem;
            
            $disputedValueIds = $dispute->items->pluck('product_value_id')->toArray();
            $disputedValues = ProductValue::whereIn('id', $disputedValueIds)
                ->lockForUpdate()
                ->get();
            
            $allOrderItemValues = ProductValue::where('order_item_id', $orderItem->id)
                ->where('status', ProductValueStatus::SOLD)
                ->lockForUpdate()
                ->get();
            
            $pricePerValue = $orderItem->price;
            
            $refundAmount = $disputedValues->count() * $pricePerValue;
            
            $nonDisputedValues = $allOrderItemValues->filter(function ($value) use ($disputedValueIds) {
                return !in_array($value->id, $disputedValueIds);
            });
            $sellerValuesCount = $nonDisputedValues->count();
            $sellerTotalBeforeCommission = $sellerValuesCount * $pricePerValue;
            
            $commissionRate = (float) Config::getConfig('commission_rate', 10) / 100;
            $sellerAmount = $sellerTotalBeforeCommission * (1 - $commissionRate);
            $commissionAmount = $sellerTotalBeforeCommission * $commissionRate;
            
            foreach ($disputedValues as $value) {
                $value->update(['status' => ProductValueStatus::REFUNDED->value]);
            }
            
            $refund = Refund::create([
                'order_id' => $order->id,
                'buyer_id' => $order->buyer_id,
                'total_amount' => $refundAmount,
                'status' => RefundStatus::COMPLETED,
                'processed_by' => $resolvedBy,
            ]);
            
            foreach ($disputedValues as $value) {
                RefundItem::create([
                    'refund_id' => $refund->id,
                    'product_value_id' => $value->id,
                    'amount' => $pricePerValue,
                ]);
            }
            
            self::refundToBuyer($order->buyer_id, $refund);
            
            if ($sellerAmount > 0) {
                self::payToSeller($order, $sellerAmount, $commissionAmount, $sellerValuesCount);
            }
            
            $dispute->update([
                'status' => DisputeStatus::APPROVED,
                'resolved_at' => now(),
                'resolved_by' => $resolvedBy,
                'admin_note' => $adminNote,
            ]);
            
            $totalOrderValues = $order->items->sum('quantity');
            $refundedCount = $disputedValues->count();
            
            if ($refundedCount >= $totalOrderValues) {
                $order->update(['status' => OrderStatus::REFUNDED]);
            } else {
                $order->update(['status' => OrderStatus::PARTIAL_REFUNDED]);
            }
            
            return $refund;
        });
    }
    
    private static function refundToBuyer(int $buyerId, Refund $refund): void
    {
        $wallet = Wallet::firstOrCreate(
            ['user_id' => $buyerId],
            ['balance' => 0, 'status' => WalletStatus::ACTIVE]
        );
        
        $wallet = Wallet::where('id', $wallet->id)
            ->lockForUpdate()
            ->first();
        
        $exists = WalletTransaction::where('reference_type', WalletTransactionReferenceType::REFUND->value)
            ->where('reference_id', $refund->id)
            ->where('type', WalletTransactionType::REFUND->value)
            ->exists();
        
        if ($exists) {
            throw new \Exception('Refund đã được xử lý');
        }
        
        $before = $wallet->balance;
        $after = $before + $refund->total_amount;
        
        $wallet->update(['balance' => $after]);
        
        WalletTransaction::create([
            'wallet_id' => $wallet->id,
            'type' => WalletTransactionType::REFUND->value,
            'amount' => $refund->total_amount,
            'balance_before' => $before,
            'balance_after' => $after,
            'reference_type' => WalletTransactionReferenceType::REFUND->value,
            'reference_id' => $refund->id,
            'description' => "Hoàn tiền khiếu nại đơn #{$refund->order->slug}",
            'status' => WalletTransactionStatus::COMPLETED->value,
        ]);
    }
    
    private static function payToSeller(Order $order, float $sellerAmount, float $commissionAmount, int $valuesCount): void
    {
        $wallet = Wallet::firstOrCreate(
            ['user_id' => $order->seller_id],
            ['balance' => 0, 'status' => WalletStatus::ACTIVE]
        );
        
        $wallet = Wallet::where('id', $wallet->id)
            ->lockForUpdate()
            ->first();
        
        $before = $wallet->balance;
        $after = $before + $sellerAmount;
        
        $wallet->update(['balance' => $after]);
        
        WalletTransaction::create([
            'wallet_id' => $wallet->id,
            'type' => WalletTransactionType::SALE->value,
            'amount' => $sellerAmount,
            'balance_before' => $before,
            'balance_after' => $after,
            'reference_type' => WalletTransactionReferenceType::ORDER->value,
            'reference_id' => $order->id,
            'description' => "Tiền bán hàng ({$valuesCount} giá trị) đơn #{$order->slug} (Phí sàn -" . number_format($commissionAmount, 0, ',', '.') . "đ)",
            'status' => WalletTransactionStatus::COMPLETED->value,
        ]);
    }
    
    public static function rejectDispute(Dispute $dispute, int $resolvedBy, string $adminNote): void
    {
        DB::transaction(function () use ($dispute, $resolvedBy, $adminNote) {
            $dispute->update([
                'status' => DisputeStatus::REJECTED,
                'resolved_at' => now(),
                'resolved_by' => $resolvedBy,
                'admin_note' => $adminNote,
            ]);
            
            $order = $dispute->order;
            if ($order->status === OrderStatus::DISPUTED) {
                $hasOtherOpenDisputes = $order->disputes()
                    ->where('id', '!=', $dispute->id)
                    ->whereIn('status', [DisputeStatus::OPEN, DisputeStatus::REVIEWING])
                    ->exists();
                
                if (!$hasOtherOpenDisputes) {
                    $order->update(['status' => OrderStatus::PAID]);
                }
            }
        });
    }
    
    public static function sellerRejectDispute(Dispute $dispute, string $sellerNote): void
    {
        DB::transaction(function () use ($dispute, $sellerNote) {
            $dispute->update([
                'status' => DisputeStatus::REVIEWING,
                'seller_note' => $sellerNote,
            ]);
        });
    }
    
    public static function withdrawDispute(Dispute $dispute): void
    {
        DB::transaction(function () use ($dispute) {
            if (!in_array($dispute->status, [DisputeStatus::OPEN, DisputeStatus::REVIEWING])) {
                throw new \Exception('Không thể rút khiếu nại này');
            }
            
            $dispute->update([
                'status' => DisputeStatus::WITHDRAWN,
                'resolved_at' => now(),
            ]);
            
            $order = $dispute->order;
            if ($order->status === OrderStatus::DISPUTED) {
                $hasOtherOpenDisputes = $order->disputes()
                    ->where('id', '!=', $dispute->id)
                    ->whereIn('status', [DisputeStatus::OPEN, DisputeStatus::REVIEWING])
                    ->exists();
                
                if (!$hasOtherOpenDisputes) {
                    $order->update(['status' => OrderStatus::PAID]);
                }
            }
        });
    }
}

