<?php

namespace App\Models;

use App\Enums\DisputeStatus;
use App\Traits\HasSlug;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Dispute extends Model
{
    use HasFactory, HasSlug;

    protected $fillable = [
        'order_id',
        'order_item_id',
        'product_value_id',
        'buyer_id',
        'seller_id',
        'slug',
        'reason',
        'evidence',
        'status',
        'resolved_at',
        'resolved_by',
    ];

    protected $casts = [
        'order_id' => 'integer',
        'order_item_id' => 'integer',
        'product_value_id' => 'integer',
        'buyer_id' => 'integer',
        'seller_id' => 'integer',
        'evidence' => 'array',
        'resolved_at' => 'datetime',
        'resolved_by' => 'integer',
        'status' => DisputeStatus::class,
    ];

    protected string $slugPrefix = 'dispute';
    protected int $slugMaxLength = 20;
    protected int $randomStringLength = 12;
    protected bool $regenerateSlugOnUpdate = false;

    protected function getSlugSourceValue(): string
    {
        return '';
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function orderItem(): BelongsTo
    {
        return $this->belongsTo(OrderItem::class);
    }

    public function productValue(): BelongsTo
    {
        return $this->belongsTo(ProductValue::class);
    }

    public function buyer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'buyer_id');
    }

    public function seller(): BelongsTo
    {
        return $this->belongsTo(User::class, 'seller_id');
    }

    public function resolvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'resolved_by');
    }

    public function scopeOpen($query)
    {
        return $query->where('status', DisputeStatus::OPEN);
    }

    public function scopeReviewing($query)
    {
        return $query->where('status', DisputeStatus::REVIEWING);
    }

    public function scopeApproved($query)
    {
        return $query->where('status', DisputeStatus::APPROVED);
    }

    public function scopeRejected($query)
    {
        return $query->where('status', DisputeStatus::REJECTED);
    }

    public function changeStatus(DisputeStatus $to): void
    {
        if (! $this->status->canTransitionTo($to)) {
            throw new \DomainException('Chuyển đổi trạng thái tranh chấp không hợp lệ');
        }
    
        $this->update(['status' => $to]);
    }
}