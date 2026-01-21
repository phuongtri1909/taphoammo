<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Enums\SellerRegistrationStatus;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'full_name',
        'email',
        'password',
        'role',
        'is_seller_banned',
        'seller_ban_reason',
        'seller_banned_at',
        'seller_banned_by',
        'avatar',
        'active',
        'key_active',
        'key_reset_password',
        'reset_password_at',
        'last_activation_email_sent_at',
        'last_reset_password_email_sent_at',
        'two_factor_secret',
        'two_factor_recovery_codes',
        'two_factor_confirmed_at',
        'bank_name',
        'bank_account_number',
        'bank_account_name',
        'qr_code',
    ];

    const ROLE_ADMIN = 'admin';
    const ROLE_USER = 'user';
    const ROLE_SELLER = 'seller';

    const ACTIVE_YES = 1;
    const ACTIVE_NO = 0;


    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'reset_password_at' => 'datetime',
        'last_activation_email_sent_at' => 'datetime',
        'last_reset_password_email_sent_at' => 'datetime',
        'two_factor_confirmed_at' => 'datetime',
        'is_seller_banned' => 'boolean',
        'seller_banned_at' => 'datetime',
    ];

    public function isActive(): bool
    {
        return $this->active === self::ACTIVE_YES;
    }

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_secret',
        'two_factor_recovery_codes',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'reset_password_at' => 'datetime',
            'last_activation_email_sent_at' => 'datetime',
            'last_reset_password_email_sent_at' => 'datetime',
            'two_factor_confirmed_at' => 'datetime',
        ];
    }

    /**
     * Kiểm tra user có role cụ thể không
     */
    public function hasRole($role)
    {
        return $this->role === $role;
    }

    /**
     * Kiểm tra user có bất kỳ role nào trong danh sách không
     */
    public function hasAnyRole(array $roles)
    {
        return in_array($this->role, $roles);
    }

    /**
     * Kiểm tra user có tất cả roles trong danh sách không
     */
    public function hasAllRoles(array $roles)
    {
        foreach ($roles as $role) {
            if (!$this->hasRole($role)) {
                return false;
            }
        }
        return true;
    }

    public function wallet()
    {
        return $this->hasOne(Wallet::class);
    }

    public function getBalanceAttribute()
    {
        return number_format($this->wallet?->balance, 0, ',', '.');
    }

    /**
     * Check if user has two factor authentication enabled
     */
    public function hasTwoFactorEnabled(): bool
    {
        return !is_null($this->two_factor_secret) 
            && !is_null($this->two_factor_confirmed_at);
    }

    /**
     * Check if user is a seller
     */
    public function isSeller(): bool
    {
        return $this->role === self::ROLE_SELLER;
    }

    /**
     * Check if user is an admin
     */
    public function isAdmin(): bool
    {
        return $this->role === self::ROLE_ADMIN;
    }

    /**
     * Check if user can register as seller
     */
    public function canRegisterAsSeller(): bool
    {
        return !$this->isAdmin() && !$this->isSeller();
    }

    /**
     * Seller registration relationship
     */
    public function sellerRegistration()
    {
        return $this->hasOne(SellerRegistration::class);
    }

    /**
     * Get pending seller registration
     */
    public function hasPendingSellerRegistration(): bool
    {
        return $this->sellerRegistration()
            ->where('status', SellerRegistrationStatus::PENDING)
            ->exists();
    }

    /**
     * Check if seller is banned
     */
    public function isSellerBanned(): bool
    {
        return $this->isSeller() && $this->is_seller_banned;
    }

    /**
     * Check if seller is active (not banned)
     */
    public function isSellerActive(): bool
    {
        return $this->isSeller() && !$this->is_seller_banned;
    }

    /**
     * Ban seller
     */
    public function banSeller(User $admin, string $reason): bool
    {
        if (!$this->isSeller()) {
            return false;
        }

        return $this->update([
            'is_seller_banned' => true,
            'seller_ban_reason' => $reason,
            'seller_banned_at' => now(),
            'seller_banned_by' => $admin->id,
        ]);
    }

    /**
     * Unban seller
     */
    public function unbanSeller(): bool
    {
        if (!$this->isSeller()) {
            return false;
        }

        return $this->update([
            'is_seller_banned' => false,
            'seller_ban_reason' => null,
            'seller_banned_at' => null,
            'seller_banned_by' => null,
        ]);
    }

    /**
     * Get who banned this seller
     */
    public function bannedBy()
    {
        return $this->belongsTo(User::class, 'seller_banned_by');
    }

    /**
     * Products relationship (for sellers)
     */
    public function products()
    {
        return $this->hasMany(Product::class, 'seller_id');
    }

    /**
     * Scope to get active sellers (not banned)
     */
    public function scopeActiveSellers($query)
    {
        return $query->where('role', self::ROLE_SELLER)
                     ->where('is_seller_banned', false);
    }

    /**
     * Scope to get banned sellers
     */
    public function scopeBannedSellers($query)
    {
        return $query->where('role', self::ROLE_SELLER)
                     ->where('is_seller_banned', true);
    }

    /**
     * Favorites relationship
     */
    public function favorites()
    {
        return $this->hasMany(Favorite::class);
    }

    /**
     * Check if user has favorited an item
     */
    public function hasFavorited($favoritable)
    {
        return $this->favorites()
            ->where('favoritable_type', get_class($favoritable))
            ->where('favoritable_id', $favoritable->id)
            ->exists();
    }

    /**
     * Featured histories relationship (as seller)
     */
    public function featuredHistories()
    {
        return $this->hasMany(FeaturedHistory::class, 'seller_id');
    }
}
