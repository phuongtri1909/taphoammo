<?php

namespace App\Services;

use App\Models\Order;
use App\Models\ProductVariant;
use App\Models\ProductValue;
use App\Models\OrderItem;
use App\Models\User;
use App\Models\Wallet;
use App\Models\Product;
use App\Enums\OrderStatus;
use App\Enums\ProductValueStatus;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class OrderService
{
    /**
     * Mua sản phẩm
     * 
     * @param int $buyerId
     * @param string $productSlug
     * @param string|null $variantSlug
     * @param int $qty
     * @return Order
     * @throws \DomainException
     * @throws \RuntimeException
     */
    public function buy(
        int $buyerId,
        string $productSlug,
        ?string $variantSlug,
        int $qty
    ): Order {
        $maxAttempts = 3;
        $attempt = 0;
        
        while ($attempt < $maxAttempts) {
            try {
                return $this->executePurchase($buyerId, $productSlug, $variantSlug, $qty);
                
            } catch (\Illuminate\Database\QueryException $e) {
                // Deadlock (1213) , Lock wait timeout (1205)
                if (in_array($e->getCode(), [1213, 1205]) && $attempt < $maxAttempts - 1) {
                    $attempt++;
                    usleep(10000 * pow(2, $attempt - 1));
                    
                    Log::warning('Database khóa bị xung đột, thử lại', [
                        'attempt' => $attempt,
                        'buyer_id' => $buyerId,
                        'variant_slug' => $variantSlug,
                        'error_code' => $e->getCode(),
                    ]);
                    continue;
                }
                
                Log::error('Đơn hàng mua hàng lỗi database', [
                    'buyer_id' => $buyerId,
                    'product_slug' => $productSlug,
                    'variant_slug' => $variantSlug,
                    'qty' => $qty,
                    'error' => $e->getMessage(),
                    'code' => $e->getCode(),
                ]);
                
                if ($e->getCode() == 1213) {
                    throw new \RuntimeException('Hệ thống đang bận. Vui lòng thử lại.');
                }
                
                throw new \RuntimeException('Có lỗi xảy ra khi xử lý đơn hàng. Vui lòng thử lại.');
                
            } catch (\DomainException $e) {
                throw $e;
                
            } catch (\Throwable $e) {
                Log::error('Order purchase system error', [
                    'buyer_id' => $buyerId,
                    'product_slug' => $productSlug,
                    'variant_slug' => $variantSlug,
                    'qty' => $qty,
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString(),
                ]);
                
                throw new \RuntimeException('Có lỗi hệ thống. Vui lòng thử lại sau.');
            }
        }
        
        throw new \RuntimeException('Hệ thống đang bận. Vui lòng thử lại sau.');
    }

    /**
     * Thực thi logic mua hàng
     */
    private function executePurchase(
        int $buyerId,
        string $productSlug,
        ?string $variantSlug,
        int $qty
    ): Order {
        return DB::transaction(function () use ($buyerId, $productSlug, $variantSlug, $qty) {
            if ($qty <= 0) {
                throw new \DomainException('Số lượng phải lớn hơn 0');
            }            
            $wallet = Wallet::where('user_id', $buyerId)
                ->lockForUpdate()
                ->first();

            if (!$wallet) {
                throw new \DomainException('Bạn không đủ tiền trong ví, vui lòng nạp thêm tiền');
            }

            $product = Product::with('seller')
                ->where('slug', $productSlug)
                ->first();

            if (!$product) {
                throw new \DomainException('Sản phẩm không tồn tại');
            }

            if (!$product->isPurchasable()) {
                throw new \DomainException('Sản phẩm không thể mua được');
            }

            if ($product->seller_id === $buyerId) {
                throw new \DomainException('Không thể mua sản phẩm của chính mình');
            }

            if (!$variantSlug) {
                throw new \DomainException('Vui lòng chọn biến thể sản phẩm');
            }

            $variant = ProductVariant::where('slug', $variantSlug)
                ->where('product_id', $product->id)
                ->first();

            if (!$variant) {
                throw new \DomainException('Biến thể không tồn tại hoặc không thuộc sản phẩm này');
            }

            if (!$variant->isPurchasable()) {
                throw new \DomainException('Biến thể không thể mua được');
            }

            $total = $variant->price * $qty;

            if (!$wallet->hasEnoughBalance($total)) {
                $formatted = number_format($total, 0, ',', '.');
                $balance = number_format($wallet->balance, 0, ',', '.');
                throw new \DomainException("Số dư không đủ. Cần {$formatted}đ, hiện có {$balance}đ");
            }

            $order = Order::create([
                'buyer_id' => $buyerId,
                'seller_id' => $product->seller_id,
                'total_amount' => $total,
                'status' => OrderStatus::PAID->value,
            ]);

            $orderItem = OrderItem::create([
                'order_id' => $order->id,
                'product_variant_id' => $variant->id,
                'quantity' => $qty,
                'price' => $variant->price,
            ]);

            $affectedRows = DB::update("
                UPDATE product_values 
                SET 
                    status = ?,
                    order_id = ?,
                    order_item_id = ?,
                    sold_to_user_id = ?,
                    sold_at = NOW(),
                    updated_at = NOW()
                WHERE product_variant_id = ?
                  AND status = ?
                ORDER BY id ASC
                LIMIT ?
            ", [
                ProductValueStatus::SOLD->value,
                $order->id,
                $orderItem->id,
                $buyerId,
                $variant->id,
                ProductValueStatus::AVAILABLE->value,
                $qty
            ]);

            if ($affectedRows < $qty) {
                throw new \DomainException(
                    $affectedRows === 0 
                        ? 'Sản phẩm đã hết hàng. Vui lòng thử lại sau.' 
                        : "Không đủ hàng trong kho. Bạn muốn mua {$qty} sản phẩm nhưng chỉ còn {$affectedRows} sản phẩm"
                );
            }

            DB::update("
                UPDATE product_variants 
                SET 
                    sold_count = sold_count + ?,
                    stock_quantity = stock_quantity - ?,
                    updated_at = NOW()
                WHERE id = ?
            ", [$qty, $qty, $variant->id]);

            WalletService::purchaseWithWallet($wallet, $order);

            return $order->fresh(['buyer', 'seller', 'items']);
        });
    }
}