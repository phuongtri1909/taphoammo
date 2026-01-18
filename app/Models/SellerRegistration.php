<?php

namespace App\Models;

use App\Enums\SellerRegistrationStatus;
use App\Traits\HasSlug;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SellerRegistration extends Model
{
    use HasFactory, HasSlug;

    protected $fillable = [
        'user_id',
        'slug',
        'phone',
        'bank_name',
        'bank_account_number',
        'bank_account_name',
        'facebook_url',
        'telegram_username',
        'status',
        'admin_note',
        'reviewed_at',
        'reviewed_by',
    ];

    /**
     * Slug configuration
     */
    protected string $slugSource = 'user_id';
    protected string $slugPrefix = 'seller-reg';
    protected int $slugMaxLength = 50;
    protected int $randomStringLength = 8;
    protected bool $regenerateSlugOnUpdate = false;

    /**
     * Get the source value for slug generation
     */
    protected function getSlugSourceValue(): string
    {
        $user = $this->user ?? User::find($this->user_id);
        $source = $user ? $user->full_name : 'seller';
        return $source . '-' . time();
    }

    protected $casts = [
        'reviewed_at' => 'datetime',
        'status' => SellerRegistrationStatus::class,
    ];

    /**
     * User relationship
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Reviewer relationship
     */
    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    /**
     * Check if pending
     */
    public function isPending(): bool
    {
        return $this->status === SellerRegistrationStatus::PENDING;
    }

    /**
     * Check if approved
     */
    public function isApproved(): bool
    {
        return $this->status === SellerRegistrationStatus::APPROVED;
    }

    /**
     * Check if rejected
     */
    public function isRejected(): bool
    {
        return $this->status === SellerRegistrationStatus::REJECTED;
    }

    /**
     * Scope pending
     */
    public function scopePending($query)
    {
        return $query->where('status', SellerRegistrationStatus::PENDING);
    }

    /**
     * Scope approved
     */
    public function scopeApproved($query)
    {
        return $query->where('status', SellerRegistrationStatus::APPROVED);
    }

    /**
     * Scope rejected
     */
    public function scopeRejected($query)
    {
        return $query->where('status', SellerRegistrationStatus::REJECTED);
    }

    /**
     * Approve registration
     */
    public function approve(User $reviewer, ?string $note = null): bool
    {
        $this->update([
            'status' => SellerRegistrationStatus::APPROVED,
            'admin_note' => $note,
            'reviewed_at' => now(),
            'reviewed_by' => $reviewer->id,
        ]);

        $this->user->update(['role' => User::ROLE_SELLER]);

        return true;
    }

    /**
     * Reject registration
     */
    public function reject(User $reviewer, ?string $note = null): bool
    {
        return $this->update([
            'status' => SellerRegistrationStatus::REJECTED,
            'admin_note' => $note,
            'reviewed_at' => now(),
            'reviewed_by' => $reviewer->id,
        ]);
    }
}
