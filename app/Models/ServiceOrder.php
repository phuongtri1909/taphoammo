<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Traits\HasSlug;
use App\Enums\ServiceOrderStatus;

class ServiceOrder extends Model
{
    use HasFactory, HasSlug;

    protected $fillable = [
        'buyer_id',
        'seller_id',
        'service_variant_id',
        'slug',
        'total_amount',
        'status',
        'seller_confirmed_at',
        'seller_reconfirmed_at',
        'last_dispute_resolved_at',
        'last_dispute_created_at',
    ];

    protected $casts = [
        'buyer_id' => 'integer',
        'seller_id' => 'integer',
        'service_variant_id' => 'integer',
        'total_amount' => 'decimal:2',
        'status' => ServiceOrderStatus::class,
        'seller_confirmed_at' => 'datetime',
        'seller_reconfirmed_at' => 'datetime',
        'last_dispute_resolved_at' => 'datetime',
        'last_dispute_created_at' => 'datetime',
    ];

    protected string $slugPrefix = 'service-order';
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

    public function serviceVariant(): BelongsTo
    {
        return $this->belongsTo(ServiceVariant::class);
    }

    public function disputes(): HasMany
    {
        return $this->hasMany(ServiceDispute::class);
    }

    public function refunds(): HasMany
    {
        return $this->hasMany(ServiceRefund::class);
    }

    public function scopePending($query)
    {
        return $query->where('status', ServiceOrderStatus::PENDING);
    }

    public function scopePaid($query)
    {
        return $query->where('status', ServiceOrderStatus::PAID);
    }

    public function scopeSellerConfirmed($query)
    {
        return $query->where('status', ServiceOrderStatus::SELLER_CONFIRMED);
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', ServiceOrderStatus::COMPLETED);
    }

    public function scopeRefunded($query)
    {
        return $query->where('status', ServiceOrderStatus::REFUNDED);
    }

    public function scopePartialRefunded($query)
    {
        return $query->where('status', ServiceOrderStatus::PARTIAL_REFUNDED);
    }

    public function scopeDisputed($query)
    {
        return $query->where('status', ServiceOrderStatus::DISPUTED);
    }

    

    public function changeStatus(ServiceOrderStatus $to): void
    {
        if (! $this->status->canTransitionTo($to)) {
            throw new \DomainException('Bạn không thể chuyển thực hiện hành động này');
        }
    
        $this->update(['status' => $to]);
    }
}
