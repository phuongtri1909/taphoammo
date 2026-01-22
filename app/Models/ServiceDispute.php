<?php

namespace App\Models;

use App\Traits\HasSlug;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Enums\ServiceDisputeStatus;

class ServiceDispute extends Model
{
    use HasFactory, HasSlug;

    protected $fillable = [
        'service_order_id',
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
        'service_order_id' => 'integer',
        'order_item_id' => 'integer',
        'buyer_id' => 'integer',
        'seller_id' => 'integer',
        'evidence' => 'array',
        'evidence_files' => 'array',
        'resolved_at' => 'datetime',
        'resolved_by' => 'integer',
        'status' => ServiceDisputeStatus::class,
    ];

    protected string $slugPrefix = 'service-dispute';
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
        return $query->where('status', ServiceDisputeStatus::OPEN);
    }

    public function scopeReviewing($query)
    {
        return $query->where('status', ServiceDisputeStatus::REVIEWING);
    }

    public function scopeApproved($query)
    {
        return $query->where('status', ServiceDisputeStatus::APPROVED);
    }

    public function scopeRejected($query)
    {
        return $query->where('status', ServiceDisputeStatus::REJECTED);
    }

    public function changeStatus(ServiceDisputeStatus $to): void
    {
        if (! $this->status->canTransitionTo($to)) {
            throw new \DomainException('Bạn không thể chuyển thực hiện hành động này');
        }
    
        $this->update(['status' => $to]);
    }
}
