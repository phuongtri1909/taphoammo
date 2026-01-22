<?php

namespace App\Models;

use App\Traits\HasSlug;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class AuctionBanner extends Model
{
    use HasFactory, HasSlug;

    protected $fillable = [
        'slug',
        'auction_id',
        'auction_bid_id',
        'bannerable_type',
        'bannerable_id',
        'position',
        'display_from',
        'display_until',
        'display_order',
        'is_active',
    ];

    protected $casts = [
        'display_from' => 'datetime',
        'display_until' => 'datetime',
        'display_order' => 'integer',
        'is_active' => 'boolean',
    ];

    protected string $slugPrefix = 'banner';
    protected int $slugMaxLength = 30;
    protected int $randomStringLength = 12;
    protected bool $regenerateSlugOnUpdate = false;
    protected bool $alwaysUseRandomStringInSlug = true;

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
     * Get the winning bid
     */
    public function bid(): BelongsTo
    {
        return $this->belongsTo(AuctionBid::class, 'auction_bid_id');
    }

    /**
     * Get the bannerable model (Product or Service)
     */
    public function bannerable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Scope: Active banners
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true)
            ->where('display_from', '<=', now())
            ->where('display_until', '>=', now());
    }

    /**
     * Scope: By position
     */
    public function scopeByPosition($query, string $position)
    {
        return $query->where('position', $position);
    }

    /**
     * Check if banner is currently visible
     */
    public function isVisible(): bool
    {
        return $this->is_active 
            && now()->gte($this->display_from) 
            && now()->lte($this->display_until);
    }
}
