<?php

namespace App\Models;

use App\Traits\HasSlug;
use App\Enums\CommonStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ServiceSubCategory extends Model
{
    use HasFactory, HasSlug;

    protected $fillable = [
        'service_category_id',
        'name',
        'slug',
        'order',
        'description',
        'status',
    ];

    protected $casts = [
        'service_category_id' => 'integer',
        'order' => 'integer',
        'status' => CommonStatus::class,
    ];

    protected string $slugSource = 'name';
    protected string $slugPrefix = 'subcategory';
    protected int $slugMaxLength = 50;
    protected int $randomStringLength = 8;
    protected bool $regenerateSlugOnUpdate = false;

    public function serviceCategory()
    {
        return $this->belongsTo(ServiceCategory::class, 'service_category_id');
    }

    public function category()
    {
        return $this->belongsTo(ServiceCategory::class, 'service_category_id');
    }

    public function services(): HasMany
    {
        return $this->hasMany(Service::class);
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
