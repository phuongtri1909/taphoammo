<?php

namespace App\Services;

use App\Models\Config;
use App\Models\FeaturedHistory;
use App\Models\Product;
use App\Models\Service;
use App\Models\User;
use App\Models\WalletTransaction;
use App\Enums\WalletTransactionType;
use App\Enums\WalletTransactionReferenceType;
use App\Enums\WalletTransactionStatus;
use Illuminate\Support\Facades\DB;

class FeaturedService
{
    /**
     * Get featured price from config
     */
    public function getFeaturedPrice(): int
    {
        return (int) Config::getConfig('featured_price', 10000);
    }

    /**
     * Get featured hours from config
     */
    public function getFeaturedHours(): int
    {
        return (int) Config::getConfig('featured_hours', 24);
    }

    /**
     * Feature a product
     */
    public function featureProduct(User $seller, Product $product, int $times = 1, ?string $note = null): FeaturedHistory
    {
        return $this->feature($seller, $product, $times, $note);
    }

    /**
     * Feature a service
     */
    public function featureService(User $seller, Service $service, int $times = 1, ?string $note = null): FeaturedHistory
    {
        return $this->feature($seller, $service, $times, $note);
    }

    /**
     * Core feature logic
     */
    protected function feature(User $seller, $featurable, int $times = 1, ?string $note = null): FeaturedHistory
    {
        $price = $this->getFeaturedPrice();
        $hours = $this->getFeaturedHours();
        
        $totalAmount = $price * $times;
        $totalHours = $hours * $times;

        $wallet = $seller->wallet;
        if (!$wallet || $wallet->balance < $totalAmount) {
            throw new \DomainException('Số dư ví không đủ để thực hiện đề xuất. Vui lòng nạp thêm tiền.');
        }

        if ($featurable->seller_id !== $seller->id) {
            throw new \DomainException('Bạn không có quyền đề xuất sản phẩm/dịch vụ này.');
        }

        return DB::transaction(function () use ($seller, $featurable, $totalAmount, $totalHours, $note, $wallet) {
            $now = now();
            $currentFeaturedUntil = $featurable->featured_until;
            
            if ($currentFeaturedUntil && $currentFeaturedUntil->isFuture()) {
                $featuredFrom = $currentFeaturedUntil;
            } else {
                $featuredFrom = $now;
            }
            
            $featuredUntil = $featuredFrom->copy()->addHours($totalHours);

            $featuredHistory = FeaturedHistory::create([
                'seller_id' => $seller->id,
                'featurable_type' => get_class($featurable),
                'featurable_id' => $featurable->id,
                'amount' => $totalAmount,
                'hours' => $totalHours,
                'featured_from' => $featuredFrom,
                'featured_until' => $featuredUntil,
                'note' => $note,
            ]);

            $featurable->update([
                'featured_until' => $featuredUntil,
            ]);

            $newBalance = $wallet->balance - $totalAmount;
            $wallet->update(['balance' => $newBalance]);

            $featurableName = $featurable->name ?? 'Không xác định';
            $featurableType = $featurable instanceof Product ? 'sản phẩm' : 'dịch vụ';
            
            WalletTransaction::create([
                'wallet_id' => $wallet->id,
                'type' => WalletTransactionType::FEATURED_PURCHASE,
                'amount' => -$totalAmount,
                'balance_before' => $wallet->balance + $totalAmount,
                'balance_after' => $newBalance,
                'reference_type' => WalletTransactionReferenceType::FEATURED_HISTORY,
                'reference_id' => $featuredHistory->id,
                'description' => "Đề xuất {$featurableType}: {$featurableName} ({$totalHours} giờ)",
                'status' => WalletTransactionStatus::COMPLETED,
            ]);

            return $featuredHistory;
        });
    }

    public function getSellerProducts(User $seller)
    {
        return Product::where('seller_id', $seller->id)
            ->approved()
            ->orderBy('name')
            ->get();
    }

    public function getSellerServices(User $seller)
    {
        return Service::where('seller_id', $seller->id)
            ->approved()
            ->orderBy('name')
            ->get();
    }

    public function getSellerFeaturedHistory(User $seller, ?string $type = null, int $perPage = 15)
    {
        $query = FeaturedHistory::with(['featurable'])
            ->bySeller($seller->id)
            ->orderByDesc('created_at');

        if ($type) {
            $query->byType($type);
        }

        return $query->paginate($perPage);
    }

    public function getAllFeaturedHistory(?string $type = null, ?int $sellerId = null, int $perPage = 20)
    {
        $query = FeaturedHistory::with(['seller', 'featurable'])
            ->orderByDesc('created_at');

        if ($type) {
            $query->byType($type);
        }

        if ($sellerId) {
            $query->bySeller($sellerId);
        }

        return $query->paginate($perPage);
    }
}
