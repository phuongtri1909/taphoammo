<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Favorite;
use App\Models\Product;
use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FavoriteController extends Controller
{
    public function toggle(Request $request)
    {
        $request->validate([
            'type' => 'required|in:product,service',
            'slug' => 'required|string',
        ]);

        $user = Auth::user();
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Vui lòng đăng nhập để yêu thích sản phẩm/dịch vụ.',
            ], 401);
        }

        $model = $request->type === 'product' ? Product::class : Service::class;
        $item = $model::where('slug', $request->slug)->first();

        if (!$item) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy sản phẩm/dịch vụ.',
            ], 404);
        }

        $favorite = Favorite::where('user_id', $user->id)
            ->where('favoritable_type', $model)
            ->where('favoritable_id', $item->id)
            ->first();

        if ($favorite) {
            $favorite->delete();
            return response()->json([
                'success' => true,
                'is_favorited' => false,
                'message' => 'Đã bỏ yêu thích.',
            ]);
        } else {
            Favorite::create([
                'user_id' => $user->id,
                'favoritable_type' => $model,
                'favoritable_id' => $item->id,
            ]);
            return response()->json([
                'success' => true,
                'is_favorited' => true,
                'message' => 'Đã thêm vào yêu thích.',
            ]);
        }
    }

    public function index(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('sign-in');
        }

        $type = $request->get('type', 'all');

        $query = Favorite::where('user_id', $user->id)
            ->with(['favoritable']);

        if ($type === 'product') {
            $query->where('favoritable_type', Product::class);
        } elseif ($type === 'service') {
            $query->where('favoritable_type', Service::class);
        }

        $favorites = $query->orderByDesc('created_at')->paginate(12);

        $favoriteItems = collect();
        foreach ($favorites as $favorite) {
            $item = $favorite->favoritable;
            if (!$item) {
                continue;
            }

            $isProduct = $item instanceof Product;
            $routeName = $isProduct ? 'products.show' : 'services.show';
            $type = $isProduct ? 'product' : 'service';

            $favoriteItems->push([
                'id' => $item->id,
                'slug' => $item->slug,
                'name' => $item->name,
                'title' => $item->name,
                'image' => $item->image ? \Illuminate\Support\Facades\Storage::url($item->image) : 'images/placeholder.jpg',
                'type' => $type,
                'route' => route($routeName, $item->slug),
                'favorited_at' => $favorite->created_at,
            ]);
        }

        return view('client.pages.favorites.index', [
            'favorites' => $favorites,
            'favoriteItems' => $favoriteItems,
            'currentType' => $type,
        ]);
    }
}
