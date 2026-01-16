<?php

namespace App\Models;

use App\Enums\ProductStatus;
use App\Traits\HasSlug;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
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
            throw new \DomainException('Chuyển đổi trạng thái sản phẩm không hợp lệ');
        }
    
        $this->update(['status' => $to]);
    }

    /**
     * Sản phẩm có hiển thị cho client không?
     */
    public function isVisibleToClient(): bool
    {
        return $this->status->isVisibleToClient();
    }

    /**
     * Sản phẩm có thể mua được không?
     * Check: status APPROVED + có ít nhất 1 variant ACTIVE với stock > 0
     */
    public function isPurchasable(): bool
    {
        if (!$this->status->canBePurchased()) {
            return false;
        }

        return $this->variants()
            ->where('status', \App\Enums\CommonStatus::ACTIVE)
            ->where('stock_quantity', '>', 0)
            ->exists();
    }

    /**
     * Scope: Chỉ lấy sản phẩm hiển thị cho client
     */
    public function scopeVisibleToClient($query)
    {
        return $query->where('status', ProductStatus::APPROVED);
    }
}