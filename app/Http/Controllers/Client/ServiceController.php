<?php

namespace App\Http\Controllers\Client;

use App\Models\Service;
use App\Models\SeoSetting;
use Illuminate\Http\Request;
use App\Models\ServiceCategory;
use App\Models\ServiceSubCategory;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Artesaos\SEOTools\Facades\SEOTools;
use Artesaos\SEOTools\Facades\SEOMeta;
use Artesaos\SEOTools\Facades\OpenGraph;
use Artesaos\SEOTools\Facades\TwitterCard;

class ServiceController extends Controller
{
    public function index(Request $request)
    {
        // SEO Settings
        $seoSetting = SeoSetting::getByPageKey('services');
        
        if ($seoSetting) {
            SEOTools::setTitle($seoSetting->title);
            SEOTools::setDescription($seoSetting->description);
            SEOMeta::setKeywords($seoSetting->keywords);
            SEOTools::setCanonical(url()->current());

            OpenGraph::setTitle($seoSetting->title);
            OpenGraph::setDescription($seoSetting->description);
            OpenGraph::setUrl(url()->current());
            OpenGraph::setSiteName(config('app.name'));
            if ($seoSetting->thumbnail) {
                OpenGraph::addImage($seoSetting->thumbnail_url);
            }

            TwitterCard::setTitle($seoSetting->title);
            TwitterCard::setDescription($seoSetting->description);
            TwitterCard::setType('summary_large_image');
        } else {
            SEOTools::setTitle('Danh sách dịch vụ - ' . config('app.name'));
            SEOTools::setDescription('Dịch vụ MMO chuyên nghiệp tại ' . config('app.name'));
            SEOTools::setCanonical(url()->current());
        }

        $query = Service::with(['serviceSubCategory.serviceCategory', 'seller'])
            ->visibleToClient();

        if ($request->filled('subcategory')) {
            $subcategory = ServiceSubCategory::where('slug', $request->subcategory)->first();
            if ($subcategory) {
                $query->where('service_sub_category_id', $subcategory->id);
            }
        } elseif ($request->filled('filters')) {
            $subcategorySlugs = is_array($request->filters) ? $request->filters : [$request->filters];
            $subcategoryIds = ServiceSubCategory::whereIn('slug', $subcategorySlugs)->pluck('id')->toArray();
            if (!empty($subcategoryIds)) {
                $query->whereIn('service_sub_category_id', $subcategoryIds);
            }
        } elseif ($request->filled('category')) {
            $category = ServiceCategory::where('slug', $request->category)->first();
            if ($category) {
                $query->whereHas('serviceSubCategory', function ($q) use ($category) {
                    $q->where('service_category_id', $category->id);
                });
            }
        }

        if ($request->filled('q')) {
            $searchTerm = $request->q;
            $query->where(function ($q) use ($searchTerm) {
                $q->where('name', 'like', '%' . $searchTerm . '%')
                    ->orWhereHas('seller', function ($sellerQuery) use ($searchTerm) {
                        $sellerQuery->where('full_name', 'like', '%' . $searchTerm . '%');
                    });
            });
        }

        $query->withMin('variants', 'price')
            ->withMax('variants', 'price')
            ->withSum('variants', 'sold_count');

        $sortBy = $request->get('sort', 'popular');
        if ($sortBy === 'price_asc') {
            $query->orderBy('variants_min_price', 'asc')
                ->orderByRaw('CASE WHEN featured_until IS NOT NULL AND featured_until > NOW() THEN 0 ELSE 1 END')
                ->orderByDesc('featured_until')
                ->orderByDesc('variants_sum_sold_count');
        } elseif ($sortBy === 'price_desc') {
            $query->orderBy('variants_max_price', 'desc')
                ->orderByRaw('CASE WHEN featured_until IS NOT NULL AND featured_until > NOW() THEN 0 ELSE 1 END')
                ->orderByDesc('featured_until')
                ->orderByDesc('variants_sum_sold_count');
        } else {
            $query->orderByRaw('CASE WHEN featured_until IS NOT NULL AND featured_until > NOW() THEN 0 ELSE 1 END')
                ->orderByDesc('featured_until')
                ->orderByDesc('variants_sum_sold_count')
                ->orderByRaw('CASE WHEN variants_sum_sold_count > 0 THEN created_at END ASC')
                ->orderByDesc('created_at');
        }

        $services = $query->paginate(12);

        // Load user favorites for efficiency
        $userFavorites = [];
        if (Auth::check()) {
            $userFavorites = Auth::user()->favorites()
                ->where('favoritable_type', Service::class)
                ->pluck('favoritable_id')
                ->toArray();
        }

        $categorySlug = $request->category;
        $subcategorySlug = $request->subcategory;
        $selectedSubcategory = null;
        $selectedCategory = null;

        if ($subcategorySlug) {
            $selectedSubcategory = ServiceSubCategory::with(['serviceCategory.serviceSubCategories' => function ($q) {
                $q->active()->ordered();
            }])->where('slug', $subcategorySlug)->first();
            $selectedCategory = $selectedSubcategory?->serviceCategory;
            $categorySlug = $selectedCategory?->slug;
            
            if ($selectedCategory) {
                $categories = collect([$selectedCategory]);
                $filterOptions = $selectedCategory->serviceSubCategories ?? collect();
            } else {
                $categories = collect();
                $filterOptions = collect();
            }
        } elseif ($categorySlug) {
            $selectedCategory = ServiceCategory::with(['serviceSubCategories' => function ($q) {
                $q->active()->ordered();
            }])->where('slug', $categorySlug)->first();
            $filterOptions = $selectedCategory?->serviceSubCategories ?? collect();
            $categories = $selectedCategory ? collect([$selectedCategory]) : collect();
        } else {
            $categories = ServiceCategory::with(['serviceSubCategories' => function ($q) {
                $q->active()->ordered();
            }])->active()->ordered()->get();
            
            $filterOptions = ServiceSubCategory::with('serviceCategory')->active()->ordered()->get();
        }

        $formattedServices = $services->map(function ($service) use ($userFavorites) {
            return [
                'id' => $service->id,
                'title' => $service->name,
                'name' => $service->name,
                'slug' => $service->slug,
                'image' => $service->image ? Storage::url($service->image) : 'images/placeholder.jpg',
                'rating' => round($service->average_rating, 1), 
                'reviews_count' => $service->reviews_count, 
                'sold_count' => $service->variants_sum_sold_count ?? 0,
                'complaint_rate' => 0.0, 
                'seller' => $service->seller->full_name ?? 'N/A',
                'category' => $service->serviceSubCategory->name ?? 'N/A',
                'description' => $service->description ?? '',
                'price' => $service->variants_min_price ?? 0,
                'is_favorited' => in_array($service->id, $userFavorites),
            ];
        });

        $categoryName = 'Dịch vụ';
        if ($selectedSubcategory) {
            $categoryName = $selectedSubcategory->name ?? 'Dịch vụ';
        } elseif ($selectedCategory) {
            $categoryName = $selectedCategory->name ?? 'Dịch vụ';
        }

        return view('client.pages.services.index', [
            'category' => $categoryName,
            'products' => $formattedServices,
            'totalProducts' => $services->total(),
            'sortBy' => $sortBy,
            'filters' => is_array($request->filters) ? $request->filters : ($request->filters ? [$request->filters] : []),
            'filterOptions' => $filterOptions,
            'categorySlug' => $categorySlug,
            'subcategorySlug' => $subcategorySlug,
            'categories' => $categories,
            'pagination' => $services,
        ]);
    }

    public function show(Service $service)
    {
        if (!$service->isVisibleToClient()) {
            abort(404);
        }

        $service->load([
            'serviceSubCategory.serviceCategory',
            'seller',
            'variants' => function ($query) {
                $query->visibleToClient();
            },
            'visibleReviews' => function ($query) {
                $query->with('user:id,full_name')->orderBy('created_at', 'desc');
            }
        ]);

        // Dynamic SEO for service
        $baseSeo = SeoSetting::getByPageKey('services');
        $seoData = SeoSetting::getServiceSeo($service, $baseSeo);
        
        SEOTools::setTitle($seoData->title);
        SEOTools::setDescription($seoData->description);
        SEOMeta::setKeywords($seoData->keywords);
        SEOTools::setCanonical(route('services.show', $service->slug));

        OpenGraph::setTitle($seoData->title);
        OpenGraph::setDescription($seoData->description);
        OpenGraph::setUrl(route('services.show', $service->slug));
        OpenGraph::setSiteName(config('app.name'));
        OpenGraph::addProperty('type', 'product');
        OpenGraph::addImage($seoData->thumbnail);

        TwitterCard::setTitle($seoData->title);
        TwitterCard::setDescription($seoData->description);
        TwitterCard::setType('summary_large_image');
        TwitterCard::addImage($seoData->thumbnail);

        $minPrice = $service->variants->min('price') ?? 0;
        $maxPrice = $service->variants->max('price') ?? 0;
        $totalSold = $service->variants->sum('sold_count');

        $formattedVariants = $service->variants->map(function ($variant) {
            return [
                'id' => $variant->id,
                'name' => $variant->name,
                'slug' => $variant->slug,
                'price' => $variant->price,
                'sold_count' => $variant->sold_count,
                'is_available' => $variant->isPurchasable(),
            ];
        })->values();

        // Get reviews with formatted data
        $formattedReviews = $service->visibleReviews->map(function ($review) {
            return [
                'id' => $review->id,
                'user_name' => $review->user->full_name ?? 'Người dùng',
                'rating' => $review->rating,
                'content' => $review->content,
                'created_at' => $review->created_at->format('d/m/Y H:i'),
                'created_at_diff' => $review->created_at->diffForHumans(),
            ];
        })->values()->toArray();

        $averageRating = $service->visibleReviews->avg('rating') ?? 0;
        $reviewsCount = $service->visibleReviews->count();

        $formattedService = [
            'id' => $service->id,
            'title' => $service->name,
            'name' => $service->name,
            'slug' => $service->slug,
            'image' => $service->image ? Storage::url($service->image) : 'images/placeholder.jpg',
            'rating' => round($averageRating, 1), 
            'reviews_count' => $reviewsCount, 
            'sold_count' => $totalSold,
            'complaint_rate' => 0.0, 
            'seller' => $service->seller->full_name ?? 'N/A',
            'seller_online' => false, 
            'category' => $service->serviceSubCategory->name ?? 'N/A',
            'price' => $minPrice,
            'price_min' => $minPrice,
            'price_max' => $maxPrice,
            'description' => $service->long_description ?? $service->description ?? '',
            'reviews' => $formattedReviews, 
        ];

        $similarServices = Service::with(['serviceSubCategory.serviceCategory', 'seller'])
            ->visibleToClient()
            ->where('id', '!=', $service->id)
            ->where('service_sub_category_id', $service->service_sub_category_id)
            ->withMin('variants', 'price')
            ->withMax('variants', 'price')
            ->withSum('variants', 'sold_count')
            ->orderByRaw('CASE WHEN featured_until IS NOT NULL AND featured_until > NOW() THEN 0 ELSE 1 END')
            ->orderByDesc('featured_until')
            ->orderByDesc('variants_sum_sold_count')
            ->orderByRaw('CASE WHEN variants_sum_sold_count > 0 THEN created_at END ASC')
            ->orderByDesc('created_at')
            ->limit(10)
            ->get()
            ->map(function ($similar) {
                return [
                    'id' => $similar->id,
                    'title' => $similar->name,
                    'slug' => $similar->slug,
                    'image' => $similar->image ? Storage::url($similar->image) : 'images/placeholder.jpg',
                    'rating' => round($similar->average_rating, 1),
                    'reviews' => $similar->reviews_count,
                    'category' => $similar->serviceSubCategory->serviceCategory->name ?? 'N/A',
                    'subcategory' => $similar->serviceSubCategory->name ?? 'N/A',
                    'price_min' => $similar->variants_min_price ?? 0,
                    'price_max' => $similar->variants_max_price ?? 0,
                ];
            });

        return view('client.pages.services.show', [
            'product' => $formattedService,
            'variants' => $formattedVariants,
            'similarProducts' => $similarServices,
        ]);
    }

    public function buy(Request $request)
    {
        // Kiểm tra đăng nhập trước
        if (!Auth::check()) {
            return response()->json([
                'success' => false,
                'message' => 'Vui lòng đăng nhập để mua dịch vụ'
            ], 401);
        }

        $request->validate([
            'service_slug' => 'required|string|max:100|exists:services,slug',
            'variant_slug' => 'nullable|string|max:100',
        ], [
            'service_slug.required' => 'Vui lòng chọn dịch vụ',
            'service_slug.string' => 'Dịch vụ không hợp lệ',
            'service_slug.exists' => 'Dịch vụ không tồn tại',
            'variant_slug.string' => 'Biến thể không hợp lệ',
        ]);

        try {
            $serviceOrderService = new \App\Services\ServiceOrderService();
            $serviceOrder = $serviceOrderService->buy(
                Auth::id(),
                $request->service_slug,
                $request->variant_slug
            );

            return response()->json([
                'success' => true,
                'message' => 'Đặt dịch vụ thành công!',
                'order' => [
                    'slug' => $serviceOrder->slug,
                    'total_amount' => $serviceOrder->total_amount,
                ]
            ]);

        } catch (\DomainException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);

        } catch (\RuntimeException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);

        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Mua dịch vụ lỗi không xác định: ' . $e->getMessage(), [
                'service_slug' => $request->service_slug,
                'variant_slug' => $request->variant_slug,
                'user_id' => Auth::id(),
                'exception' => $e->getTraceAsString(),
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Đã có lỗi xảy ra. Vui lòng thử lại sau.'
            ], 500);
        }
    }
}