<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Social extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'key',
        'url',
        'icon',
        'is_active',
        'sort_order'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_active' => 'boolean',
        'sort_order' => 'integer'
    ];

    /**
     * Scope a query to only include active social links.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function getIconHtmlAttribute()
    {
        if (Str::startsWith($this->icon, 'custom-')) {
            return '<span class="' . $this->icon . '"></span>';
        }

        return '<i class="' . $this->icon . '"></i>';
    }
}