<?php

namespace App\Console\Commands;

use App\Models\Order;
use App\Models\Config;
use App\Enums\OrderStatus;
use App\Enums\DisputeStatus;
use App\Services\WalletService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CompleteExpiredOrders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'orders:complete-expired {--chunk=50 : Số orders xử lý mỗi lần}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Tự động hoàn thành các đơn hàng đã hết thời gian khiếu nại và cộng tiền cho seller';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $chunkSize = (int) $this->option('chunk');
        $refundHours = (int) Config::getConfig('refund_hours', 24);
        $deadline = now()->subHours($refundHours);
        
        $this->info("Đang tìm đơn hàng PAID đã quá {$refundHours} giờ (trước {$deadline->format('Y-m-d H:i:s')})...");
        
        $totalProcessed = 0;
        $totalSuccess = 0;
        $totalFailed = 0;

        Order::where('status', OrderStatus::PAID)
            ->where('created_at', '<=', $deadline)
            ->whereDoesntHave('disputes', function ($query) {
                $query->whereIn('status', [DisputeStatus::OPEN, DisputeStatus::REVIEWING]);
            })
            ->orderBy('created_at', 'asc')
            ->chunk($chunkSize, function ($orders) use (&$totalProcessed, &$totalSuccess, &$totalFailed) {
                foreach ($orders as $order) {
                    $totalProcessed++;
                    
                    try {
                        DB::transaction(function () use ($order) {
                            $order->update(['status' => OrderStatus::COMPLETED]);
                            
                            WalletService::paySellerForOrder($order);
                        });
                        
                        $totalSuccess++;
                        $this->line("  ✓ Order #{$order->slug} - Hoàn thành");
                        
                    } catch (\Exception $e) {
                        $totalFailed++;
                        $this->error("  ✗ Order #{$order->slug} - Lỗi: {$e->getMessage()}");
                        
                        Log::error("Hoàn thành đơn hàng #{$order->slug} thất bại: {$e->getMessage()}", [
                            'order_id' => $order->id,
                            'order_slug' => $order->slug,
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

