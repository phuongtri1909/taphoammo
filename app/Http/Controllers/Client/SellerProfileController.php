<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Product;
use App\Models\Service;
use App\Models\Review;
use App\Models\SeoSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Artesaos\SEOTools\Facades\SEOTools;
use Artesaos\SEOTools\Facades\SEOMeta;
use Artesaos\SEOTools\Facades\OpenGraph;
use Artesaos\SEOTools\Facades\TwitterCard;

class SellerProfileController extends Controller
{

    public function show(string $fullName)
    {
        $seller = User::where('full_name', $fullName)
            ->where('role', User::ROLE_SELLER)
            ->first();

        if (!$seller) {
            abort(404);
        }

        if ($seller->isSellerBanned()) {
            return view('client.pages.seller-profile.banned', [
                'seller' => $seller,
            ]);
        }

        // Dynamic SEO for seller profile
        $baseSeo = SeoSetting::getByPageKey('home');
        $seoData = SeoSetting::getSellerProfileSeo($seller, $baseSeo);
        
        SEOTools::setTitle($seoData->title);
        SEOTools::setDescription($seoData->description);
        SEOMeta::setKeywords($seoData->keywords);
        SEOTools::setCanonical(route('seller.profile', $seller->full_name));

        OpenGraph::setTitle($seoData->title);
        OpenGraph::setDescription($seoData->description);
        OpenGraph::setUrl(route('seller.profile', $seller->full_name));
        OpenGraph::setSiteName(config('app.name'));
        OpenGraph::addProperty('type', 'profile');
        OpenGraph::addImage($seoData->thumbnail);

        TwitterCard::setTitle($seoData->title);
        TwitterCard::setDescription($seoData->description);
        TwitterCard::setType('summary_large_image');
        TwitterCard::addImage($seoData->thumbnail);

        $registration = $seller->sellerRegistration;

        $products = Product::with(['subCategory.category'])
            ->visibleToClient()
            ->where('seller_id', $seller->id)
            ->withMin('variants', 'price')
            ->withMax('variants', 'price')
            ->withSum('variants', 'sold_count')
            ->withSum('variants', 'stock_quantity')
            ->orderByRaw('CASE WHEN featured_until IS NOT NULL AND featured_until > NOW() THEN 0 ELSE 1 END')
            ->orderByDesc('featured_until')
            ->orderByDesc('variants_sum_sold_count')
            ->paginate(20, ['*'], 'products');

        $services = Service::with(['serviceSubCategory.serviceCategory'])
            ->visibleToClient()
            ->where('seller_id', $seller->id)
            ->withMin('variants', 'price')
            ->withMax('variants', 'price')
            ->withSum('variants', 'sold_count')
            ->orderByRaw('CASE WHEN featured_until IS NOT NULL AND featured_until > NOW() THEN 0 ELSE 1 END')
            ->orderByDesc('featured_until')
            ->orderByDesc('variants_sum_sold_count')
            ->paginate(20, ['*'], 'services');

        // Get product IDs for this seller
        $productIds = Product::where('seller_id', $seller->id)->pluck('id')->toArray();
        $serviceIds = Service::where('seller_id', $seller->id)->pluck('id')->toArray();
        
        // Get all reviews for seller's products and services
        $productReviews = Review::where('reviewable_type', Product::class)
            ->whereIn('reviewable_id', $productIds)
            ->where('is_visible', true);
        
        $serviceReviews = Review::where('reviewable_type', Service::class)
            ->whereIn('reviewable_id', $serviceIds)
            ->where('is_visible', true);
        
        $totalReviewsCount = $productReviews->count() + $serviceReviews->count();
        $avgRating = 0;
        
        if ($totalReviewsCount > 0) {
            $productAvg = $productReviews->avg('rating') ?? 0;
            $serviceAvg = $serviceReviews->avg('rating') ?? 0;
            $productCount = $productReviews->count();
            $serviceCount = $serviceReviews->count();
            
            // Weighted average
            if ($productCount > 0 && $serviceCount > 0) {
                $avgRating = (($productAvg * $productCount) + ($serviceAvg * $serviceCount)) / $totalReviewsCount;
            } elseif ($productCount > 0) {
                $avgRating = $productAvg;
            } elseif ($serviceCount > 0) {
                $avgRating = $serviceAvg;
            }
        }

        $totalProductsSold = Product::where('seller_id', $seller->id)
            ->withSum('variants', 'sold_count')
            ->get()
            ->sum('variants_sum_sold_count') ?? 0;
        
        $totalServicesSold = Service::where('seller_id', $seller->id)
            ->withSum('variants', 'sold_count')
            ->get()
            ->sum('variants_sum_sold_count') ?? 0;

        $stats = [
            'total_products' => Product::visibleToClient()
                ->where('seller_id', $seller->id)
                ->count(),
            'total_services' => Service::visibleToClient()
                ->where('seller_id', $seller->id)
                ->count(),
            'total_sold' => $totalProductsSold + $totalServicesSold,
            'joined_date' => $seller->created_at,
            'rating' => round($avgRating, 1),
            'reviews_count' => $totalReviewsCount,
        ];

        $sellerInfo = [
            'id' => $seller->id,
            'name' => $seller->full_name,
            'slug' => $seller->full_name,
            'avatar' => $seller->avatar ? Storage::url($seller->avatar) : null,
            'joined_date' => $seller->created_at->format('d/m/Y'),
            'is_online' => false,
            'facebook_url' => $registration->facebook_url ?? null,
            'telegram_username' => $registration->telegram_username ?? null,
        ];

        $formattedProducts = $products->map(function ($product) {
            return [
                'id' => $product->id,
                'title' => $product->name,
                'slug' => $product->slug,
                'image' => $product->image ? Storage::url($product->image) : 'images/placeholder.jpg',
                'category' => $product->subCategory->category->name ?? 'N/A',
                'subcategory' => $product->subCategory->name ?? 'N/A',
                'price_min' => $product->variants_min_price ?? 0,
                'price_max' => $product->variants_max_price ?? 0,
                'sold_count' => $product->variants_sum_sold_count ?? 0,
                'stock' => $product->variants_sum_stock_quantity ?? 0,
                'is_featured' => $product->isFeatured(),
                'rating' => round($product->average_rating, 1),
                'reviews_count' => $product->reviews_count,
                'type' => 'product',
            ];
        });

        $formattedServices = $services->map(function ($service) {
            return [
                'id' => $service->id,
                'title' => $service->name,
                'slug' => $service->slug,
                'image' => $service->image ? Storage::url($service->image) : 'images/placeholder.jpg',
                'category' => $service->serviceSubCategory->serviceCategory->name ?? 'N/A',
                'subcategory' => $service->serviceSubCategory->name ?? 'N/A',
                'price_min' => $service->variants_min_price ?? 0,
                'price_max' => $service->variants_max_price ?? 0,
                'sold_count' => $service->variants_sum_sold_count ?? 0,
                'stock' => null,
                'is_featured' => $service->isFeatured(),
                'rating' => round($service->average_rating, 1),
                'reviews_count' => $service->reviews_count,
                'type' => 'service',
            ];
        });

        return view('client.pages.seller-profile.show', [
            'seller' => $sellerInfo,
            'stats' => $stats,
            'products' => $formattedProducts,
            'services' => $formattedServices,
            'productsPagination' => $products,
            'servicesPagination' => $services,
        ]);
    }
}


