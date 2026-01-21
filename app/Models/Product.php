<?php

namespace App\Models;

use App\Enums\ProductStatus;
use App\Traits\HasSlug;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use HasFactory, SoftDeletes, HasSlug;

    protected $fillable = [
        'sub_category_id',
        'seller_id',
        'name',
        'slug',
        'image',
        'description',
        'long_description',
        'admin_note',
        'status',
        'featured_until',
    ];

    protected $casts = [
        'sub_category_id' => 'integer',
        'seller_id' => 'integer',
        'status' => ProductStatus::class,
        'featured_until' => 'datetime',
    ];

    /**
     * Slug configuration
     */
    protected string $slugSource = 'name';
    protected string $slugPrefix = 'product';
    protected int $slugMaxLength = 50;
    protected int $randomStringLength = 8;
    protected bool $regenerateSlugOnUpdate = false;

    public function subCategory(): BelongsTo
    {
        return $this->belongsTo(SubCategory::class);
    }

    public function seller(): BelongsTo
    {
        return $this->belongsTo(User::class, 'seller_id');
    }

    public function variants(): HasMany
    {
        return $this->hasMany(ProductVariant::class);
    }

    public function scopeApproved($query)
    {
        return $query->where('status', ProductStatus::APPROVED);
    }

    public function scopePending($query)
    {
        return $query->where('status', ProductStatus::PENDING);
    }

    public function scopeHidden($query)
    {
        return $query->where('status', ProductStatus::HIDDEN);
    }

    public function scopeBanned($query)
    {
        return $query->where('status', ProductStatus::BANNED);
    }

    public function scopeRejected($query)
    {
        return $query->where('status', ProductStatus::REJECTED);
    }

    public function scopeFeatured($query)
    {
        return $query->whereNotNull('featured_until')
            ->where('featured_until', '>', now());
    }

    public function isFeatured(): bool
    {
        return $this->featured_until !== null && $this->featured_until->isFuture();
    }

    public function changeStatus(ProductStatus $to): void
    {
        if (! $this->status->canTransitionTo($to)) {
            throw new \DomainException('Bạn không thể chuyển thực hiện hành động này');
        }
    
        $this->update(['status' => $to]);
    }

    /**
     * Sản phẩm có hiển thị cho client không?
     * Check: status visible + seller không bị ban
     */
    public function isVisibleToClient(): bool
    {
        if (!$this->status->isVisibleToClient()) {
            return false;
        }

        if ($this->seller && $this->seller->isSellerBanned()) {
            return false;
        }

        return true;
    }

    /**
     * Sản phẩm có thể mua được không?
     * Check: status APPROVED + seller không bị ban + có ít nhất 1 variant ACTIVE với stock > 0
     */
    public function isPurchasable(): bool
    {
        if (!$this->status->canBePurchased()) {
            return false;
        }

        if ($this->seller && $this->seller->isSellerBanned()) {
            return false;
        }

        return $this->variants()
            ->where('status', \App\Enums\CommonStatus::ACTIVE)
            ->where('stock_quantity', '>', 0)
            ->exists();
    }

    /**
     * Scope: Chỉ lấy sản phẩm hiển thị cho client
     * - Status APPROVED
     * - Seller không bị ban
     */
    public function scopeVisibleToClient($query)
    {
        return $query->where('status', ProductStatus::APPROVED)
            ->whereHas('seller', function ($q) {
                $q->where('is_seller_banned', false);
            });
    }

    /**
     * Scope: Chỉ lấy sản phẩm có thể mua
     */
    public function scopePurchasable($query)
    {
        return $query->visibleToClient()
            ->whereHas('variants', function ($q) {
                $q->where('status', \App\Enums\CommonStatus::ACTIVE)
                  ->where('stock_quantity', '>', 0);
            });
    }

    /**
     * Favorites relationship
     */
    public function favorites(): MorphMany
    {
        return $this->morphMany(Favorite::class, 'favoritable');
    }

    /**
     * Reviews relationship
     */
    public function reviews(): MorphMany
    {
        return $this->morphMany(Review::class, 'reviewable');
    }

    /**
     * Get visible reviews
     */
    public function visibleReviews(): MorphMany
    {
        return $this->reviews()->where('is_visible', true);
    }

    /**
     * Get average rating
     */
    public function getAverageRatingAttribute(): float
    {
        return round($this->visibleReviews()->avg('rating') ?? 0, 1);
    }

    /**
     * Get reviews count
     */
    public function getReviewsCountAttribute(): int
    {
        return $this->visibleReviews()->count();
    }

    /**
     * Check if product has any orders
     */
    public function hasOrders(): bool
    {
        return \Illuminate\Support\Facades\DB::table('order_items')
            ->join('product_variants', 'order_items.product_variant_id', '=', 'product_variants.id')
            ->where('product_variants.product_id', $this->id)
            ->exists();
    }
}