<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Service;
use App\Models\Config;
use App\Services\FeaturedService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FeaturedController extends Controller
{
    protected FeaturedService $featuredService;

    public function __construct(FeaturedService $featuredService)
    {
        $this->featuredService = $featuredService;
    }

    public function index(Request $request)
    {
        $seller = Auth::user();
        $type = $request->get('type');

        $featuredPrice = $this->featuredService->getFeaturedPrice();
        $featuredHours = $this->featuredService->getFeaturedHours();

        $products = $this->featuredService->getSellerProducts($seller);
        $services = $this->featuredService->getSellerServices($seller);

        $featuredHistories = $this->featuredService->getSellerFeaturedHistory($seller, $type);

        return view('seller.pages.featured.index', compact(
            'products',
            'services',
            'featuredHistories',
            'featuredPrice',
            'featuredHours',
            'type'
        ));
    }

    public function featureProduct(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'times' => 'required|integer|min:1|max:100',
            'note' => 'nullable|string|max:500',
        ], [
            'product_id.required' => 'Vui lòng chọn sản phẩm.',
            'product_id.exists' => 'Sản phẩm không tồn tại.',
            'times.required' => 'Vui lòng nhập số lần đề xuất.',
            'times.integer' => 'Số lần phải là số nguyên.',
            'times.min' => 'Số lần tối thiểu là 1.',
            'times.max' => 'Số lần tối đa là 100.',
            'note.max' => 'Ghi chú không được vượt quá 500 ký tự.',
        ]);

        $seller = Auth::user();
        $product = Product::findOrFail($request->product_id);

        try {
            $featuredHistory = $this->featuredService->featureProduct(
                $seller,
                $product,
                $request->times,
                $request->note
            );

            return response()->json([
                'success' => true,
                'message' => 'Đề xuất sản phẩm thành công! Sản phẩm sẽ được hiển thị ưu tiên trong ' . $featuredHistory->hours . ' giờ.',
                'data' => [
                    'featured_until' => $featuredHistory->featured_until->format('d/m/Y H:i'),
                    'amount' => $featuredHistory->formatted_amount,
                ]
            ]);

        } catch (\DomainException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);

        } catch (\Exception $e) {
            \Log::error('Feature product error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra. Vui lòng thử lại sau.'
            ], 500);
        }
    }

    public function featureService(Request $request)
    {
        $request->validate([
            'service_id' => 'required|exists:services,id',
            'times' => 'required|integer|min:1|max:100',
            'note' => 'nullable|string|max:500',
        ], [
            'service_id.required' => 'Vui lòng chọn dịch vụ.',
            'service_id.exists' => 'Dịch vụ không tồn tại.',
            'times.required' => 'Vui lòng nhập số lần đề xuất.',
            'times.integer' => 'Số lần phải là số nguyên.',
            'times.min' => 'Số lần tối thiểu là 1.',
            'times.max' => 'Số lần tối đa là 100.',
            'note.max' => 'Ghi chú không được vượt quá 500 ký tự.',
        ]);

        $seller = Auth::user();
        $service = Service::findOrFail($request->service_id);

        try {
            $featuredHistory = $this->featuredService->featureService(
                $seller,
                $service,
                $request->times,
                $request->note
            );

            return response()->json([
                'success' => true,
                'message' => 'Đề xuất dịch vụ thành công! Dịch vụ sẽ được hiển thị ưu tiên trong ' . $featuredHistory->hours . ' giờ.',
                'data' => [
                    'featured_until' => $featuredHistory->featured_until->format('d/m/Y H:i'),
                    'amount' => $featuredHistory->formatted_amount,
                ]
            ]);

        } catch (\DomainException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);

        } catch (\Exception $e) {
            \Log::error('Feature service error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra. Vui lòng thử lại sau.'
            ], 500);
        }
    }

    public function show($slug)
    {
        $seller = Auth::user();
        
        $featuredHistory = \App\Models\FeaturedHistory::with(['featurable'])
            ->where('slug', $slug)
            ->where('seller_id', $seller->id)
            ->firstOrFail();

        return view('seller.pages.featured.show', compact('featuredHistory'));
    }
}
