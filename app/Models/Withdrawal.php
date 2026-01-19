<?php

namespace App\Models;

use App\Enums\WithdrawalStatus;
use App\Traits\HasSlug;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Withdrawal extends Model
{
    use HasFactory, HasSlug, SoftDeletes;

    protected $fillable = [
        'user_id',
        'slug',
        'amount',
        'bank_name',
        'bank_account_number',
        'bank_account_name',
        'status',
        'otp_code',
        'otp_expires_at',
        'otp_verified',
        'note',
        'admin_note',
        'processed_by',
        'processed_at',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'otp_expires_at' => 'datetime',
        'otp_verified' => 'boolean',
        'processed_at' => 'datetime',
        'status' => WithdrawalStatus::class,
    ];

    protected $hidden = [
        'otp_code',
    ];

    protected string $slugPrefix = 'wd';
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

    public function processedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'processed_by');
    }

    public function getAmountFormattedAttribute(): string
    {
        return number_format($this->amount, 0, ',', '.') . '₫';
    }

    public function isOtpValid(string $otp): bool
    {
        if (!$this->otp_code || !$this->otp_expires_at) {
            return false;
        }

        if ($this->otp_expires_at->isPast()) {
            return false;
        }

        return $this->otp_code === $otp;
    }

    public function isPendingOtp(): bool
    {
        return $this->status === WithdrawalStatus::PENDING_OTP;
    }

    public function isPending(): bool
    {
        return $this->status === WithdrawalStatus::PENDING;
    }

    public function isProcessing(): bool
    {
        return $this->status === WithdrawalStatus::PROCESSING;
    }

    public function isCompleted(): bool
    {
        return $this->status === WithdrawalStatus::COMPLETED;
    }

    public function isRejected(): bool
    {
        return $this->status === WithdrawalStatus::REJECTED;
    }

    public function scopePendingOtp($query)
    {
        return $query->where('status', WithdrawalStatus::PENDING_OTP);
    }

    public function scopePending($query)
    {
        return $query->where('status', WithdrawalStatus::PENDING);
    }

    public function scopeProcessing($query)
    {
        return $query->where('status', WithdrawalStatus::PROCESSING);
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', WithdrawalStatus::COMPLETED);
    }

    public function scopeRejected($query)
    {
        return $query->where('status', WithdrawalStatus::REJECTED);
    }

    public function scopeNeedsProcessing($query)
    {
        return $query->whereIn('status', [WithdrawalStatus::PENDING, WithdrawalStatus::PROCESSING]);
    }
}
