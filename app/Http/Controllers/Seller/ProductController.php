<?php

namespace App\Http\Controllers\Seller;

use App\Models\Product;
use App\Models\Category;
use App\Enums\CommonStatus;
use App\Models\SubCategory;
use App\Enums\ProductStatus;
use App\Helpers\ImageHelper;
use App\Models\ProductValue;
use Illuminate\Http\Request;
use App\Models\ProductVariant;
use App\Enums\ProductValueStatus;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::with(['subCategory.category', 'variants'])
            ->where('seller_id', Auth::id());

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $products = $query->latest()->paginate(20);
        $statuses = ProductStatus::cases();

        return view('seller.pages.products.index', compact('products', 'statuses'));
    }

    public function create()
    {
        $categories = Category::with(['subCategories' => function ($q) {
            $q->active()->ordered();
        }])->active()->ordered()->get();

        return view('seller.pages.products.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'sub_category_id' => 'required|exists:sub_categories,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'long_description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,jpg,png,webp|max:5120',
            'variants' => 'required|array|min:1',
            'variants.*.name' => 'required|string|max:255',
            'variants.*.price' => 'required|numeric|min:0',
            'variants.*.field_name' => 'required|string|max:255',
        ], [
            'sub_category_id.required' => 'Vui lòng chọn danh mục.',
            'name.required' => 'Tên sản phẩm là bắt buộc.',
            'variants.required' => 'Phải có ít nhất 1 biến thể sản phẩm.',
            'variants.min' => 'Phải có ít nhất 1 biến thể sản phẩm.',
            'variants.*.name.required' => 'Tên biến thể là bắt buộc.',
            'variants.*.price.required' => 'Giá biến thể là bắt buộc.',
            'variants.*.field_name.required' => 'Tên trường dữ liệu là bắt buộc.',
        ]);

        DB::beginTransaction();
        try {
            $imagePath = null;
            if ($request->hasFile('image')) {
                $imagePath = ImageHelper::optimizeAndSave($request->file('image'), 'products', null, 85, true);
            }

            $product = Product::create([
                'sub_category_id' => $request->sub_category_id,
                'seller_id' => Auth::id(),
                'name' => $request->name,
                'image' => $imagePath,
                'description' => $request->description,
                'long_description' => $request->long_description,
                'status' => ProductStatus::PENDING,
            ]);

            foreach ($request->variants as $index => $variantData) {
                ProductVariant::create([
                    'product_id' => $product->id,
                    'name' => $variantData['name'],
                    'price' => $variantData['price'],
                    'field_name' => $variantData['field_name'],
                    'order' => $index,
                    'status' => CommonStatus::INACTIVE,
                ]);
            }

            DB::commit();

            return redirect()->route('seller.products.index')
                ->with('success', 'Đã tạo sản phẩm thành công! Vui lòng chờ admin duyệt.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($e->getMessage());
            return redirect()->back()
                ->withInput()
                ->with('error', 'Có lỗi xảy ra, vui lòng thử lại sau.');
        }
    }

    public function show(Product $product)
    {
        if ($product->seller_id !== Auth::id()) {
            abort(403);
        }

        $product->load(['subCategory.category', 'variants.productValues']);

        return view('seller.pages.products.show', compact('product'));
    }

    public function edit(Product $product)
    {
        if ($product->seller_id !== Auth::id()) {
            abort(403);
        }

        if (!in_array($product->status, [ProductStatus::PENDING, ProductStatus::REJECTED])) {
            return redirect()->route('seller.products.show', $product)
                ->with('error', 'Không thể chỉnh sửa sản phẩm đã được duyệt!');
        }

        $product->load('variants');

        $categories = Category::with(['subCategories' => function ($q) {
            $q->active()->ordered();
        }])->active()->ordered()->get();

        return view('seller.pages.products.edit', compact('product', 'categories'));
    }

    public function update(Request $request, Product $product)
    {
        if ($product->seller_id !== Auth::id()) {
            abort(403);
        }

        if (!in_array($product->status, [ProductStatus::PENDING, ProductStatus::REJECTED])) {
            return redirect()->route('seller.products.show', $product)
                ->with('error', 'Không thể chỉnh sửa sản phẩm đã được duyệt!');
        }

        $request->validate([
            'sub_category_id' => 'required|exists:sub_categories,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'long_description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,jpg,png,webp|max:5120',
            'variants' => 'required|array|min:1',
            'variants.*.id' => 'nullable|exists:product_variants,id',
            'variants.*.name' => 'required|string|max:255',
            'variants.*.price' => 'required|numeric|min:0',
            'variants.*.field_name' => 'required|string|max:255',
        ]);

        DB::beginTransaction();
        try {
            $imagePath = $product->image;
            if ($request->hasFile('image')) {
                if ($product->image) {
                    ImageHelper::delete($product->image);
                }
                $imagePath = ImageHelper::optimizeAndSave($request->file('image'), 'products', null, 85, true);
            }

            $product->update([
                'sub_category_id' => $request->sub_category_id,
                'name' => $request->name,
                'image' => $imagePath,
                'description' => $request->description,
                'long_description' => $request->long_description,
                'status' => ProductStatus::PENDING,
                'admin_note' => null,
            ]);

            $existingVariantIds = $product->variants->pluck('id')->toArray();
            $updatedVariantIds = [];

            foreach ($request->variants as $index => $variantData) {
                if (!empty($variantData['id'])) {
                    $variant = ProductVariant::find($variantData['id']);
                    if ($variant && $variant->product_id === $product->id) {
                        $variant->update([
                            'name' => $variantData['name'],
                            'price' => $variantData['price'],
                            'field_name' => $variantData['field_name'],
                            'order' => $index,
                        ]);
                        $updatedVariantIds[] = $variant->id;
                    }
                } else {
                    $newVariant = ProductVariant::create([
                        'product_id' => $product->id,
                        'name' => $variantData['name'],
                        'price' => $variantData['price'],
                        'field_name' => $variantData['field_name'],
                        'order' => $index,
                        'status' => CommonStatus::INACTIVE,
                    ]);
                    $updatedVariantIds[] = $newVariant->id;
                }
            }

            $variantsToDelete = array_diff($existingVariantIds, $updatedVariantIds);
            foreach ($variantsToDelete as $variantId) {
                $variant = ProductVariant::find($variantId);
                if ($variant && $variant->productValues()->count() === 0) {
                    $variant->delete();
                }
            }

            DB::commit();

            return redirect()->route('seller.products.index')
                ->with('success', 'Đã cập nhật sản phẩm! Vui lòng chờ admin duyệt lại.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($e->getMessage());
            return redirect()->back()
                ->withInput()
                ->with('error', 'Có lỗi xảy ra, vui lòng thử lại sau.');
        }
    }

    public function updateStatus(Request $request, Product $product)
    {
        if ($product->seller_id !== Auth::id()) {
            abort(403);
        }

        if (!in_array($product->status, [ProductStatus::APPROVED, ProductStatus::HIDDEN])) {
            return redirect()->back()->with('error', 'Không thể thay đổi trạng thái sản phẩm này!');
        }

        $request->validate([
            'status' => 'required|in:approved,hidden',
        ]);

        $newStatus = $request->status === 'hidden' ? ProductStatus::HIDDEN : ProductStatus::APPROVED;
        
        $product->update(['status' => $newStatus]);

        return redirect()->back()->with('success', 'Đã cập nhật trạng thái sản phẩm!');
    }

    public function updateVariantStatus(Request $request, ProductVariant $variant)
    {
        $product = $variant->product;

        if ($product->seller_id !== Auth::id()) {
            abort(403);
        }

        if ($product->status !== ProductStatus::APPROVED) {
            return redirect()->back()->with('error', 'Chỉ có thể thay đổi trạng thái biến thể khi sản phẩm đã được duyệt!');
        }

        $request->validate([
            'status' => 'required|in:active,inactive',
        ]);

        $newStatus = $request->status === 'active' ? CommonStatus::ACTIVE : CommonStatus::INACTIVE;
        
        $variant->update(['status' => $newStatus]);

        return redirect()->back()->with('success', 'Đã cập nhật trạng thái biến thể!');
    }

    public function updateVariantPrice(Request $request, ProductVariant $variant)
    {
        $product = $variant->product;

        if ($product->seller_id !== Auth::id()) {
            abort(403);
        }

        if (!in_array($product->status, [ProductStatus::APPROVED, ProductStatus::HIDDEN])) {
            return redirect()->back()->with('error', 'Chỉ có thể chỉnh sửa biến thể khi sản phẩm đã được duyệt!');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
        ], [
            'name.required' => 'Tên biến thể là bắt buộc.',
            'name.max' => 'Tên biến thể không được vượt quá 255 ký tự.',
            'price.required' => 'Giá là bắt buộc.',
            'price.numeric' => 'Giá phải là số.',
            'price.min' => 'Giá phải lớn hơn hoặc bằng 0.',
        ]);

        $variant->update([
            'name' => $request->name,
            'price' => $request->price,
        ]);

        return redirect()->back()->with('success', 'Đã cập nhật biến thể!');
    }

    public function storeValues(Request $request, ProductVariant $variant)
    {
        $product = $variant->product;

        if ($product->seller_id !== Auth::id()) {
            abort(403);
        }

        if ($product->status !== ProductStatus::APPROVED) {
            return redirect()->back()->with('error', 'Chỉ có thể thêm giá trị khi sản phẩm đã được duyệt!');
        }

        $request->validate([
            'values' => 'required|string',
        ], [
            'values.required' => 'Vui lòng nhập giá trị sản phẩm.',
        ]);

        DB::beginTransaction();
        try {
            $lines = array_filter(array_map('trim', explode("\n", $request->values)));
            $addedCount = 0;

            foreach ($lines as $line) {
                if (empty($line)) continue;

                $encryptedData = Crypt::encryptString(json_encode(['value' => $line]));
                
                ProductValue::create([
                    'product_variant_id' => $variant->id,
                    'encrypted_data' => $encryptedData,
                    'status' => ProductValueStatus::AVAILABLE,
                ]);

                $addedCount++;
            }

            $variant->increment('stock_quantity', $addedCount);

            DB::commit();

            return redirect()->back()->with('success', "Đã thêm {$addedCount} giá trị sản phẩm!");

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($e->getMessage());
            return redirect()->back()->with('error', 'Có lỗi xảy ra, vui lòng thử lại sau.');
        }
    }

    public function destroyValue(ProductValue $value)
    {
        $this->authorize('delete', $value);

        $variant = $value->productVariant;

        if ($value->status == ProductValueStatus::SOLD->value) {
            return redirect()->back()->with('error', 'Không thể xóa giá trị sản phẩm đã bán!');
        }

        if ($value->status == ProductValueStatus::REFUNDED->value) {
            return redirect()->back()->with('error', 'Không thể xóa giá trị sản phẩm đã hoàn tiền!');
        }

        if ($value->status == ProductValueStatus::INVALID->value) {
            return redirect()->back()->with('error', 'Không thể xóa giá trị sản phẩm đã không hợp lệ!');
        }

        $value->delete();
        $variant->decrement('stock_quantity');

        return redirect()->back()->with('success', 'Đã xóa giá trị sản phẩm!');
    }

    public function updateValue(Request $request, ProductValue $value)
    {
        $this->authorize('update', $value);

        $request->validate([
            'value' => 'required|string',
        ], [
            'value.required' => 'Vui lòng nhập giá trị sản phẩm.',
        ]);

        if ($value->status == ProductValueStatus::SOLD->value ) {
            return redirect()->back()->with('error', 'Không thể cập nhật giá trị sản phẩm đã bán!');
        }

        if ($value->status == ProductValueStatus::REFUNDED->value) {
            return redirect()->back()->with('error', 'Không thể cập nhật giá trị sản phẩm đã hoàn tiền!');
        }

        if ($value->status == ProductValueStatus::INVALID->value) {
            return redirect()->back()->with('error', 'Không thể cập nhật giá trị sản phẩm đã không hợp lệ!');
        }

        $encryptedData = Crypt::encryptString(json_encode(['value' => $request->value]));
        $value->update(['encrypted_data' => $encryptedData]);

        return redirect()->back()->with('success', 'Đã cập nhật giá trị sản phẩm!');
    }

    public function updateImage(Request $request, Product $product)
    {
        if ($product->seller_id !== Auth::id()) {
            abort(403);
        }

        $request->validate([
            'image' => 'required|image|mimes:jpeg,jpg,png,webp|max:5120',
        ], [
            'image.required' => 'Vui lòng chọn ảnh.',
            'image.image' => 'File phải là ảnh.',
            'image.mimes' => 'Ảnh phải là định dạng: jpeg, jpg, png, webp.',
            'image.max' => 'Kích thước ảnh tối đa 5MB.',
        ]);

        try {
            if ($product->image) {
                ImageHelper::delete($product->image);
            }

            $imagePath = ImageHelper::optimizeAndSave($request->file('image'), 'products', null, 85, true);
            $product->update(['image' => $imagePath]);

            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Đã cập nhật ảnh sản phẩm thành công!',
                    'image_url' => Storage::url($imagePath)
                ]);
            }

            return redirect()->back()->with('success', 'Đã cập nhật ảnh sản phẩm thành công!');
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Có lỗi xảy ra, vui lòng thử lại sau.'
                ], 500);
            }
            
            return redirect()->back()->with('error', 'Có lỗi xảy ra, vui lòng thử lại sau.');
        }
    }

    public function destroy(Product $product)
    {
        if ($product->seller_id !== Auth::id()) {
            abort(403);
        }

        $hasOrders = DB::table('order_items')
            ->join('product_variants', 'order_items.product_variant_id', '=', 'product_variants.id')
            ->where('product_variants.product_id', $product->id)
            ->exists();

        if ($hasOrders) {
            return redirect()->back()->with('error', 'Không thể xóa sản phẩm đã có đơn hàng!');
        }

        DB::beginTransaction();
        try {
            if ($product->image) {
                ImageHelper::delete($product->image);
            }

            $product->delete();

            DB::commit();

            return redirect()->route('seller.products.index')
                ->with('success', 'Đã xóa sản phẩm thành công!');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($e->getMessage());
            return redirect()->back()->with('error', 'Có lỗi xảy ra, vui lòng thử lại sau.');
        }
    }
}

