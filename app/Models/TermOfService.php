<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TermOfService extends Model
{
    use HasFactory;

    protected $table = 'terms_of_service';

    protected $fillable = [
        'title',
        'content',
        'summary',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Get active terms
     */
    public static function active()
    {
        return static::where('is_active', true);
    }

    /**
     * Get latest active terms
     */
    public static function getLatest()
    {
        return static::active()->latest()->first();
    }
}
