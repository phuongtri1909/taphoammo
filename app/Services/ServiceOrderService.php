<?php

namespace App\Services;

use App\Models\ServiceOrder;
use App\Models\ServiceVariant;
use App\Models\Service;
use App\Models\ServiceRefund;
use App\Models\Wallet;
use App\Models\Config;
use App\Enums\ServiceOrderStatus;
use App\Enums\ServiceDisputeStatus;
use App\Enums\ServiceRefundStatus;
use App\Services\WalletService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ServiceOrderService
{
    /**
     * Mua dịch vụ
     * 
     * @param int $buyerId
     * @param string $serviceSlug
     * @param string|null $variantSlug
     * @return ServiceOrder
     * @throws \DomainException
     * @throws \RuntimeException
     */
    public function buy(
        int $buyerId,
        string $serviceSlug,
        ?string $variantSlug = null,
        ?string $note = null
    ): ServiceOrder {
        $maxAttempts = 3;
        $attempt = 0;
        
        while ($attempt < $maxAttempts) {
            try {
                return $this->executePurchase($buyerId, $serviceSlug, $variantSlug, $note);
                
            } catch (\Illuminate\Database\QueryException $e) {
                // Deadlock (1213) , Lock wait timeout (1205)
                if (in_array($e->getCode(), [1213, 1205]) && $attempt < $maxAttempts - 1) {
                    $attempt++;
                    usleep(10000 * pow(2, $attempt - 1));
                    
                    Log::warning('Database khóa bị xung đột khi mua dịch vụ, thử lại', [
                        'attempt' => $attempt,
                        'buyer_id' => $buyerId,
                        'variant_slug' => $variantSlug,
                        'error_code' => $e->getCode(),
                    ]);
                    continue;
                }
                
                Log::error('Đơn hàng mua dịch vụ lỗi database', [
                    'buyer_id' => $buyerId,
                    'service_slug' => $serviceSlug,
                    'variant_slug' => $variantSlug,
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
                Log::error('Service order purchase system error', [
                    'buyer_id' => $buyerId,
                    'service_slug' => $serviceSlug,
                    'variant_slug' => $variantSlug,
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString(),
                ]);
                
                throw new \RuntimeException('Có lỗi hệ thống. Vui lòng thử lại sau.');
            }
        }
        
        throw new \RuntimeException('Hệ thống đang bận. Vui lòng thử lại sau.');
    }

    private function executePurchase(
        int $buyerId,
        string $serviceSlug,
        ?string $variantSlug = null,
        ?string $note = null
    ): ServiceOrder {
        return DB::transaction(function () use ($buyerId, $serviceSlug, $variantSlug, $note) {
            $wallet = Wallet::where('user_id', $buyerId)
                ->lockForUpdate()
                ->first();

            if (!$wallet) {
                throw new \DomainException('Bạn không đủ tiền trong ví, vui lòng nạp thêm tiền');
            }

            $service = Service::with('seller')
                ->where('slug', $serviceSlug)
                ->first();

            if (!$service) {
                throw new \DomainException('Dịch vụ không tồn tại');
            }

            if (!$service->isVisibleToClient()) {
                throw new \DomainException('Dịch vụ không thể mua được');
            }

            if ($service->seller_id === $buyerId) {
                throw new \DomainException('Không thể mua dịch vụ của chính mình');
            }

            if (!$variantSlug) {
                throw new \DomainException('Vui lòng chọn biến thể dịch vụ');
            } else {
                $variant = ServiceVariant::where('slug', $variantSlug)
                    ->where('service_id', $service->id)
                    ->first();

                if (!$variant) {
                    throw new \DomainException('Biến thể không tồn tại hoặc không thuộc dịch vụ này');
                }

                if (!$variant->isVisibleToClient()) {
                    throw new \DomainException('Biến thể này đã bị ẩn hoặc không còn khả dụng');
                }

                if (!$variant->isPurchasable()) {
                    throw new \DomainException('Biến thể này không thể mua được');
                }
            }

            $total = $variant->price;

            if (!$wallet->hasEnoughBalance($total)) {
                $formatted = number_format($total, 0, ',', '.');
                $balance = number_format($wallet->balance, 0, ',', '.');
                throw new \DomainException("Số dư không đủ. Cần {$formatted}đ, hiện có {$balance}đ");
            }

            $serviceOrder = ServiceOrder::create([
                'buyer_id' => $buyerId,
                'seller_id' => $service->seller_id,
                'service_variant_id' => $variant->id,
                'total_amount' => $total,
                'status' => ServiceOrderStatus::PAID->value,
                'note' => $note ? trim($note) : null,
            ]);

            $variant->incrementSold(1);

            WalletService::purchaseServiceOrderWithWallet($wallet, $serviceOrder);

            $serviceOrder = $serviceOrder->fresh(['buyer', 'seller', 'serviceVariant.service']);

            try {
                $telegramService = new TelegramNotificationService();
                $telegramService->sendServiceOrderNotification($serviceOrder);
                $telegramService->sendServiceOrderNotificationToBuyer($serviceOrder);
                $telegramService->sendServiceOrderNotificationToSeller($serviceOrder);
            } catch (\Exception $e) {
                Log::warning('Không thể gửi thông báo Telegram cho đơn hàng dịch vụ', [
                    'service_order_id' => $serviceOrder->id,
                    'error' => $e->getMessage()
                ]);
            }

            return $serviceOrder;
        });
    }

    /**
     * Seller từ chối nhận đơn hàng (hoàn tiền cho buyer)
     * 
     * @param ServiceOrder $serviceOrder
     * @param int $sellerId
     * @return ServiceOrder
     * @throws \DomainException
     */
    public function rejectOrder(ServiceOrder $serviceOrder, int $sellerId): ServiceOrder
    {
        if ($serviceOrder->seller_id !== $sellerId) {
            throw new \DomainException('Bạn không có quyền thực hiện hành động này');
        }

        if (!in_array($serviceOrder->status, [ServiceOrderStatus::PAID, ServiceOrderStatus::DISPUTED], true)) {
            throw new \DomainException('Chỉ có thể từ chối đơn hàng đang chờ xử lý hoặc đang có khiếu nại');
        }

        $completionHours = (int) Config::getConfig('service_order_completion_hours', 96);
        $baseTime = $serviceOrder->last_dispute_created_at ?? $serviceOrder->created_at;
        $deadline = $baseTime->copy()->addHours($completionHours);

        if (now()->isAfter($deadline)) {
            throw new \DomainException('Đã quá thời hạn để từ chối nhận đơn hàng');
        }

        return DB::transaction(function () use ($serviceOrder, $sellerId) {
            $serviceOrder->update([
                'status' => ServiceOrderStatus::REFUNDED,
            ]);

            $openDisputes = $serviceOrder->disputes()
                ->whereIn('status', [ServiceDisputeStatus::OPEN, ServiceDisputeStatus::REVIEWING])
                ->get();

            foreach ($openDisputes as $dispute) {
                $dispute->update([
                    'status' => ServiceDisputeStatus::APPROVED,
                    'seller_note' => $dispute->seller_note ?: 'Seller từ chối nhận đơn hàng và đã hoàn tiền cho người mua.',
                    'resolved_at' => now(),
                    'resolved_by' => $sellerId,
                ]);
            }

            ServiceRefund::create([
                'service_order_id' => $serviceOrder->id,
                'buyer_id' => $serviceOrder->buyer_id,
                'total_amount' => $serviceOrder->total_amount,
                'status' => ServiceRefundStatus::COMPLETED,
                'processed_by' => $sellerId,
            ]);

            WalletService::refundForServiceOrder($serviceOrder);

            return $serviceOrder->fresh();
        });
    }

    public function confirmCompletion(ServiceOrder $serviceOrder, int $sellerId): ServiceOrder
    {
        if ($serviceOrder->seller_id !== $sellerId) {
            throw new \DomainException('Bạn không có quyền thực hiện hành động này');
        }

        if ($serviceOrder->status !== ServiceOrderStatus::PAID) {
            throw new \DomainException('Đơn hàng không ở trạng thái chờ xác nhận');
        }

        $completionHours = (int) Config::getConfig('service_order_completion_hours', 96);
        
        if ($serviceOrder->last_dispute_resolved_at) {
            $deadline = $serviceOrder->last_dispute_resolved_at->copy()->addHours($completionHours);
        } else {
            $deadline = $serviceOrder->created_at->copy()->addHours($completionHours);
        }
        
        if (now()->isAfter($deadline)) {
            throw new \DomainException('Đã quá thời hạn xác nhận hoàn thành dịch vụ');
        }

        return DB::transaction(function () use ($serviceOrder) {
            $updateData = [
                'status' => ServiceOrderStatus::SELLER_CONFIRMED,
            ];
            
            if ($serviceOrder->last_dispute_resolved_at) {
                $updateData['seller_reconfirmed_at'] = now();
            } else {
                $updateData['seller_confirmed_at'] = now();
            }
            
            $serviceOrder->update($updateData);

            return $serviceOrder->fresh();
        });
    }

    /**
     * Buyer xác nhận đơn hàng đã đúng yêu cầu
     * 
     * @param ServiceOrder $serviceOrder
     * @param int $buyerId
     * @return ServiceOrder
     * @throws \DomainException
     */
    public function confirmOrder(ServiceOrder $serviceOrder, int $buyerId): ServiceOrder
    {
        if ($serviceOrder->buyer_id !== $buyerId) {
            throw new \DomainException('Bạn không có quyền thực hiện hành động này');
        }

        if ($serviceOrder->status !== ServiceOrderStatus::SELLER_CONFIRMED) {
            throw new \DomainException('Đơn hàng không ở trạng thái chờ xác nhận');
        }

        if ($serviceOrder->disputes()->whereIn('status', [
            \App\Enums\ServiceDisputeStatus::OPEN,
            \App\Enums\ServiceDisputeStatus::REVIEWING
        ])->exists()) {
            throw new \DomainException('Đơn hàng đang có khiếu nại chưa được xử lý');
        }

        return DB::transaction(function () use ($serviceOrder) {
            $serviceOrder->update([
                'status' => ServiceOrderStatus::COMPLETED,
            ]);

            WalletService::paySellerForServiceOrder($serviceOrder);

            return $serviceOrder->fresh();
        });
    }

    /**
     * Tính deadline cho buyer xác nhận (khi status = SELLER_CONFIRMED)
     * 
     * @param ServiceOrder $serviceOrder
     * @return \Carbon\Carbon
     */
    public function getBuyerConfirmDeadline(ServiceOrder $serviceOrder): \Carbon\Carbon
    {
        $buyerConfirmHours = (int) Config::getConfig('service_order_buyer_confirm_hours', 96);

        if ($serviceOrder->seller_reconfirmed_at) {
            return $serviceOrder->seller_reconfirmed_at->copy()->addHours($buyerConfirmHours);
        }

        if ($serviceOrder->seller_confirmed_at) {
            return $serviceOrder->seller_confirmed_at->copy()->addHours($buyerConfirmHours);
        }

        return $serviceOrder->created_at->copy()->addHours($buyerConfirmHours);
    }

    /**
     * Reset deadline khi buyer tạo dispute (lần 1)
     * 
     * @param ServiceOrder $serviceOrder
     * @return void
     */
    public function resetDeadlineOnDisputeCreated(ServiceOrder $serviceOrder): void
    {
        $serviceOrder->update([
            'last_dispute_created_at' => now(),
            'status' => ServiceOrderStatus::DISPUTED,
        ]);
    }

   
    public function resetDeadlineOnDisputeResolved(ServiceOrder $serviceOrder): void
    {
        $serviceOrder->update([
            'last_dispute_resolved_at' => now(),
            'status' => ServiceOrderStatus::PAID,
            'seller_reconfirmed_at' => null,
        ]);
    }
}