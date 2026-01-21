<?php

namespace App\Models;

use App\Traits\HasSlug;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class FeaturedHistory extends Model
{
    use HasFactory, HasSlug;

    protected $fillable = [
        'slug',
        'seller_id',
        'featurable_type',
        'featurable_id',
        'amount',
        'hours',
        'featured_from',
        'featured_until',
        'note',
    ];

    protected $casts = [
        'seller_id' => 'integer',
        'featurable_id' => 'integer',
        'amount' => 'decimal:0',
        'hours' => 'integer',
        'featured_from' => 'datetime',
        'featured_until' => 'datetime',
    ];

    protected string $slugSource = 'id';
    protected string $slugPrefix = 'featured';
    protected int $slugMaxLength = 50;
    protected int $randomStringLength = 10;
    protected bool $regenerateSlugOnUpdate = false;

    public function seller(): BelongsTo
    {
        return $this->belongsTo(User::class, 'seller_id');
    }

    public function featurable(): MorphTo
    {
        return $this->morphTo();
    }

    public function getFormattedAmountAttribute(): string
    {
        return number_format($this->amount, 0, ',', '.') . ' VNĐ';
    }

    public function getFeaturableTypeLabelAttribute(): string
    {
        return match ($this->featurable_type) {
            Product::class, 'App\\Models\\Product' => 'Sản phẩm',
            Service::class, 'App\\Models\\Service' => 'Dịch vụ',
            default => 'Không xác định',
        };
    }

    public function getFeaturableTypeBadgeAttribute(): string
    {
        return match ($this->featurable_type) {
            Product::class, 'App\\Models\\Product' => 'primary',
            Service::class, 'App\\Models\\Service' => 'info',
            default => 'secondary',
        };
    }

    public function isActive(): bool
    {
        return $this->featured_until->isFuture();
    }

    public function scopeBySeller($query, $sellerId)
    {
        return $query->where('seller_id', $sellerId);
    }

    public function scopeActive($query)
    {
        return $query->where('featured_until', '>', now());
    }

    public function scopeByType($query, string $type)
    {
        $modelClass = match ($type) {
            'product' => Product::class,
            'service' => Service::class,
            default => null,
        };

        if ($modelClass) {
            return $query->where('featurable_type', $modelClass);
        }

        return $query;
    }
}
