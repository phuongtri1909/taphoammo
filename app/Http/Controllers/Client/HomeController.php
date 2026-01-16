<?php

namespace App\Http\Controllers\Client;

use App\Models\Category;
use App\Models\Product;
use App\Enums\ProductStatus;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;

class HomeController extends Controller
{
    public function index(Request $request)
    {
        $categories = Category::active()->ordered()->get();
        
        $featuredProducts = Product::with(['subCategory.category', 'seller'])
            ->visibleToClient()
            ->whereNotNull('featured_until')
            ->whereRaw('featured_until > NOW()')
            ->withMin('variants', 'price')
            ->withMax('variants', 'price')
            ->withSum('variants', 'sold_count')
            ->orderByDesc('featured_until')
            ->get();

        $shortcutsProducts = collect();

        if ($featuredProducts->count() < 5) {
            $neededCount = 5 - $featuredProducts->count();
            $featuredIds = $featuredProducts->pluck('id')->toArray();

            $popularProducts = Product::with(['subCategory.category', 'seller'])
                ->visibleToClient()
                ->whereNotIn('id', $featuredIds)
                ->withSum('variants', 'sold_count')
                ->havingRaw('variants_sum_sold_count > 0')
                ->withMin('variants', 'price')
                ->withMax('variants', 'price')
                ->orderByDesc('variants_sum_sold_count')
                ->orderByDesc('created_at')
                ->limit($neededCount)
                ->get();

            $shortcutsProducts = $featuredProducts->merge($popularProducts);
        } else {
            $shortcutsProducts = $featuredProducts;
        }

        $formattedProducts = $shortcutsProducts->map(function ($product) {
            return [
                'id' => $product->id,
                'title' => $product->name,
                'slug' => $product->slug,
                'image' => $product->image ? Storage::url($product->image) : 'images/placeholder.jpg',
                'rating' => 5.0, 
                'reviews' => 0, 
                'category' => $product->subCategory->category->name ?? 'N/A',
                'subcategory' => $product->subCategory->name ?? 'N/A',
                'price_min' => $product->variants_min_price ?? 0,
                'price_max' => $product->variants_max_price ?? 0,
            ];
        });
        
        return view('client.pages.home', [
            'categories' => $categories,
            'shortcutsProducts' => $formattedProducts,
        ]);
    }

    public function getCategories()
    {
        $categories = Category::with(['subCategories' => function ($q) {
            $q->active()->ordered();
        }])->active()->ordered()->get();

        $data = $categories->map(function ($category) {
            return [
                'id' => $category->id,
                'name' => $category->name,
                'slug' => $category->slug,
                'type' => 'category',
                'subcategories' => $category->subCategories->map(function ($subCategory) use ($category) {
                    return [
                        'id' => $subCategory->id,
                        'name' => $subCategory->name,
                        'slug' => $subCategory->slug,
                        'type' => 'subcategory',
                        'category_id' => $subCategory->category_id,
                        'category_slug' => $category->slug,
                    ];
                }),
            ];
        });

        return response()->json($data);
    }
}
