<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class HeaderConfig extends Model
{
    use HasFactory;

    protected $fillable = [
        'key',
        'label',
        'config_data',
        'is_active',
    ];

    protected $casts = [
        'config_data' => 'array',
        'is_active' => 'boolean',
    ];

    public static function getSupportBar()
    {
        return static::where('key', 'support_bar')->first();
    }

    public static function getPromotionalBanner()
    {
        return static::where('key', 'promotional_banner')->first();
    }

    public static function getSearchBackground()
    {
        return static::where('key', 'search_background')->first();
    }

    public function getConfig($key, $default = null)
    {
        return $this->config_data[$key] ?? $default;
    }

    public function setConfig($key, $value)
    {
        $config = $this->config_data ?? [];
        $config[$key] = $value;
        $this->config_data = $config;
        return $this;
    }
}
