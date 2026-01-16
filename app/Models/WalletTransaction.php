<?php

namespace App\Models;

use App\Enums\WalletTransactionType;
use App\Enums\WalletTransactionStatus;
use App\Traits\HasSlug;
use Illuminate\Database\Eloquent\Model;
use App\Enums\WalletTransactionReferenceType;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

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