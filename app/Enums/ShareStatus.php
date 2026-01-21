<?php

namespace App\Enums;

enum ShareStatus: string
{
    case DRAFT = 'draft';
    case PENDING = 'pending';
    case APPROVED = 'approved';
    case REJECTED = 'rejected';
    case HIDDEN = 'hidden';

    public function label(): string
    {
        return match ($this) {
            self::DRAFT => 'Nháp',
            self::PENDING => 'Chờ duyệt',
            self::APPROVED => 'Đã duyệt',
            self::REJECTED => 'Từ chối',
            self::HIDDEN => 'Đã ẩn',
        };
    }

    public function badgeColor(): string
    {
        return match ($this) {
            self::DRAFT => 'secondary',
            self::PENDING => 'warning',
            self::APPROVED => 'success',
            self::REJECTED => 'danger',
            self::HIDDEN => 'dark',
        };
    }

    public static function sellerCanSet(): array
    {
        return [
            self::DRAFT,
            self::PENDING,
            self::HIDDEN,
        ];
    }

    public static function adminCanSet(): array
    {
        return [
            self::DRAFT,
            self::PENDING,
            self::APPROVED,
            self::REJECTED,
            self::HIDDEN,
        ];
    }

    public static function adminCanSetValues(): array
    {
        return array_map(fn($status) => $status->value, self::adminCanSet());
    }

    public static function sellerCanSetValues(): array
    {
        return array_map(fn($status) => $status->value, self::sellerCanSet());
    }
}
