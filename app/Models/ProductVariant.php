<?php

namespace App\Models;

use App\Enums\CommonStatus;
use App\Traits\HasSlug;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProductVariant extends Model
{
    use HasFactory, HasSlug;

    protected $fillable = [
        'product_id',
        'name',
        'slug',
        'price',
        'stock_quantity',
        'sold_count',
        'field_name',
        'order',
        'status',
    ];

    protected $casts = [
        'product_id' => 'integer',
        'price' => 'decimal:2',
        'stock_quantity' => 'integer',
        'sold_count' => 'integer',
        'order' => 'integer',
        'status' => CommonStatus::class,
    ];

    /**
     * Slug configuration
     */
    protected string $slugSource = 'name';
    protected string $slugPrefix = 'variant';
    protected int $slugMaxLength = 50;
    protected int $randomStringLength = 8;
    protected bool $regenerateSlugOnUpdate = false;

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function productValues(): HasMany
    {
        return $this->hasMany(ProductValue::class);
    }

    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function scopeActive($query)
    {
        return $query->where('status', CommonStatus::ACTIVE);
    }

    public function scopeInactive($query)
    {
        return $query->where('status', CommonStatus::INACTIVE);
    }

    public function scopeInStock($query)
    {
        return $query->where('stock_quantity', '>', 0);
    }

    public function incrementSold(int $quantity = 1): void
    {
        $this->increment('sold_count', $quantity);
        $this->decrement('stock_quantity', $quantity);
    }

    /**
     * Biến thể có hiển thị cho client không?
     */
    public function isVisibleToClient(): bool
    {
        return $this->status->isVisibleToClient();
    }

    /**
     * Biến thể có thể mua được không?
     * Check: status ACTIVE + stock > 0
     */
    public function isPurchasable(): bool
    {
        return $this->status->canBePurchased() && $this->stock_quantity > 0;
    }

    /**
     * Scope: Chỉ lấy biến thể hiển thị cho client
     */
    public function scopeVisibleToClient($query)
    {
        return $query->where('status', CommonStatus::ACTIVE);
    }

    /**
     * Scope: Chỉ lấy biến thể có thể mua
     */
    public function scopePurchasable($query)
    {
        return $query->where('status', CommonStatus::ACTIVE)
            ->where('stock_quantity', '>', 0);
    }
}
