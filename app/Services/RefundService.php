<?php

namespace App\Services;

use App\Models\Refund;
use App\Models\ProductValue;
use App\Models\Order;
use App\Enums\ProductValueStatus;
use App\Enums\OrderStatus;
use App\Enums\RefundStatus;
use Illuminate\Support\Facades\DB;
use App\Services\WalletService;
use App\Models\RefundItem;

class RefundService
{
    public function refund(array $productValueIds, int $adminId): Refund
    {
        return DB::transaction(function () use ($productValueIds, $adminId) {

            $values = ProductValue::whereIn('id', $productValueIds)
                ->lockForUpdate()
                ->get();

            $order = Order::findOrFail($values->first()->order_id);
            $total = 0;

            foreach ($values as $value) {
                if ($value->status !== ProductValueStatus::SOLD->value) {
                    throw new \Exception('Trạng thái giá trị sản phẩm không hợp lệ');
                }

                $price = $value->orderItem->price;
                $total += $price;

                $value->update([
                    'status' => ProductValueStatus::REFUNDED->value,
                ]);
            }

            $refund = Refund::create([
                'order_id' => $order->id,
                'buyer_id' => $order->buyer_id,
                'total_amount' => $total,
                'status' => RefundStatus::COMPLETED->value,
                'processed_by' => $adminId,
            ]);

            foreach ($values as $value) {
                RefundItem::create([
                    'refund_id' => $refund->id,
                    'product_value_id' => $value->id,
                    'amount' => $value->orderItem->price,
                ]);
            }

            WalletService::refund($order->buyer_id, $refund);

            $order->changeStatus(
                $values->count() === $order->orderItems->sum('quantity')
                    ? OrderStatus::REFUNDED
                    : OrderStatus::PARTIAL_REFUNDED
            );

            return $refund;
        });
    }
}
