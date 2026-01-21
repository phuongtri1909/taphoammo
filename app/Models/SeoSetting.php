<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Storage;

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
        return asset('images/logo/Logo-site-1050-x-300.webp'); 
    }

    /**
     * Get all page keys for admin management
     */
    public static function getPageKeys()
    {
        return [
            'home' => 'Trang chủ',
            'products' => 'Danh sách sản phẩm',
            'services' => 'Danh sách dịch vụ',
            'contact' => 'Liên hệ',
            'faqs' => 'Câu hỏi thường gặp',
            'terms_of_service' => 'Điều khoản sử dụng',
            'shares' => 'Chia sẻ / Blog',
            'login' => 'Đăng nhập',
            'register' => 'Đăng ký',
            'forgot_password' => 'Quên mật khẩu',
            'seller_register' => 'Đăng ký bán hàng',
        ];
    }

    /**
     * Get SEO data for Product
     */
    public static function getProductSeo($product, $baseSeo = null)
    {
        $title = $product->name . ' - ' . config('app.name');
        
        $description = $product->description;
        if ($product->long_description) {
            $description = strip_tags($product->long_description);
        }
        $description = mb_strlen($description) > 160 ? mb_substr($description, 0, 160) . '...' : $description;
        
        $keywords = $product->name;
        if ($product->subCategory) {
            $keywords .= ', ' . $product->subCategory->name;
            if ($product->subCategory->category) {
                $keywords .= ', ' . $product->subCategory->category->name;
            }
        }
        if ($product->seller) {
            $keywords .= ', ' . $product->seller->full_name;
        }
        if ($baseSeo && $baseSeo->keywords) {
            $keywords .= ', ' . $baseSeo->keywords;
        }
        $keywords .= ', ' . config('app.name') . ', mua bán tài khoản, MMO';

        $thumbnail = asset('images/logo/Logo-site-1050-x-300.webp');
        if ($product->image) {
            $thumbnail = Storage::url($product->image);
        } elseif ($baseSeo && $baseSeo->thumbnail) {
            $thumbnail = $baseSeo->thumbnail_url;
        }

        return (object) [
            'title' => $title,
            'description' => $description,
            'keywords' => $keywords,
            'thumbnail' => $thumbnail
        ];
    }

    /**
     * Get SEO data for Service
     */
    public static function getServiceSeo($service, $baseSeo = null)
    {
        $title = $service->name . ' - ' . config('app.name');
        
        $description = $service->description;
        if ($service->long_description) {
            $description = strip_tags($service->long_description);
        }
        $description = mb_strlen($description) > 160 ? mb_substr($description, 0, 160) . '...' : $description;
        
        $keywords = $service->name;
        if ($service->serviceSubCategory) {
            $keywords .= ', ' . $service->serviceSubCategory->name;
            if ($service->serviceSubCategory->serviceCategory) {
                $keywords .= ', ' . $service->serviceSubCategory->serviceCategory->name;
            }
        }
        if ($service->seller) {
            $keywords .= ', ' . $service->seller->full_name;
        }
        if ($baseSeo && $baseSeo->keywords) {
            $keywords .= ', ' . $baseSeo->keywords;
        }
        $keywords .= ', ' . config('app.name') . ', dịch vụ MMO';

        $thumbnail = asset('images/logo/Logo-site-1050-x-300.webp');
        if ($service->image) {
            $thumbnail = Storage::url($service->image);
        } elseif ($baseSeo && $baseSeo->thumbnail) {
            $thumbnail = $baseSeo->thumbnail_url;
        }

        return (object) [
            'title' => $title,
            'description' => $description,
            'keywords' => $keywords,
            'thumbnail' => $thumbnail
        ];
    }

    /**
     * Get SEO data for Share (Blog post)
     */
    public static function getShareSeo($share, $baseSeo = null)
    {
        $title = $share->title . ' - ' . config('app.name');
        
        $description = $share->excerpt;
        if (!$description) {
            $description = strip_tags($share->content);
        }
        $description = mb_strlen($description) > 160 ? mb_substr($description, 0, 160) . '...' : $description;
        
        $keywords = $share->title;
        if ($share->category) {
            $keywords .= ', ' . $share->category->name;
        }
        if ($share->author) {
            $keywords .= ', ' . $share->author->full_name;
        }
        if ($baseSeo && $baseSeo->keywords) {
            $keywords .= ', ' . $baseSeo->keywords;
        }
        $keywords .= ', ' . config('app.name') . ', chia sẻ kinh nghiệm, MMO';

        $thumbnail = asset('images/logo/Logo-site-1050-x-300.webp');
        if ($share->image) {
            $thumbnail = Storage::url($share->image);
        } elseif ($baseSeo && $baseSeo->thumbnail) {
            $thumbnail = $baseSeo->thumbnail_url;
        }

        return (object) [
            'title' => $title,
            'description' => $description,
            'keywords' => $keywords,
            'thumbnail' => $thumbnail
        ];
    }

    /**
     * Get SEO data for Seller Profile
     */
    public static function getSellerProfileSeo($seller, $baseSeo = null)
    {
        $title = 'Gian hàng ' . $seller->full_name . ' - ' . config('app.name');
        
        $description = 'Xem các sản phẩm và dịch vụ từ gian hàng ' . $seller->full_name . ' trên ' . config('app.name') . '. Mua bán tài khoản, dịch vụ MMO uy tín.';
        
        $keywords = $seller->full_name . ', gian hàng ' . $seller->full_name;
        if ($baseSeo && $baseSeo->keywords) {
            $keywords .= ', ' . $baseSeo->keywords;
        }
        $keywords .= ', ' . config('app.name') . ', người bán uy tín, MMO';

        $thumbnail = asset('images/logo/Logo-site-1050-x-300.webp');
        if ($seller->avatar) {
            $thumbnail = Storage::url($seller->avatar);
        } elseif ($baseSeo && $baseSeo->thumbnail) {
            $thumbnail = $baseSeo->thumbnail_url;
        }

        return (object) [
            'title' => $title,
            'description' => $description,
            'keywords' => $keywords,
            'thumbnail' => $thumbnail
        ];
    }
}
