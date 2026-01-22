<?php

namespace App\Models;

use App\Traits\HasSlug;
use App\Enums\ServiceStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Service extends Model
{
    use HasFactory, SoftDeletes, HasSlug;

    protected $fillable = [
        'service_sub_category_id',
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
        'service_sub_category_id' => 'integer',
        'seller_id' => 'integer',
        'status' => ServiceStatus::class,
        'featured_until' => 'datetime',
    ];

    /**
     * Slug configuration
     */
    protected string $slugSource = 'name';
    protected string $slugPrefix = 'service';
    protected int $slugMaxLength = 50;
    protected int $randomStringLength = 8;
    protected bool $regenerateSlugOnUpdate = false;
    protected bool $useRandomStringInSlug = false;

    public function serviceSubCategory(): BelongsTo
    {
        return $this->belongsTo(ServiceSubCategory::class);
    }

    public function seller(): BelongsTo
    {
        return $this->belongsTo(User::class, 'seller_id');
    }

    public function variants(): HasMany
    {
        return $this->hasMany(ServiceVariant::class);
    }

    public function scopeApproved($query)
    {
        return $query->where('status', ServiceStatus::APPROVED);
    }

    public function scopePending($query)
    {
        return $query->where('status', ServiceStatus::PENDING);
    }

    public function scopeHidden($query)
    {
        return $query->where('status', ServiceStatus::HIDDEN);
    }

    public function scopeBanned($query)
    {
        return $query->where('status', ServiceStatus::BANNED);
    }

    public function scopeRejected($query)
    {
        return $query->where('status', ServiceStatus::REJECTED);
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

    public function changeStatus(ServiceStatus $to): void
    {
        if (! $this->status->canTransitionTo($to)) {
            throw new \DomainException('Bạn không thể chuyển thực hiện hành động này');
        }
    
        $this->update(['status' => $to]);
    }

    /**
     * Dịch vụ có hiển thị cho client không?
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
     * Dịch vụ có thể mua được không?
     * Check: status APPROVED + seller không bị ban + có ít nhất 1 variant ACTIVE
     */
    public function isPurchasable(): bool
    {
        if (!$this->status->canBePurchased()) {
            return false;
        }

        if ($this->seller && $this->seller->isSellerBanned()) {
            return false;
        }

        return true;
    }

    /**
     * Scope: Chỉ lấy dịch vụ hiển thị cho client
     * - Status APPROVED
     * - Seller không bị ban
     */
    public function scopeVisibleToClient($query)
    {
        return $query->where('status', ServiceStatus::APPROVED)
            ->whereHas('seller', function ($q) {
                $q->where('is_seller_banned', false);
            });
    }

    /**
     * Scope: Chỉ lấy dịch vụ có thể mua
     */
    public function scopePurchasable($query)
    {
        return $query->visibleToClient();
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
     * Check if service has any orders
     */
    public function hasOrders(): bool
    {
        return \Illuminate\Support\Facades\DB::table('service_orders')
            ->join('service_variants', 'service_orders.service_variant_id', '=', 'service_variants.id')
            ->where('service_variants.service_id', $this->id)
            ->exists();
    }
}
