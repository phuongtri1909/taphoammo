<?php

namespace App\Models;

use App\Traits\HasSlug;
use App\Enums\CommonStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ServiceCategory extends Model
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
    protected string $slugPrefix = 'service-category';
    protected int $slugMaxLength = 50;
    protected int $randomStringLength = 8;
    protected bool $regenerateSlugOnUpdate = false;

    public function serviceSubCategories(): HasMany
    {
        return $this->hasMany(ServiceSubCategory::class);
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
}
