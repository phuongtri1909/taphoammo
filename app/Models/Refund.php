<?php

namespace App\Models;

use App\Enums\RefundStatus;
use App\Traits\HasSlug;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Refund extends Model
{
    use HasFactory, HasSlug;

    protected $fillable = [
        'order_id',
        'buyer_id',
        'slug',
        'total_amount',
        'status',
        'processed_by',
    ];

    protected $casts = [
        'order_id' => 'integer',
        'buyer_id' => 'integer',
        'total_amount' => 'decimal:2',
        'processed_by' => 'integer',
        'status' => RefundStatus::class,
    ];

    protected string $slugPrefix = 'refund';
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

    public function buyer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'buyer_id');
    }

    public function processedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'processed_by');
    }

    public function items(): HasMany
    {
        return $this->hasMany(RefundItem::class);
    }

    public function scopePending($query)
    {
        return $query->where('status', RefundStatus::PENDING);
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', RefundStatus::COMPLETED);
    }

    public function scopeRejected($query)
    {
        return $query->where('status', RefundStatus::REJECTED);
    }

    public function changeStatus(RefundStatus $to): void
    {
        if (! $this->status->canTransitionTo($to)) {
            throw new \DomainException('Chuyển đổi trạng thái hoàn trả không hợp lệ');
        }

        $this->update(['status' => $to]);
    }
}
