<?php

namespace App\Models;

use App\Traits\HasSlug;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Review extends Model
{
    use HasFactory, HasSlug;

    protected $fillable = [
        'slug',
        'reviewable_type',
        'reviewable_id',
        'user_id',
        'order_type',
        'order_id',
        'rating',
        'content',
        'is_visible',
    ];

    protected $casts = [
        'rating' => 'integer',
        'is_visible' => 'boolean',
    ];

    protected string $slugPrefix = 'review';
    protected int $slugMaxLength = 30;
    protected int $randomStringLength = 12;
    protected bool $regenerateSlugOnUpdate = false;
    protected bool $alwaysUseRandomStringInSlug = true;

    protected function getSlugSourceValue(): string
    {
        return '';
    }

    /**
     * Get the reviewable model (Product or Service)
     */
    public function reviewable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Get the user who made the review
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the order (Product Order)
     */
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class, 'order_id');
    }

    /**
     * Get the service order
     */
    public function serviceOrder(): BelongsTo
    {
        return $this->belongsTo(ServiceOrder::class, 'order_id');
    }

    /**
     * Get the related order based on order_type
     */
    public function getRelatedOrderAttribute()
    {
        if ($this->order_type === 'product') {
            return $this->order;
        }
        return $this->serviceOrder;
    }

    /**
     * Scope visible reviews
     */
    public function scopeVisible($query)
    {
        return $query->where('is_visible', true);
    }

    /**
     * Scope for product reviews
     */
    public function scopeForProduct($query, $productId)
    {
        return $query->where('reviewable_type', Product::class)
            ->where('reviewable_id', $productId);
    }

    /**
     * Scope for service reviews
     */
    public function scopeForService($query, $serviceId)
    {
        return $query->where('reviewable_type', Service::class)
            ->where('reviewable_id', $serviceId);
    }

    /**
     * Check if user can review this order
     */
    public static function canReview(string $orderType, int $orderId, int $userId, int $reviewableId): bool
    {
        return !self::where('order_type', $orderType)
            ->where('order_id', $orderId)
            ->where('user_id', $userId)
            ->where('reviewable_id', $reviewableId)
            ->exists();
    }
}
