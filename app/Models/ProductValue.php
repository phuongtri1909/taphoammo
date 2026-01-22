<?php

namespace App\Models;

use App\Enums\ProductValueStatus;
use App\Traits\HasSlug;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Gate;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ProductValue extends Model
{
    use HasFactory, HasSlug;

    protected $fillable = [
        'product_variant_id',
        'slug',
        'encrypted_data',
        'status',
        'order_id',
        'order_item_id',
        'sold_to_user_id',
        'sold_at',
    ];

    protected $casts = [
        'product_variant_id' => 'integer',
        'order_id' => 'integer',
        'order_item_id' => 'integer',
        'sold_to_user_id' => 'integer',
        'sold_at' => 'datetime',
        'status' => ProductValueStatus::class,
    ];

    protected $hidden = [
        'encrypted_data',
    ];

    protected string $slugPrefix = 'val';
    protected int $slugMaxLength = 15;
    protected int $randomStringLength = 12;
    protected bool $regenerateSlugOnUpdate = false;
    protected bool $alwaysUseRandomStringInSlug = true;

    protected function getSlugSourceValue(): string
    {
        return '';
    }

    public function productVariant(): BelongsTo
    {
        return $this->belongsTo(ProductVariant::class);
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function orderItem(): BelongsTo
    {
        return $this->belongsTo(OrderItem::class);
    }

    public function soldToUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sold_to_user_id');
    }

    public function setDataAttribute(array $data): void
    {
        $this->attributes['encrypted_data'] = Crypt::encryptString(json_encode($data));
    }

    public function getDataAttribute(): ?array
    {
        if (empty($this->encrypted_data)) {
            return null;
        }

        try {
            return json_decode(Crypt::decryptString($this->encrypted_data), true);
        } catch (\Exception $e) {
            return null;
        }
    }

    public function getDecryptedDataFor(?User $user = null): ?array
    {
        $user = $user ?? Auth::user();
        
        if (!$user || !Gate::forUser($user)->allows('viewData', $this)) {
            return null;
        }

        return $this->data;
    }

    public function canViewDataBy(?User $user = null): bool
    {
        $user = $user ?? Auth::user();
        
        if (!$user) {
            return false;
        }

        return Gate::forUser($user)->allows('viewData', $this);
    }

    public function getSeller(): ?User
    {
        return $this->productVariant?->product?->seller;
    }

    public function scopeAvailable($query)
    {
        return $query->where('status', ProductValueStatus::AVAILABLE);
    }

    public function scopeSold($query)
    {
        return $query->where('status', ProductValueStatus::SOLD);
    }


    public function scopeRefunded($query)
    {
        return $query->where('status', ProductValueStatus::REFUNDED);
    }

    public function scopeInvalid($query)
    {
        return $query->where('status', ProductValueStatus::INVALID);
    }

    public function markAsSold(Order $order, OrderItem $orderItem, User $buyer): void
    {
        $this->update([
            'status' => ProductValueStatus::SOLD,
            'order_id' => $order->id,
            'order_item_id' => $orderItem->id,
            'sold_to_user_id' => $buyer->id,
            'sold_at' => now(),
        ]);
    }

    public function markAsAvailable(): void
    {
        $this->update([
            'status' => ProductValueStatus::AVAILABLE,
            'order_id' => null,
            'order_item_id' => null,
            'sold_to_user_id' => null,
            'sold_at' => null,
        ]);
    }

    public function changeStatus(ProductValueStatus $to): void
    {
        if (! $this->status->canTransitionTo($to)) {
            throw new \DomainException('Bạn không thể chuyển thực hiện hành động này');
        }

        $this->update(['status' => $to]);
    }

    public function isPurchasable(): bool
    {
        return $this->status->canBePurchased();
    }

    public function scopePurchasable($query)
    {
        return $query->where('status', ProductValueStatus::AVAILABLE);
    }
}
