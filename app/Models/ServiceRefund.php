<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Traits\HasSlug;
use App\Enums\ServiceRefundStatus;

class ServiceRefund extends Model
{
    use HasFactory, HasSlug;

    protected $fillable = [
        'service_order_id',
        'buyer_id',
        'slug',
        'total_amount',
        'status',
        'processed_by',
    ];

    protected $casts = [
        'service_order_id' => 'integer',
        'buyer_id' => 'integer',
        'total_amount' => 'decimal:2',
        'processed_by' => 'integer',
        'status' => ServiceRefundStatus::class,
    ];

    protected string $slugPrefix = 'service-refund';
    protected int $slugMaxLength = 20;
    protected int $randomStringLength = 12;
    protected bool $regenerateSlugOnUpdate = false;
    protected bool $alwaysUseRandomStringInSlug = true;

    protected function getSlugSourceValue(): string
    {
        return '';
    }

    public function serviceOrder(): BelongsTo
    {
        return $this->belongsTo(ServiceOrder::class);
    }

    public function buyer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'buyer_id');
    }

    public function processedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'processed_by');
    }

    public function scopePending($query)
    {
        return $query->where('status', ServiceRefundStatus::PENDING);
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', ServiceRefundStatus::COMPLETED);
    }

    public function scopeRejected($query)
    {
        return $query->where('status', ServiceRefundStatus::REJECTED);
    }

    public function changeStatus(ServiceRefundStatus $to): void
    {
        if (! $this->status->canTransitionTo($to)) {
            throw new \DomainException('Bạn không thể chuyển thực hiện hành động này');
        }

        $this->update(['status' => $to]);
    }
}
