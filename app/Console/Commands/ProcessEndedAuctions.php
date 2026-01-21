<?php

namespace App\Console\Commands;

use App\Models\Auction;
use App\Models\AuctionBid;
use App\Models\AuctionBanner;
use App\Services\WalletService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ProcessEndedAuctions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'auctions:process-ended';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Xử lý các phiên đấu giá đã kết thúc: trừ tiền, tạo banner';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Bắt đầu xử lý các phiên đấu giá đã kết thúc...');

        // Get ended auctions that haven't been processed
        $endedAuctions = Auction::where('status', 'active')
            ->where('end_time', '<=', now())
            ->get();

        if ($endedAuctions->isEmpty()) {
            $this->info('Không có phiên đấu giá nào cần xử lý.');
            return 0;
        }

        $processed = 0;
        $failed = 0;

        foreach ($endedAuctions as $auction) {
            try {
                DB::beginTransaction();

                // Get top bids ordered by amount (desc)
                $topBids = AuctionBid::where('auction_id', $auction->id)
                    ->where('status', 'active')
                    ->orderBy('bid_amount', 'desc')
                    ->get();

                if ($topBids->isEmpty()) {
                    // No bids, just mark as ended
                    $auction->update([
                        'status' => 'ended',
                        'ended_at' => now(),
                    ]);
                    $this->info("Phiên đấu giá #{$auction->slug} không có bid nào.");
                    DB::commit();
                    $processed++;
                    continue;
                }

                // Find winner with sufficient balance
                $winner = null;
                foreach ($topBids as $bid) {
                    try {
                        // Try to deduct money
                        WalletService::deductForAuctionBid($bid);

                        // Success! This is the winner
                        $winner = $bid;
                        break;
                    } catch (\Exception $e) {
                        // Not enough balance, try next bid
                        $this->warn("Bid #{$bid->slug} không đủ tiền: {$e->getMessage()}");
                        continue;
                    }
                }

                if (!$winner) {
                    // No one has enough balance
                    $auction->update([
                        'status' => 'ended',
                        'ended_at' => now(),
                    ]);
                    $this->warn("Phiên đấu giá #{$auction->slug} không có người thắng đủ tiền.");
                    DB::commit();
                    $processed++;
                    continue;
                }

                // Mark winner
                $winner->markAsWon();

                // Mark all other bids as invalid
                AuctionBid::where('auction_id', $auction->id)
                    ->where('id', '!=', $winner->id)
                    ->update(['status' => 'invalid']);

                // Update auction
                $auction->update([
                    'status' => 'ended',
                    'winner_id' => $winner->seller_id,
                    'winning_price' => $winner->bid_amount,
                    'ended_at' => now(),
                ]);

                // Create banner
                $displayFrom = now();
                $displayUntil = now()->addDays($auction->banner_duration_days);

                // Get current max display_order for this position
                $maxOrder = AuctionBanner::where('position', $auction->banner_position)
                    ->max('display_order') ?? 0;

                AuctionBanner::create([
                    'auction_id' => $auction->id,
                    'auction_bid_id' => $winner->id,
                    'bannerable_type' => $winner->biddable_type,
                    'bannerable_id' => $winner->biddable_id,
                    'position' => $auction->banner_position,
                    'display_from' => $displayFrom,
                    'display_until' => $displayUntil,
                    'display_order' => $maxOrder + 1,
                    'is_active' => true,
                ]);

                $this->info("Phiên đấu giá #{$auction->slug} đã được xử lý. Người thắng: {$winner->seller->full_name}, Giá: " . number_format($winner->bid_amount, 0, ',', '.') . "₫");

                DB::commit();
                $processed++;

            } catch (\Exception $e) {
                DB::rollBack();
                $failed++;
                $this->error("Lỗi xử lý phiên đấu giá #{$auction->slug}: {$e->getMessage()}");
                Log::error("Lỗi xử lý phiên đấu giá", [
                    'auction_id' => $auction->id,
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString(),
                ]);
            }
        }

        $this->info("Hoàn thành! Đã xử lý: {$processed}, Lỗi: {$failed}");

        return 0;
    }
}
