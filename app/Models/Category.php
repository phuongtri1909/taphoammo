<?php

namespace App\Models;

use App\Enums\CommonStatus;
use App\Traits\HasSlug;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Category extends Model
{
    use HasFactory, HasSlug;

    protected $fillable = [
        'name',
        'slug',
        'order',
        'description',
        'status',
        'icon',
    ];

    protected $casts = [
        'order' => 'integer',
        'status' => CommonStatus::class,
    ];

    protected string $slugSource = 'name';
    protected string $slugPrefix = 'category';
    protected int $slugMaxLength = 50;
    protected int $randomStringLength = 8;
    protected bool $regenerateSlugOnUpdate = false;
    protected bool $useRandomStringInSlug = false;

    public function subCategories(): HasMany
    {
        return $this->hasMany(SubCategory::class);
    }

    public function scopeActive($query)
    {
        return $query->where('status', CommonStatus::ACTIVE);
    }

    public function scopeInactive($query)
    {
        return $query->where('status', CommonStatus::INACTIVE);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('order', 'asc');
    }

    /**
     * Check if icon is SVG code
     */
    public function isIconSvgCode(): bool
    {
        if (!$this->icon) {
            return false;
        }
        return strpos($this->icon, '<svg') !== false || strpos($this->icon, '<?xml') !== false;
    }

    /**
     * Check if icon is a file path
     */
    public function isIconFile(): bool
    {
        if (!$this->icon) {
            return false;
        }
        if ($this->isIconSvgCode()) {
            return false;
        }
        return \Illuminate\Support\Facades\Storage::disk('public')->exists($this->icon);
    }
}