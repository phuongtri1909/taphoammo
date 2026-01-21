<?php

namespace App\Http\Controllers\Client;

use App\Models\Product;
use App\Models\Category;
use App\Models\SubCategory;
use App\Models\SeoSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Models\Favorite;
use Artesaos\SEOTools\Facades\SEOTools;
use Artesaos\SEOTools\Facades\SEOMeta;
use Artesaos\SEOTools\Facades\OpenGraph;
use Artesaos\SEOTools\Facades\TwitterCard;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        // SEO Settings
        $seoSetting = SeoSetting::getByPageKey('products');
        
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
            SEOTools::setTitle('Danh sách sản phẩm - ' . config('app.name'));
            SEOTools::setDescription('Khám phá hàng ngàn sản phẩm số chất lượng tại ' . config('app.name'));
            SEOTools::setCanonical(url()->current());
        }

        $query = Product::with(['subCategory.category', 'seller'])
            ->visibleToClient();

        if ($request->filled('subcategory')) {
            $subcategory = SubCategory::where('slug', $request->subcategory)->first();
            if ($subcategory) {
                $query->where('sub_category_id', $subcategory->id);
            }
        } elseif ($request->filled('filters')) {
            $subcategorySlugs = is_array($request->filters) ? $request->filters : [$request->filters];
            $subcategoryIds = SubCategory::whereIn('slug', $subcategorySlugs)->pluck('id')->toArray();
            if (!empty($subcategoryIds)) {
                $query->whereIn('sub_category_id', $subcategoryIds);
            }
        } elseif ($request->filled('category')) {
            $category = Category::where('slug', $request->category)->first();
            if ($category) {
                $query->whereHas('subCategory', function ($q) use ($category) {
                    $q->where('category_id', $category->id);
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
            ->withSum('variants', 'stock_quantity')
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

        $products = $query->paginate(12);

        // Load user favorites for efficiency
        $userFavorites = [];
        if (Auth::check()) {
            $userFavorites = Auth::user()->favorites()
                ->where('favoritable_type', Product::class)
                ->pluck('favoritable_id')
                ->toArray();
        }

        $categorySlug = $request->category;
        $subcategorySlug = $request->subcategory;
        $selectedSubcategory = null;
        $selectedCategory = null;

        if ($subcategorySlug) {
            $selectedSubcategory = SubCategory::with(['category.subCategories' => function ($q) {
                $q->active()->ordered();
            }])->where('slug', $subcategorySlug)->first();
            $selectedCategory = $selectedSubcategory?->category;
            $categorySlug = $selectedCategory?->slug;
            
            if ($selectedCategory) {
                $categories = collect([$selectedCategory]);
                $filterOptions = $selectedCategory->subCategories ?? collect();
            } else {
                $categories = collect();
                $filterOptions = collect();
            }
        } elseif ($categorySlug) {
            $selectedCategory = Category::with(['subCategories' => function ($q) {
                $q->active()->ordered();
            }])->where('slug', $categorySlug)->first();
            $filterOptions = $selectedCategory?->subCategories ?? collect();
            $categories = $selectedCategory ? collect([$selectedCategory]) : collect();
        } else {
            $categories = Category::with(['subCategories' => function ($q) {
                $q->active()->ordered();
            }])->active()->ordered()->get();
            
            $filterOptions = SubCategory::with('category')->active()->ordered()->get();
        }

        $formattedProducts = $products->map(function ($product) use ($userFavorites) {
            return [
                'id' => $product->id,
                'title' => $product->name,
                'name' => $product->name,
                'slug' => $product->slug,
                'image' => $product->image ? Storage::url($product->image) : 'images/placeholder.jpg',
                'rating' => round($product->average_rating, 1), 
                'reviews_count' => $product->reviews_count, 
                'sold_count' => $product->variants_sum_sold_count ?? 0,
                'complaint_rate' => 0.0, 
                'seller' => $product->seller->full_name ?? 'N/A',
                'category' => $product->subCategory->name ?? 'N/A',
                'description' => $product->description ?? '',
                'stock' => $product->variants_sum_stock_quantity ?? 0,
                'price' => $product->variants_min_price ?? 0,
                'is_favorited' => in_array($product->id, $userFavorites),
            ];
        });

        $categoryName = 'Sản phẩm';
        if ($selectedSubcategory) {
            $categoryName = $selectedSubcategory->name ?? 'Sản phẩm';
        } elseif ($selectedCategory) {
            $categoryName = $selectedCategory->name ?? 'Sản phẩm';
        }

        return view('client.pages.products.index', [
            'category' => $categoryName,
            'products' => $formattedProducts,
            'totalProducts' => $products->total(),
            'sortBy' => $sortBy,
            'filters' => is_array($request->filters) ? $request->filters : ($request->filters ? [$request->filters] : []),
            'filterOptions' => $filterOptions,
            'categorySlug' => $categorySlug,
            'subcategorySlug' => $subcategorySlug,
            'categories' => $categories,
            'pagination' => $products,
        ]);
    }

    public function show(Product $product)
    {
        if (!$product->isVisibleToClient()) {
            abort(404);
        }

        $product->load([
            'subCategory.category',
            'seller',
            'variants' => function ($query) {
                $query->visibleToClient();
            },
            'visibleReviews' => function ($query) {
                $query->with('user:id,full_name')->orderBy('created_at', 'desc');
            }
        ]);

        // Dynamic SEO for product
        $baseSeo = SeoSetting::getByPageKey('products');
        $seoData = SeoSetting::getProductSeo($product, $baseSeo);
        
        SEOTools::setTitle($seoData->title);
        SEOTools::setDescription($seoData->description);
        SEOMeta::setKeywords($seoData->keywords);
        SEOTools::setCanonical(route('products.show', $product->slug));

        OpenGraph::setTitle($seoData->title);
        OpenGraph::setDescription($seoData->description);
        OpenGraph::setUrl(route('products.show', $product->slug));
        OpenGraph::setSiteName(config('app.name'));
        OpenGraph::addProperty('type', 'product');
        OpenGraph::addImage($seoData->thumbnail);

        TwitterCard::setTitle($seoData->title);
        TwitterCard::setDescription($seoData->description);
        TwitterCard::setType('summary_large_image');
        TwitterCard::addImage($seoData->thumbnail);

        $minPrice = $product->variants->min('price') ?? 0;
        $maxPrice = $product->variants->max('price') ?? 0;
        $totalStock = $product->variants->sum('stock_quantity');
        $totalSold = $product->variants->sum('sold_count');

        $formattedVariants = $product->variants->map(function ($variant) {
            return [
                'id' => $variant->id,
                'name' => $variant->name,
                'slug' => $variant->slug,
                'price' => $variant->price,
                'stock_quantity' => $variant->stock_quantity,
                'sold_count' => $variant->sold_count,
                'is_available' => $variant->isPurchasable(),
            ];
        })->values();

        // Get reviews with formatted data
        $formattedReviews = $product->visibleReviews->map(function ($review) {
            return [
                'id' => $review->id,
                'user_name' => $review->user->full_name ?? 'Người dùng',
                'rating' => $review->rating,
                'content' => $review->content,
                'created_at' => $review->created_at->format('d/m/Y H:i'),
                'created_at_diff' => $review->created_at->diffForHumans(),
            ];
        })->values()->toArray();

        $averageRating = $product->visibleReviews->avg('rating') ?? 0;
        $reviewsCount = $product->visibleReviews->count();

        $formattedProduct = [
            'id' => $product->id,
            'title' => $product->name,
            'name' => $product->name,
            'slug' => $product->slug,
            'image' => $product->image ? Storage::url($product->image) : 'images/placeholder.jpg',
            'rating' => round($averageRating, 1), 
            'reviews_count' => $reviewsCount, 
            'sold_count' => $totalSold,
            'complaint_rate' => 0.0, 
            'seller' => $product->seller->full_name ?? 'N/A',
            'seller_online' => false, 
            'category' => $product->subCategory->name ?? 'N/A',
            'stock' => $totalStock,
            'price' => $minPrice,
            'price_min' => $minPrice,
            'price_max' => $maxPrice,
            'description' => $product->long_description ?? $product->description ?? '',
            'reviews' => $formattedReviews, 
        ];

        $similarProducts = Product::with(['subCategory.category', 'seller'])
            ->visibleToClient()
            ->where('id', '!=', $product->id)
            ->where('sub_category_id', $product->sub_category_id)
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
                    'category' => $similar->subCategory->category->name ?? 'N/A',
                    'subcategory' => $similar->subCategory->name ?? 'N/A',
                    'price_min' => $similar->variants_min_price ?? 0,
                    'price_max' => $similar->variants_max_price ?? 0,
                ];
            });

        return view('client.pages.products.show', [
            'product' => $formattedProduct,
            'variants' => $formattedVariants,
            'similarProducts' => $similarProducts,
        ]);
    }

    public function buy(Request $request)
    {
        $request->validate([
            'product_slug' => 'required|string|max:100',
            'variant_slug' => 'nullable|string|max:100',
            'quantity' => 'required|integer|min:1',
        ], [
            'product_slug.required' => 'Vui lòng chọn sản phẩm',
            'product_slug.string' => 'Sản phẩm không hợp lệ',
            'variant_slug.string' => 'Biến thể không hợp lệ',
            'quantity.required' => 'Vui lòng nhập số lượng',
            'quantity.integer' => 'Số lượng phải là số nguyên',
            'quantity.min' => 'Số lượng phải lớn hơn 0',
        ]);

        if (!Auth::check()) {
            return response()->json([
                'success' => false,
                'message' => 'Vui lòng đăng nhập để mua hàng'
            ], 401);
        }

        try {
            $orderService = new \App\Services\OrderService();
            $order = $orderService->buy(
                Auth::id(),
                $request->product_slug,
                $request->variant_slug,
                $request->quantity
            );

            return response()->json([
                'success' => true,
                'message' => 'Mua hàng thành công!',
                'order' => [
                    'slug' => $order->slug,
                    'total_amount' => $order->total_amount,
                ]
            ]);

        } catch (\DomainException $e) {
            // Business logic error - trả về message cho user
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);

        } catch (\RuntimeException $e) {
            // System error - đã log trong OrderService
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);

        } catch (\Exception $e) {
            Log::error('Mua hàng sản phẩm lỗi không xác định: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Đã có lỗi xảy ra. Vui lòng thử lại sau.'
            ], 500);
        }
    }
}