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
        'buyer_id',
        'seller_id',
        'slug',
        'reason',
        'seller_note',
        'admin_note',
        'evidence',
        'evidence_files',
        'status',
        'resolved_at',
        'resolved_by',
    ];

    protected $casts = [
        'order_id' => 'integer',
        'order_item_id' => 'integer',
        'buyer_id' => 'integer',
        'seller_id' => 'integer',
        'evidence' => 'array',
        'evidence_files' => 'array',
        'resolved_at' => 'datetime',
        'resolved_by' => 'integer',
        'status' => DisputeStatus::class,
    ];

    protected string $slugPrefix = 'dispute';
    protected int $slugMaxLength = 20;
    protected int $randomStringLength = 12;
    protected bool $regenerateSlugOnUpdate = false;
    protected bool $alwaysUseRandomStringInSlug = true;

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

    public function items(): HasMany
    {
        return $this->hasMany(DisputeItem::class);
    }

    public function productValues()
    {
        return $this->hasManyThrough(ProductValue::class, DisputeItem::class, 'dispute_id', 'id', 'id', 'product_value_id');
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
            throw new \DomainException('Bạn không thể chuyển thực hiện hành động này');
        }
    
        $this->update(['status' => $to]);
    }
}