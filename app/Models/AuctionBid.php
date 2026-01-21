<?php

namespace App\Models;

use App\Traits\HasSlug;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class AuctionBid extends Model
{
    use HasFactory, HasSlug;

    protected $fillable = [
        'slug',
        'auction_id',
        'seller_id',
        'biddable_type',
        'biddable_id',
        'bid_amount',
        'rank_at_bid',
        'status',
    ];

    protected $casts = [
        'bid_amount' => 'decimal:2',
        'rank_at_bid' => 'integer',
    ];

    protected string $slugPrefix = 'bid';
    protected int $slugMaxLength = 30;
    protected int $randomStringLength = 12;
    protected bool $regenerateSlugOnUpdate = false;

    protected function getSlugSourceValue(): string
    {
        return '';
    }

    /**
     * Get the auction
     */
    public function auction(): BelongsTo
    {
        return $this->belongsTo(Auction::class);
    }

    /**
     * Get the seller who made this bid
     */
    public function seller(): BelongsTo
    {
        return $this->belongsTo(User::class, 'seller_id');
    }

    /**
     * Get the biddable model (Product or Service)
     */
    public function biddable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Get the banner created from this bid (if won)
     */
    public function banner(): HasOne
    {
        return $this->hasOne(AuctionBanner::class, 'auction_bid_id');
    }

    /**
     * Check if this bid is the top bid
     */
    public function isTopBid(): bool
    {
        if ($this->status !== 'active') {
            return false;
        }

        $topBid = $this->auction->topBid();
        return $topBid && $topBid->id === $this->id;
    }

    /**
     * Mark bid as outbid
     */
    public function markAsOutbid(): void
    {
        $this->update(['status' => 'outbid']);
    }

    /**
     * Mark bid as won
     */
    public function markAsWon(): void
    {
        $this->update(['status' => 'won']);
    }
}
