<?php

namespace App\Models;

use App\Enums\OrderStatus;
use App\Traits\HasSlug;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Order extends Model
{
    use HasFactory, HasSlug;

    protected $fillable = [
        'buyer_id',
        'seller_id',
        'slug',
        'total_amount',
        'status',
    ];

    protected $casts = [
        'buyer_id' => 'integer',
        'seller_id' => 'integer',
        'total_amount' => 'decimal:2',
        'status' => OrderStatus::class,
    ];

    protected string $slugPrefix = 'order';
    protected int $slugMaxLength = 20;
    protected int $randomStringLength = 12;
    protected bool $regenerateSlugOnUpdate = false;

    protected function getSlugSourceValue(): string
    {
        return '';
    }

    public function buyer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'buyer_id');
    }

    public function seller(): BelongsTo
    {
        return $this->belongsTo(User::class, 'seller_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function productValues(): HasMany
    {
        return $this->hasMany(ProductValue::class);
    }

    public function disputes(): HasMany
    {
        return $this->hasMany(Dispute::class);
    }

    public function refunds(): HasMany
    {
        return $this->hasMany(Refund::class);
    }

    public function scopePending($query)
    {
        return $query->where('status', OrderStatus::PENDING);
    }

    public function scopePaid($query)
    {
        return $query->where('status', OrderStatus::PAID);
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', OrderStatus::COMPLETED);
    }

    public function scopeRefunded($query)
    {
        return $query->where('status', OrderStatus::REFUNDED);
    }

    public function scopePartialRefunded($query)
    {
        return $query->where('status', OrderStatus::PARTIAL_REFUNDED);
    }

    public function scopeDisputed($query)
    {
        return $query->where('status', OrderStatus::DISPUTED);
    }

    

    public function changeStatus(OrderStatus $to): void
    {
        if (! $this->status->canTransitionTo($to)) {
            throw new \DomainException('Chuyển đổi trạng thái đơn hàng không hợp lệ');
        }
    
        $this->update(['status' => $to]);
    }
}
