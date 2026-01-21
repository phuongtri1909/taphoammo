<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Traits\HasSlug;
use App\Enums\CommonStatus;

class ServiceVariant extends Model
{
    use HasFactory, HasSlug;

    protected $fillable = [
        'service_id',
        'name',
        'slug',
        'price',
        'sold_count',
        'order',
        'status',
    ];

    protected $casts = [
        'service_id' => 'integer',
        'price' => 'decimal:2',
        'sold_count' => 'integer',
        'order' => 'integer',
        'status' => CommonStatus::class,
    ];

    /**
     * Slug configuration
     */
    protected string $slugSource = 'name';
    protected string $slugPrefix = 'service-variant';
    protected int $slugMaxLength = 50;
    protected int $randomStringLength = 8;
    protected bool $regenerateSlugOnUpdate = false;

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }

    public function serviceOrders(): HasMany
    {
        return $this->hasMany(ServiceOrder::class);
    }

    public function scopeActive($query)
    {
        return $query->where('status', CommonStatus::ACTIVE);
    }

    public function scopeInactive($query)
    {
        return $query->where('status', CommonStatus::INACTIVE);
    }

    public function incrementSold(int $quantity = 1): void
    {
        $this->increment('sold_count', $quantity);
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
     */
    public function isPurchasable(): bool
    {
        return $this->status->canBePurchased();
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
        return $query->where('status', CommonStatus::ACTIVE);
    }
}
