<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

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
        'avatar',
        'active',
        'key_active',
        'key_reset_password',
        'reset_password_at',
    ];

    const ROLE_ADMIN = 'admin';
    const ROLE_USER = 'user';

    const ACTIVE_YES = 1;
    const ACTIVE_NO = 0;


    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
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
}
