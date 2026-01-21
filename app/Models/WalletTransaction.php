<?php

namespace App\Models;

use App\Enums\WalletTransactionType;
use App\Enums\WalletTransactionStatus;
use App\Enums\WalletTransactionReferenceType;
use App\Traits\HasSlug;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Auth;

class WalletTransaction extends Model
{
    use HasFactory, HasSlug;

    protected $fillable = [
        'wallet_id',
        'slug',
        'type',
        'amount',
        'balance_before',
        'balance_after',
        'reference_type',
        'reference_id',
        'description',
        'status',
    ];

    protected $casts = [
        'wallet_id' => 'integer',
        'amount' => 'decimal:2',
        'balance_before' => 'decimal:2',
        'balance_after' => 'decimal:2',
        'reference_id' => 'integer',
        'status' => WalletTransactionStatus::class,
        'type' => WalletTransactionType::class,
        'reference_type' => WalletTransactionReferenceType::class,
    ];

    protected string $slugPrefix = 'txn';
    protected int $slugMaxLength = 15;
    protected int $randomStringLength = 12;
    protected bool $regenerateSlugOnUpdate = false;

    protected function getSlugSourceValue(): string
    {
        return '';
    }

    public function wallet(): BelongsTo
    {
        return $this->belongsTo(Wallet::class);
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class, 'reference_id');
    }

    public function serviceOrder(): BelongsTo
    {
        return $this->belongsTo(ServiceOrder::class, 'reference_id');
    }

    public function refund(): BelongsTo
    {
        return $this->belongsTo(Refund::class, 'reference_id');
    }

    public function deposit(): BelongsTo
    {
        return $this->belongsTo(Deposit::class, 'reference_id');
    }
    
    public function withdrawal(): BelongsTo
    {
        return $this->belongsTo(Withdrawal::class, 'reference_id');
    }

    public function manualWalletAdjustment(): BelongsTo
    {
        return $this->belongsTo(\App\Models\ManualWalletAdjustment::class, 'reference_id');
    }

    public function getReferenceSlugAttribute(): ?string
    {
        if (!$this->reference_type || !$this->reference_id) {
            return null;
        }

        return match ($this->reference_type) {
            WalletTransactionReferenceType::ORDER => $this->relationLoaded('order') 
                ? $this->order?->slug 
                : Order::find($this->reference_id)?->slug,
            WalletTransactionReferenceType::SERVICE_ORDER => $this->relationLoaded('serviceOrder') 
                ? $this->serviceOrder?->slug 
                : ServiceOrder::find($this->reference_id)?->slug,
            WalletTransactionReferenceType::REFUND => $this->relationLoaded('refund') 
                ? $this->refund?->slug 
                : Refund::find($this->reference_id)?->slug,
            WalletTransactionReferenceType::DEPOSIT => $this->relationLoaded('deposit') 
                ? $this->deposit?->slug 
                : Deposit::find($this->reference_id)?->slug,
            WalletTransactionReferenceType::WITHDRAWAL => $this->relationLoaded('withdrawal') 
                ? $this->withdrawal?->slug 
                : Withdrawal::find($this->reference_id)?->slug,
            WalletTransactionReferenceType::MANUAL_ADJUSTMENT => $this->relationLoaded('manualWalletAdjustment') 
                ? $this->manualWalletAdjustment?->slug 
                : \App\Models\ManualWalletAdjustment::find($this->reference_id)?->slug,
            default => null,
        };
    }

    public function getReferenceUrlAttribute(): ?string
    {
        if (!$this->reference_type || !$this->reference_id) {
            return null;
        }

        $user = Auth::user();
        if (!$user) {
            return null;
        }

        return match ($this->reference_type) {
            WalletTransactionReferenceType::ORDER => $this->getOrderUrl($user),
            WalletTransactionReferenceType::SERVICE_ORDER => $this->getServiceOrderUrl($user),
            WalletTransactionReferenceType::REFUND => $this->getRefundUrl($user),
            WalletTransactionReferenceType::DEPOSIT => $this->getDepositUrl($user),
            WalletTransactionReferenceType::WITHDRAWAL => $this->getWithdrawalUrl($user),
            default => null,
        };
    }

    protected function getOrderUrl($user): ?string
    {
        $slug = $this->getReferenceSlugAttribute();
        if (!$slug) {
            return null;
        }

        if ($this->relationLoaded('order') && $this->order) {
            if ($this->order->buyer_id === $user->id) {
                return route('orders.show', $slug);
            }
            if ($this->order->seller_id === $user->id && $user->role === 'seller') {
                return route('seller.orders.show', $slug);
            }
            if ($user->role === 'admin') {
                return route('admin.orders.show', $slug);
            }
        }

        $order = Order::find($this->reference_id);
        if ($order) {
            if ($order->buyer_id === $user->id) {
                return route('orders.show', $order->slug);
            }
            if ($order->seller_id === $user->id && $user->role === 'seller') {
                return route('seller.orders.show', $order->slug);
            }
            if ($user->role === 'admin') {
                return route('admin.orders.show', $order->slug);
            }
        }

        return null;
    }

    protected function getServiceOrderUrl($user): ?string
    {
        $slug = $this->getReferenceSlugAttribute();
        if (!$slug) {
            return null;
        }

        if ($this->relationLoaded('serviceOrder') && $this->serviceOrder) {
            if ($this->serviceOrder->buyer_id === $user->id) {
                return route('orders.show', $slug);
            }
            if ($this->serviceOrder->seller_id === $user->id && $user->role === 'seller') {
                return route('seller.service-orders.show', $slug);
            }
            if ($user->role === 'admin') {
                return route('admin.service-orders.show', $slug);
            }
        }

        $serviceOrder = ServiceOrder::find($this->reference_id);
        if ($serviceOrder) {
            if ($serviceOrder->buyer_id === $user->id) {
                return route('orders.show', $serviceOrder->slug);
            }
            if ($serviceOrder->seller_id === $user->id && $user->role === 'seller') {
                return route('seller.service-orders.show', $serviceOrder->slug);
            }
            if ($user->role === 'admin') {
                return route('admin.service-orders.show', $serviceOrder->slug);
            }
        }

        return null;
    }

    protected function getRefundUrl($user): ?string
    {
        $slug = $this->getReferenceSlugAttribute();
        if (!$slug) {
            return null;
        }

        if ($this->relationLoaded('refund') && $this->refund) {
            $refund = $this->refund;
            if ($refund->order && ($refund->order->buyer_id === $user->id || $refund->order->seller_id === $user->id)) {
                return route('orders.show', $refund->order->slug);
            }
            if ($user->role === 'admin') {
                return route('admin.refunds.show', $slug);
            }
        }

        $refund = Refund::find($this->reference_id);
        if ($refund && $refund->order) {
            if ($refund->order->buyer_id === $user->id || ($refund->order->seller_id === $user->id && $user->role === 'seller')) {
                return route('orders.show', $refund->order->slug);
            }
            if ($user->role === 'admin') {
                return route('admin.refunds.show', $refund->slug);
            }
        }

        return null;
    }

    protected function getDepositUrl($user): ?string
    {
        return route('deposit.index');
    }

    protected function getWithdrawalUrl($user): ?string
    {
        if ($user->role === 'admin') {
            $slug = $this->getReferenceSlugAttribute();
            return $slug ? route('admin.withdrawals.show', $slug) : null;
        }
        
        return route('withdrawal.index');
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', WalletTransactionStatus::COMPLETED);
    }

    public function scopePending($query)
    {
        return $query->where('status', WalletTransactionStatus::PENDING);
    }

    public function scopeDeposit($query)
    {
        return $query->where('type', WalletTransactionType::DEPOSIT);
    }

    public function scopeWithdraw($query)
    {
        return $query->where('type', WalletTransactionType::WITHDRAW);
    }

    public function scopePurchase($query)
    {
        return $query->where('type', WalletTransactionType::PURCHASE);
    }

    public function scopeRefund($query)
    {
        return $query->where('type', WalletTransactionType::REFUND);
    }

    public function scopeCommission($query)
    {
        return $query->where('type', WalletTransactionType::COMMISSION);
    }

    public function scopeSale($query)
    {
        return $query->where('type', WalletTransactionType::SALE);
    }

    public static function createTransaction(
        Wallet $wallet,
        string $type,
        float $amount,
        ?string $referenceType = null,
        ?int $referenceId = null,
        ?string $description = null,
        string $status = WalletTransactionStatus::COMPLETED
    ): self {
        $balanceBefore = $wallet->balance;
        
        $balanceAfter = match($type) {
            WalletTransactionType::DEPOSIT, WalletTransactionType::REFUND, WalletTransactionType::COMMISSION => $balanceBefore + $amount,
            WalletTransactionType::WITHDRAW, WalletTransactionType::PURCHASE => $balanceBefore - $amount,
            WalletTransactionType::MANUAL_ADJUSTMENT => $amount > 0 ? $balanceBefore + $amount : $balanceBefore + $amount, // amount can be positive or negative
            default => $balanceBefore,
        };

        $wallet->update(['balance' => $balanceAfter]);

        return self::create([
            'wallet_id' => $wallet->id,
            'type' => $type,
            'amount' => $amount,
            'balance_before' => $balanceBefore,
            'balance_after' => $balanceAfter,
            'reference_type' => $referenceType,
            'reference_id' => $referenceId,
            'description' => $description,
            'status' => WalletTransactionStatus::COMPLETED,
        ]);
    }

    public function changeStatus(WalletTransactionStatus $to): void
    {
        if (! $this->status->canTransitionTo($to)) {
            throw new \DomainException('Chuyển đổi trạng thái giao dịch ví không hợp lệ');
        }
    
        $this->update(['status' => $to]);
    }
}