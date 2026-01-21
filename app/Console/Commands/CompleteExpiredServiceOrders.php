<?php

namespace App\Console\Commands;

use App\Models\ServiceOrder;
use App\Models\Config;
use App\Enums\ServiceOrderStatus;
use App\Services\WalletService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CompleteExpiredServiceOrders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'service-orders:auto-complete-expired {--chunk=50 : Số orders xử lý mỗi lần}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Tự động hoàn thành các đơn hàng dịch vụ mà buyer không phản hồi trong thời gian quy định và cộng tiền cho seller';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $chunkSize = (int) $this->option('chunk');
        $buyerConfirmHours = (int) Config::getConfig('service_order_buyer_confirm_hours', 96);
        
        $this->info("Đang tìm đơn hàng dịch vụ SELLER_CONFIRMED mà buyer chưa phản hồi sau {$buyerConfirmHours} giờ...");
        
        $totalProcessed = 0;
        $totalSuccess = 0;
        $totalFailed = 0;

        // Tìm service orders:
        // - Status = SELLER_CONFIRMED
        // - Không có dispute đang mở
        // - Đã quá deadline (dựa trên thứ tự ưu tiên: seller_reconfirmed_at > last_dispute_resolved_at > last_dispute_created_at > seller_confirmed_at)
        ServiceOrder::where('status', ServiceOrderStatus::SELLER_CONFIRMED)
            ->whereDoesntHave('disputes', function ($query) {
                $query->whereIn('status', [
                    \App\Enums\ServiceDisputeStatus::OPEN,
                    \App\Enums\ServiceDisputeStatus::REVIEWING
                ]);
            })
            ->orderBy('created_at', 'asc')
            ->chunk($chunkSize, function ($orders) use (&$totalProcessed, &$totalSuccess, &$totalFailed, $buyerConfirmHours) {
                foreach ($orders as $order) {
                    // Tính deadline theo thứ tự ưu tiên
                    $deadline = $this->calculateDeadline($order, $buyerConfirmHours);
                    
                    // Kiểm tra xem đã quá deadline chưa
                    if (now()->isBefore($deadline)) {
                        continue; // Chưa quá deadline, bỏ qua
                    }
                    
                    $totalProcessed++;
                    
                    try {
                        DB::transaction(function () use ($order) {
                            $order->update(['status' => ServiceOrderStatus::COMPLETED]);
                            
                            WalletService::paySellerForServiceOrder($order);
                        });
                        
                        $totalSuccess++;
                        $this->line("  ✓ ServiceOrder #{$order->slug} - Hoàn thành, cộng tiền cho seller");
                        
                    } catch (\Exception $e) {
                        $totalFailed++;
                        $this->error("  ✗ ServiceOrder #{$order->slug} - Lỗi: {$e->getMessage()}");
                        
                        Log::error("Hoàn thành đơn hàng dịch vụ #{$order->slug} thất bại: {$e->getMessage()}", [
                            'service_order_id' => $order->id,
                            'service_order_slug' => $order->slug,
                            'buyer_id' => $order->buyer_id,
                            'seller_id' => $order->seller_id,
                            'total_amount' => $order->total_amount,
                            'exception' => $e->getTraceAsString(),
                        ]);
                    }
                }
                
                usleep(100000);
            });

        $this->newLine();
        $this->info("=== KẾT QUẢ ===");
        $this->info("Tổng số đơn hàng đã xử lý: {$totalProcessed}");
        $this->info("Thành công: {$totalSuccess}");
        
        if ($totalFailed > 0) {
            $this->error("Thất bại: {$totalFailed}");
        }

        return Command::SUCCESS;
    }

    /**
     * Tính deadline theo thứ tự ưu tiên
     * 
     * @param ServiceOrder $order
     * @param int $buyerConfirmHours
     * @return \Carbon\Carbon
     */
    private function calculateDeadline(ServiceOrder $order, int $buyerConfirmHours): \Carbon\Carbon
    {
        // Thứ tự ưu tiên:
        // 1. seller_reconfirmed_at (seller báo lại mới nhất)
        if ($order->seller_reconfirmed_at) {
            return $order->seller_reconfirmed_at->copy()->addHours($buyerConfirmHours);
        }

        // 2. last_dispute_resolved_at (ngày giải quyết tranh chấp cuối cùng)
        if ($order->last_dispute_resolved_at) {
            return $order->last_dispute_resolved_at->copy()->addHours($buyerConfirmHours);
        }

        // 3. last_dispute_created_at (ngày tạo khiếu nại cuối cùng)
        if ($order->last_dispute_created_at) {
            return $order->last_dispute_created_at->copy()->addHours($buyerConfirmHours);
        }

        // 4. seller_confirmed_at (lần đầu seller xác nhận)
        if ($order->seller_confirmed_at) {
            return $order->seller_confirmed_at->copy()->addHours($buyerConfirmHours);
        }

        // Fallback: created_at (không nên xảy ra vì order phải có seller_confirmed_at mới ở status SELLER_CONFIRMED)
        return $order->created_at->copy()->addHours($buyerConfirmHours);
    }
}
