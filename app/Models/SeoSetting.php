<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SeoSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'page_key',
        'title',
        'description',
        'keywords',
        'thumbnail',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean'
    ];

    /**
     * Scope for active SEO settings
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Get SEO setting by page key
     */
    public static function getByPageKey($pageKey)
    {
        return static::where('page_key', $pageKey)->active()->first();
    }

    /**
     * Get thumbnail URL
     */
    public function getThumbnailUrlAttribute()
    {
        if ($this->thumbnail) {
            return asset('storage/' . $this->thumbnail);
        }
        return asset('images/dev/Thumbnail.png'); 
    }

    /**
     * Get all page keys
     */
    public static function getPageKeys()
    {
        return [
            'home' => 'Trang chủ',
            'about' => 'Trang giới thiệu',
            'contact' => 'Trang liên hệ',
            'gallery' => 'Trang hình ảnh',
            'news' => 'Trang tin tức',
            'projects' => 'Trang dự án'
        ];
    }

    /**
     * Get SEO data for blog post
     */
    public static function getBlogSeo($blog, $baseSeo = null)
    {
        $title = $blog->title;
        $description = strip_tags($blog->content);
        $description = strlen($description) > 160 ? substr($description, 0, 160) . '...' : $description;
        
        // Combine blog keywords with base keywords
        $keywords = $blog->title;
        if ($blog->category) {
            $keywords .= ', ' . $blog->category->name;
        }
        if ($baseSeo && $baseSeo->keywords) {
            $keywords .= ', ' . $baseSeo->keywords;
        }
        $keywords .= ', ' . config('app.name');

        return (object) [
            'title' => $title,
            'description' => $description,
            'keywords' => $keywords,
            'thumbnail' => $blog->image ? asset('storage/' . $blog->image) : ($baseSeo ? $baseSeo->thumbnail_url : asset('images/dev/Thumbnail.png'))
        ];
    }

    /**
     * Get SEO data for project
     */
    public static function getProjectSeo($project, $baseSeo = null)
    {
        $title = $project->title;
        $description = strip_tags($project->description);
        $description = strlen($description) > 160 ? substr($description, 0, 160) . '...' : $description;
        
        // Combine project keywords with base keywords
        $keywords = $project->title;
        if ($baseSeo && $baseSeo->keywords) {
            $keywords .= ', ' . $baseSeo->keywords;
        }
        $keywords .= ', ' . config('app.name') . ', dự án';

        return (object) [
            'title' => $title,
            'description' => $description,
            'keywords' => $keywords,
            'thumbnail' => $project->hero_image ? asset('storage/' . $project->hero_image) : ($baseSeo ? $baseSeo->thumbnail_url : asset('images/dev/Thumbnail.png'))
        ];
    }
}
