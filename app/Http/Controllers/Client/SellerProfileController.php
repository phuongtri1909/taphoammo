<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

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
            ->paginate(20);

        $stats = [
            'total_products' => Product::visibleToClient()
                ->where('seller_id', $seller->id)
                ->count(),
            'total_sold' => Product::where('seller_id', $seller->id)
                ->withSum('variants', 'sold_count')
                ->get()
                ->sum('variants_sum_sold_count') ?? 0,
            'joined_date' => $seller->created_at,
            'rating' => 5.0,
            'reviews_count' => 0,
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
            ];
        });

        return view('client.pages.seller-profile.show', [
            'seller' => $sellerInfo,
            'stats' => $stats,
            'products' => $formattedProducts,
            'pagination' => $products,
        ]);
    }
}


