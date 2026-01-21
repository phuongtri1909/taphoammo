<?php

namespace App\Console\Commands;

use App\Models\ServiceOrder;
use App\Models\ServiceRefund;
use App\Models\Config;
use App\Enums\ServiceOrderStatus;
use App\Enums\ServiceRefundStatus;
use App\Services\WalletService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class RefundExpiredServiceOrders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'service-orders:auto-refund-expired {--chunk=50 : Số orders xử lý mỗi lần}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Tự động hoàn tiền các đơn hàng dịch vụ mà seller không xác nhận hoàn thành trong thời gian quy định';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $chunkSize = (int) $this->option('chunk');
        $completionHours = (int) Config::getConfig('service_order_completion_hours', 96);
        $deadline = now()->subHours($completionHours);
        
        $this->info("Đang tìm đơn hàng dịch vụ PAID mà seller chưa xác nhận sau {$completionHours} giờ (trước {$deadline->format('Y-m-d H:i:s')})...");
        
        $totalProcessed = 0;
        $totalSuccess = 0;
        $totalFailed = 0;

        // Tìm service orders:
        // - Status = PAID (chưa được seller xác nhận)
        // - seller_confirmed_at = NULL
        // - created_at đã quá hạn
        // - Không có dispute đang mở (nếu có dispute, deadline đã được reset)
        ServiceOrder::where('status', ServiceOrderStatus::PAID)
            ->whereNull('seller_confirmed_at')
            ->where(function ($query) use ($deadline) {
                // Trường hợp không có dispute: dựa trên created_at
                $query->where(function ($q) use ($deadline) {
                    $q->whereNull('last_dispute_created_at')
                      ->where('created_at', '<=', $deadline);
                })
                // Trường hợp có dispute đã tạo: dựa trên last_dispute_created_at
                ->orWhere(function ($q) use ($deadline) {
                    $q->whereNotNull('last_dispute_created_at')
                      ->where('last_dispute_created_at', '<=', $deadline);
                });
            })
            ->whereDoesntHave('disputes', function ($query) {
                $query->whereIn('status', [
                    \App\Enums\ServiceDisputeStatus::OPEN,
                    \App\Enums\ServiceDisputeStatus::REVIEWING
                ]);
            })
            ->orderBy('created_at', 'asc')
            ->chunk($chunkSize, function ($orders) use (&$totalProcessed, &$totalSuccess, &$totalFailed) {
                foreach ($orders as $order) {
                    $totalProcessed++;
                    
                    try {
                        DB::transaction(function () use ($order) {
                            $order->update(['status' => ServiceOrderStatus::REFUNDED]);
                            
                            // Tạo ServiceRefund record
                            ServiceRefund::create([
                                'service_order_id' => $order->id,
                                'buyer_id' => $order->buyer_id,
                                'total_amount' => $order->total_amount,
                                'status' => ServiceRefundStatus::COMPLETED,
                                'processed_by' => null, // Tự động hoàn tiền, không có người xử lý
                            ]);
                            
                            WalletService::refundForServiceOrder($order);
                        });
                        
                        $totalSuccess++;
                        $this->line("  ✓ ServiceOrder #{$order->slug} - Đã hoàn tiền cho buyer");
                        
                    } catch (\Exception $e) {
                        $totalFailed++;
                        $this->error("  ✗ ServiceOrder #{$order->slug} - Lỗi: {$e->getMessage()}");
                        
                        Log::error("Hoàn tiền đơn hàng dịch vụ #{$order->slug} thất bại: {$e->getMessage()}", [
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
}
