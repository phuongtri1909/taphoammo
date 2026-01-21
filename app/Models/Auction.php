<?php

namespace App\Models;

use App\Traits\HasSlug;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Auction extends Model
{
    use HasFactory, HasSlug;

    protected $fillable = [
        'slug',
        'title',
        'description',
        'start_time',
        'end_time',
        'starting_price',
        'banner_duration_days',
        'banner_position',
        'status',
        'created_by',
        'winner_id',
        'winning_price',
        'ended_at',
    ];

    protected $casts = [
        'start_time' => 'datetime',
        'end_time' => 'datetime',
        'starting_price' => 'decimal:2',
        'banner_duration_days' => 'integer',
        'winning_price' => 'decimal:2',
        'ended_at' => 'datetime',
    ];

    protected string $slugSource = 'title';
    protected string $slugPrefix = 'auction';
    protected int $slugMaxLength = 50;
    protected int $randomStringLength = 8;
    protected bool $regenerateSlugOnUpdate = false;

    /**
     * Get the admin who created this auction
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the winner (seller)
     */
    public function winner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'winner_id');
    }

    /**
     * Get all bids for this auction
     */
    public function bids(): HasMany
    {
        return $this->hasMany(AuctionBid::class);
    }

    /**
     * Get active bids (highest first)
     */
    public function activeBids(): HasMany
    {
        return $this->bids()->where('status', 'active')->orderBy('bid_amount', 'desc');
    }

    /**
     * Get top bid
     */
    public function topBid()
    {
        return $this->activeBids()->first();
    }

    /**
     * Get banners for this auction
     */
    public function banners(): HasMany
    {
        return $this->hasMany(AuctionBanner::class);
    }

    /**
     * Check if auction is currently active
     */
    public function isActive(): bool
    {
        return $this->status === 'active' 
            && now()->gte($this->start_time) 
            && now()->lte($this->end_time);
    }

    /**
     * Check if auction has ended
     */
    public function hasEnded(): bool
    {
        return $this->status === 'ended' || now()->gt($this->end_time);
    }

    /**
     * Check if auction is pending
     */
    public function isPending(): bool
    {
        return $this->status === 'pending' && now()->lt($this->start_time);
    }

    /**
     * Get current highest bid amount
     */
    public function getCurrentHighestBidAttribute(): float
    {
        $topBid = $this->topBid();
        return $topBid ? (float) $topBid->bid_amount : (float) $this->starting_price;
    }
}
