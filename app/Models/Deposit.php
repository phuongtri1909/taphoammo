<?php

namespace App\Models;

use App\Enums\DepositStatus;
use App\Traits\HasSlug;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Deposit extends Model
{
    use HasFactory, HasSlug, SoftDeletes;

    protected $fillable = [
        'user_id',
        'bank_id',
        'bank_name',
        'bank_code',
        'bank_account_number',
        'bank_account_name',
        'slug',
        'transaction_code',
        'casso_transaction_id',
        'amount',
        'amount_received',
        'status',
        'note',
        'processed_at',
        'casso_response'
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'amount_received' => 'decimal:2',
        'processed_at' => 'datetime',
        'casso_response' => 'array',
        'status' => DepositStatus::class,
    ];

    protected string $slugPrefix = 'dep';
    protected int $slugMaxLength = 15;
    protected int $randomStringLength = 12;
    protected bool $regenerateSlugOnUpdate = false;

    protected function getSlugSourceValue(): string
    {
        return '';
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function bank(): BelongsTo
    {
        return $this->belongsTo(Bank::class);
    }

    public function getAmountFormattedAttribute(): string
    {
        return number_format($this->amount, 0, ',', '.') . '₫';
    }

    public function getAmountReceivedFormattedAttribute(): string
    {
        return number_format($this->amount_received ?? 0, 0, ',', '.') . '₫';
    }

    public function isSuccessful(): bool
    {
        return $this->status === DepositStatus::SUCCESS;
    }

    public function isPending(): bool
    {
        return $this->status === DepositStatus::PENDING;
    }

    public function isFailed(): bool
    {
        return $this->status === DepositStatus::FAILED;
    }

    public function isCancelled(): bool
    {
        return $this->status === DepositStatus::CANCELLED;
    }

    public function scopeSuccessful($query)
    {
        return $query->where('status', DepositStatus::SUCCESS);
    }

    public function scopePending($query)
    {
        return $query->where('status', DepositStatus::PENDING);
    }

    public function scopeFailed($query)
    {
        return $query->where('status', DepositStatus::FAILED);
    }

    public function scopeCancelled($query)
    {
        return $query->where('status', DepositStatus::CANCELLED);
    }
}
