<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Review;
use App\Models\Order;
use App\Models\ServiceOrder;
use App\Models\Product;
use App\Models\Service;
use App\Enums\OrderStatus;
use App\Enums\ServiceOrderStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ReviewController extends Controller
{
    /**
     * Store a review for a product (from order)
     */
    public function storeProductReview(Request $request, Order $order)
    {
        $user = Auth::user();
        
        // Validate order belongs to user
        if ($order->buyer_id !== $user->id) {
            return response()->json(['error' => 'Bạn không có quyền đánh giá đơn hàng này'], 403);
        }
        
        // Check if order status allows review (COMPLETED or PARTIAL_REFUNDED)
        if (!in_array($order->status, [OrderStatus::COMPLETED, OrderStatus::PARTIAL_REFUNDED])) {
            return response()->json(['error' => 'Đơn hàng chưa hoàn thành, không thể đánh giá'], 400);
        }
        
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'rating' => 'required|integer|min:1|max:5',
            'content' => 'nullable|string|max:1000',
        ]);
        
        $productId = $request->product_id;
        
        // Check if product is in this order
        $orderHasProduct = $order->items()
            ->whereHas('productVariant', function ($q) use ($productId) {
                $q->where('product_id', $productId);
            })
            ->exists();
        
        if (!$orderHasProduct) {
            return response()->json(['error' => 'Sản phẩm không có trong đơn hàng này'], 400);
        }
        
        // Check if already reviewed
        if (!Review::canReview('product', $order->id, $user->id, $productId)) {
            return response()->json(['error' => 'Bạn đã đánh giá sản phẩm này trong đơn hàng này rồi'], 400);
        }
        
        try {
            $review = Review::create([
                'reviewable_type' => Product::class,
                'reviewable_id' => $productId,
                'user_id' => $user->id,
                'order_type' => 'product',
                'order_id' => $order->id,
                'rating' => $request->rating,
                'content' => $request->content,
                'is_visible' => true,
            ]);
            
            return response()->json([
                'success' => true,
                'message' => 'Đánh giá thành công!',
                'review' => [
                    'id' => $review->id,
                    'rating' => $review->rating,
                    'content' => $review->content,
                    'created_at' => $review->created_at->format('d/m/Y H:i'),
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Có lỗi xảy ra, vui lòng thử lại'], 500);
        }
    }
    
    /**
     * Store a review for a service (from service order)
     */
    public function storeServiceReview(Request $request, ServiceOrder $serviceOrder)
    {
        $user = Auth::user();
        
        // Validate order belongs to user
        if ($serviceOrder->buyer_id !== $user->id) {
            return response()->json(['error' => 'Bạn không có quyền đánh giá đơn hàng này'], 403);
        }
        
        // Check if order status allows review (COMPLETED or PARTIAL_REFUNDED)
        if (!in_array($serviceOrder->status, [ServiceOrderStatus::COMPLETED, ServiceOrderStatus::PARTIAL_REFUNDED])) {
            return response()->json(['error' => 'Đơn hàng chưa hoàn thành, không thể đánh giá'], 400);
        }
        
        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'content' => 'nullable|string|max:1000',
        ]);
        
        // Get service from service order
        $serviceId = $serviceOrder->serviceVariant->service_id;
        
        // Check if already reviewed
        if (!Review::canReview('service', $serviceOrder->id, $user->id, $serviceId)) {
            return response()->json(['error' => 'Bạn đã đánh giá dịch vụ này trong đơn hàng này rồi'], 400);
        }
        
        try {
            $review = Review::create([
                'reviewable_type' => Service::class,
                'reviewable_id' => $serviceId,
                'user_id' => $user->id,
                'order_type' => 'service',
                'order_id' => $serviceOrder->id,
                'rating' => $request->rating,
                'content' => $request->content,
                'is_visible' => true,
            ]);
            
            return response()->json([
                'success' => true,
                'message' => 'Đánh giá thành công!',
                'review' => [
                    'id' => $review->id,
                    'rating' => $review->rating,
                    'content' => $review->content,
                    'created_at' => $review->created_at->format('d/m/Y H:i'),
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Có lỗi xảy ra, vui lòng thử lại'], 500);
        }
    }
    
    /**
     * Get reviews for a product
     */
    public function getProductReviews(Product $product, Request $request)
    {
        $perPage = $request->get('per_page', 10);
        
        $reviews = $product->visibleReviews()
            ->with(['user:id,full_name'])
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
        
        return response()->json([
            'reviews' => $reviews->items(),
            'average_rating' => $product->average_rating,
            'total_reviews' => $product->reviews_count,
            'pagination' => [
                'current_page' => $reviews->currentPage(),
                'last_page' => $reviews->lastPage(),
                'per_page' => $reviews->perPage(),
                'total' => $reviews->total(),
            ]
        ]);
    }
    
    /**
     * Get reviews for a service
     */
    public function getServiceReviews(Service $service, Request $request)
    {
        $perPage = $request->get('per_page', 10);
        
        $reviews = $service->visibleReviews()
            ->with(['user:id,full_name'])
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
        
        return response()->json([
            'reviews' => $reviews->items(),
            'average_rating' => $service->average_rating,
            'total_reviews' => $service->reviews_count,
            'pagination' => [
                'current_page' => $reviews->currentPage(),
                'last_page' => $reviews->lastPage(),
                'per_page' => $reviews->perPage(),
                'total' => $reviews->total(),
            ]
        ]);
    }
    
    /**
     * Get review stats by rating
     */
    public function getProductReviewStats(Product $product)
    {
        $stats = Review::forProduct($product->id)
            ->visible()
            ->selectRaw('rating, COUNT(*) as count')
            ->groupBy('rating')
            ->pluck('count', 'rating')
            ->toArray();
        
        // Fill missing ratings with 0
        $fullStats = [];
        for ($i = 5; $i >= 1; $i--) {
            $fullStats[$i] = $stats[$i] ?? 0;
        }
        
        return response()->json([
            'stats' => $fullStats,
            'average_rating' => $product->average_rating,
            'total_reviews' => $product->reviews_count,
        ]);
    }
    
    /**
     * Get review stats by rating for service
     */
    public function getServiceReviewStats(Service $service)
    {
        $stats = Review::forService($service->id)
            ->visible()
            ->selectRaw('rating, COUNT(*) as count')
            ->groupBy('rating')
            ->pluck('count', 'rating')
            ->toArray();
        
        // Fill missing ratings with 0
        $fullStats = [];
        for ($i = 5; $i >= 1; $i--) {
            $fullStats[$i] = $stats[$i] ?? 0;
        }
        
        return response()->json([
            'stats' => $fullStats,
            'average_rating' => $service->average_rating,
            'total_reviews' => $service->reviews_count,
        ]);
    }
}
