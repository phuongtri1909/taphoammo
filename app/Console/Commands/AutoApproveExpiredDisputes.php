<?php

namespace App\Console\Commands;

use App\Models\Config;
use App\Models\Dispute;
use App\Enums\DisputeStatus;
use App\Services\DisputeService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AutoApproveExpiredDisputes extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'disputes:auto-approve {--chunk=50 : Số lượng dispute xử lý mỗi lần}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Tự động chấp nhận các khiếu nại mà seller không phản hồi trong thời gian quy định';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $chunkSize = (int) $this->option('chunk');
        $responseHours = (int) Config::getConfig('dispute_response_hours', 48);
        $deadline = now()->subHours($responseHours);

        $this->info("Đang tìm khiếu nại OPEN quá {$responseHours} giờ (trước {$deadline})...");

        $expiredDisputes = Dispute::where('status', DisputeStatus::OPEN)
            ->where('created_at', '<', $deadline)
            ->orderBy('created_at', 'asc')
            ->get();

        $total = $expiredDisputes->count();
        
        if ($total === 0) {
            $this->info('Không có khiếu nại nào cần xử lý.');
            return 0;
        }

        $this->info("Tìm thấy {$total} khiếu nại cần xử lý.");

        $successCount = 0;
        $failCount = 0;

        foreach ($expiredDisputes->chunk($chunkSize) as $chunk) {
            foreach ($chunk as $dispute) {
                try {
                    DB::transaction(function () use ($dispute) {
                        DisputeService::approveDispute(
                            $dispute, 
                            0,
                            'Tự động chấp nhận do seller không phản hồi trong thời gian quy định.'
                        );
                    });

                    $this->info("  ✓ Dispute #{$dispute->slug} - Tự động chấp nhận");
                    Log::info("AutoApproveExpiredDisputes: Dispute #{$dispute->slug} approved automatically");
                    $successCount++;
                } catch (\Exception $e) {
                    $this->error("  ✗ Dispute #{$dispute->slug} - Lỗi: {$e->getMessage()}");
                    Log::error("AutoApproveExpiredDisputes: Dispute #{$dispute->slug} failed - {$e->getMessage()}");
                    $failCount++;
                }
            }

            if ($chunk->count() === $chunkSize) {
                sleep(1);
            }
        }

        $this->newLine();
        $this->info('=== KẾT QUẢ ===');
        $this->info("Tổng số khiếu nại đã xử lý: {$total}");
        $this->info("Thành công: {$successCount}");
        if ($failCount > 0) {
            $this->error("Thất bại: {$failCount}");
        }

        return 0;
    }
}

