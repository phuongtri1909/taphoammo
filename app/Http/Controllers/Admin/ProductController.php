<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use App\Enums\ProductStatus;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    /**
     * Trang duyệt sản phẩm (pending products)
     */
    public function pending()
    {
        $products = Product::with(['subCategory.category', 'seller', 'variants'])
            ->where('status', ProductStatus::PENDING)
            ->latest()
            ->paginate(20);

        app()->instance('pending_products_count', $products->total());

        return view('admin.pages.products.pending', compact('products'));
    }

    /**
     * Xem chi tiết sản phẩm để duyệt
     */
    public function review(Product $product)
    {
        $product->load(['subCategory.category', 'seller', 'variants']);

        return view('admin.pages.products.review', compact('product'));
    }

    /**
     * Duyệt sản phẩm
     */
    public function approve(Request $request, Product $product)
    {
        if ($product->status !== ProductStatus::PENDING) {
            return redirect()->back()->with('error', 'Sản phẩm không ở trạng thái chờ duyệt!');
        }

        $product->update([
            'status' => ProductStatus::APPROVED,
            'admin_note' => $request->admin_note,
        ]);

        return redirect()->route('admin.products.pending')
            ->with('success', 'Đã duyệt sản phẩm thành công!');
    }

    /**
     * Từ chối sản phẩm
     */
    public function reject(Request $request, Product $product)
    {
        $request->validate([
            'admin_note' => 'required|string|max:1000',
        ], [
            'admin_note.required' => 'Vui lòng nhập lý do từ chối.',
        ]);

        if ($product->status !== ProductStatus::PENDING) {
            return redirect()->back()->with('error', 'Sản phẩm không ở trạng thái chờ duyệt!');
        }

        $product->update([
            'status' => ProductStatus::REJECTED,
            'admin_note' => $request->admin_note,
        ]);

        return redirect()->route('admin.products.pending')
            ->with('success', 'Đã từ chối sản phẩm!');
    }

    /**
     * Quản lý tất cả sản phẩm
     */
    public function index(Request $request)
    {
        $query = Product::with(['subCategory.category', 'seller', 'variants']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('category_id')) {
            $query->whereHas('subCategory', function ($q) use ($request) {
                $q->where('category_id', $request->category_id);
            });
        }

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        $products = $query->latest()->paginate(20);
        $categories = Category::active()->ordered()->get();
        $statuses = ProductStatus::cases();

        return view('admin.pages.products.index', compact('products', 'categories', 'statuses'));
    }

    /**
     * Xem chi tiết sản phẩm
     */
    public function show(Product $product)
    {
        $product->load(['subCategory.category', 'seller', 'variants.productValues']);

        return view('admin.pages.products.show', compact('product'));
    }

    /**
     * Cấm sản phẩm
     */
    public function ban(Request $request, Product $product)
    {
        $request->validate([
            'admin_note' => 'required|string|max:1000',
        ], [
            'admin_note.required' => 'Vui lòng nhập lý do cấm.',
        ]);

        if (!in_array($product->status, [ProductStatus::APPROVED, ProductStatus::HIDDEN])) {
            return redirect()->back()->with('error', 'Không thể cấm sản phẩm ở trạng thái này!');
        }

        $product->update([
            'status' => ProductStatus::BANNED,
            'admin_note' => $request->admin_note,
        ]);

        return redirect()->back()->with('success', 'Đã cấm sản phẩm!');
    }

    /**
     * Bỏ cấm sản phẩm (chuyển về pending để duyệt lại)
     */
    public function unban(Product $product)
    {
        if ($product->status !== ProductStatus::BANNED) {
            return redirect()->back()->with('error', 'Sản phẩm không ở trạng thái bị cấm!');
        }

        $product->update([
            'status' => ProductStatus::PENDING,
            'admin_note' => null,
        ]);

        return redirect()->back()->with('success', 'Đã bỏ cấm sản phẩm, sản phẩm chuyển về trạng thái chờ duyệt!');
    }
}

