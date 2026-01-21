<?php

namespace App\Http\Controllers\Seller;

use App\Models\Service;
use App\Models\ServiceCategory;
use App\Enums\CommonStatus;
use App\Models\ServiceSubCategory;
use App\Enums\ServiceStatus;
use App\Helpers\ImageHelper;
use Illuminate\Http\Request;
use App\Models\ServiceVariant;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class ServiceController extends Controller
{
    public function index(Request $request)
    {
        $query = Service::with(['serviceSubCategory.serviceCategory', 'variants'])
            ->where('seller_id', Auth::id());

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $services = $query->latest()->paginate(20);
        $statuses = ServiceStatus::cases();

        return view('seller.pages.services.index', compact('services', 'statuses'));
    }

    public function create()
    {
        $categories = ServiceCategory::with(['serviceSubCategories' => function ($q) {
            $q->active()->ordered();
        }])->active()->ordered()->get();

        return view('seller.pages.services.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'service_sub_category_id' => 'required|exists:service_sub_categories,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'long_description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,jpg,png,webp|max:5120',
            'variants' => 'required|array|min:1',
            'variants.*.name' => 'required|string|max:255',
            'variants.*.price' => 'required|numeric|min:0',
        ], [
            'service_sub_category_id.required' => 'Vui lòng chọn danh mục.',
            'name.required' => 'Tên dịch vụ là bắt buộc.',
            'variants.required' => 'Phải có ít nhất 1 biến thể dịch vụ.',
            'variants.min' => 'Phải có ít nhất 1 biến thể dịch vụ.',
            'variants.*.name.required' => 'Tên biến thể là bắt buộc.',
            'variants.*.price.required' => 'Giá biến thể là bắt buộc.',
        ]);

        DB::beginTransaction();
        try {
            $imagePath = null;
            if ($request->hasFile('image')) {
                $imagePath = ImageHelper::optimizeAndSave($request->file('image'), 'services', null, 85, true);
            }

            $service = Service::create([
                'service_sub_category_id' => $request->service_sub_category_id,
                'seller_id' => Auth::id(),
                'name' => $request->name,
                'image' => $imagePath,
                'description' => $request->description,
                'long_description' => $request->long_description,
                'status' => ServiceStatus::PENDING,
            ]);

            foreach ($request->variants as $index => $variantData) {
                ServiceVariant::create([
                    'service_id' => $service->id,
                    'name' => $variantData['name'],
                    'price' => $variantData['price'],
                    'order' => $index,
                    'status' => CommonStatus::INACTIVE,
                ]);
            }

            DB::commit();

            return redirect()->route('seller.services.index')
                ->with('success', 'Đã tạo dịch vụ thành công! Vui lòng chờ admin duyệt.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($e->getMessage());
            return redirect()->back()
                ->withInput()
                ->with('error', 'Có lỗi xảy ra, vui lòng thử lại sau.');
        }
    }

    public function show(Service $service)
    {
        if ($service->seller_id !== Auth::id()) {
            abort(403);
        }

        $service->load(['serviceSubCategory.serviceCategory', 'variants']);

        return view('seller.pages.services.show', compact('service'));
    }

    public function edit(Service $service)
    {
        if ($service->seller_id !== Auth::id()) {
            abort(403);
        }

        if (!in_array($service->status, [ServiceStatus::PENDING, ServiceStatus::REJECTED])) {
            return redirect()->route('seller.services.show', $service)
                ->with('error', 'Không thể chỉnh sửa dịch vụ đã được duyệt!');
        }

        $service->load('variants');

        $categories = ServiceCategory::with(['serviceSubCategories' => function ($q) {
            $q->active()->ordered();
        }])->active()->ordered()->get();

        return view('seller.pages.services.edit', compact('service', 'categories'));
    }

    public function update(Request $request, Service $service)
    {
        if ($service->seller_id !== Auth::id()) {
            abort(403);
        }

        if (!in_array($service->status, [ServiceStatus::PENDING, ServiceStatus::REJECTED])) {
            return redirect()->route('seller.services.show', $service)
                ->with('error', 'Không thể chỉnh sửa dịch vụ đã được duyệt!');
        }

        $request->validate([
            'service_sub_category_id' => 'required|exists:service_sub_categories,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'long_description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,jpg,png,webp|max:5120',
            'variants' => 'required|array|min:1',
            'variants.*.id' => 'nullable|exists:service_variants,id',
            'variants.*.name' => 'required|string|max:255',
            'variants.*.price' => 'required|numeric|min:0',
        ], [
            'service_sub_category_id.required' => 'Vui lòng chọn danh mục.',
            'name.required' => 'Tên dịch vụ là bắt buộc.',
            'variants.required' => 'Phải có ít nhất 1 biến thể dịch vụ.',
            'variants.min' => 'Phải có ít nhất 1 biến thể dịch vụ.',
            'variants.*.name.required' => 'Tên biến thể là bắt buộc.',
            'variants.*.price.required' => 'Giá biến thể là bắt buộc.',
        ]);

        DB::beginTransaction();
        try {
            $imagePath = $service->image;
            if ($request->hasFile('image')) {
                if ($service->image) {
                    ImageHelper::delete($service->image);
                }
                $imagePath = ImageHelper::optimizeAndSave($request->file('image'), 'services', null, 85, true);
            }

            $service->update([
                'service_sub_category_id' => $request->service_sub_category_id,
                'name' => $request->name,
                'image' => $imagePath,
                'description' => $request->description,
                'long_description' => $request->long_description,
                'status' => ServiceStatus::PENDING,
                'admin_note' => null,
            ]);

            $existingVariantIds = $service->variants->pluck('id')->toArray();
            $updatedVariantIds = [];

            foreach ($request->variants as $index => $variantData) {
                if (!empty($variantData['id'])) {
                    $variant = ServiceVariant::find($variantData['id']);
                    if ($variant && $variant->service_id === $service->id) {
                        $variant->update([
                            'name' => $variantData['name'],
                            'price' => $variantData['price'],
                            'order' => $index,
                        ]);
                        $updatedVariantIds[] = $variant->id;
                    }
                } else {
                    $newVariant = ServiceVariant::create([
                        'service_id' => $service->id,
                        'name' => $variantData['name'],
                        'price' => $variantData['price'],
                        'order' => $index,
                        'status' => CommonStatus::INACTIVE,
                    ]);
                    $updatedVariantIds[] = $newVariant->id;
                }
            }

            $variantsToDelete = array_diff($existingVariantIds, $updatedVariantIds);
            foreach ($variantsToDelete as $variantId) {
                $variant = ServiceVariant::find($variantId);
                if ($variant && $variant->serviceOrders()->count() === 0) {
                    $variant->delete();
                }
            }

            DB::commit();

            return redirect()->route('seller.services.index')
                ->with('success', 'Đã cập nhật dịch vụ! Vui lòng chờ admin duyệt lại.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($e->getMessage());
            return redirect()->back()
                ->withInput()
                ->with('error', 'Có lỗi xảy ra, vui lòng thử lại sau.');
        }
    }

    public function updateStatus(Request $request, Service $service)
    {
        if ($service->seller_id !== Auth::id()) {
            abort(403);
        }

        if (!in_array($service->status, [ServiceStatus::APPROVED, ServiceStatus::HIDDEN])) {
            return redirect()->back()->with('error', 'Không thể thay đổi trạng thái dịch vụ này!');
        }

        $request->validate([
            'status' => 'required|in:approved,hidden',
        ]);

        $newStatus = $request->status === 'hidden' ? ServiceStatus::HIDDEN : ServiceStatus::APPROVED;
        
        $service->update(['status' => $newStatus]);

        return redirect()->back()->with('success', 'Đã cập nhật trạng thái dịch vụ!');
    }

    public function updateVariantStatus(Request $request, ServiceVariant $variant)
    {
        $service = $variant->service;

        if ($service->seller_id !== Auth::id()) {
            abort(403);
        }

        if ($service->status !== ServiceStatus::APPROVED) {
            return redirect()->back()->with('error', 'Chỉ có thể thay đổi trạng thái biến thể khi dịch vụ đã được duyệt!');
        }

        $request->validate([
            'status' => 'required|in:active,inactive',
        ]);

        $newStatus = $request->status === 'active' ? CommonStatus::ACTIVE : CommonStatus::INACTIVE;
        
        $variant->update(['status' => $newStatus]);

        return redirect()->back()->with('success', 'Đã cập nhật trạng thái biến thể!');
    }

    public function updateVariantPrice(Request $request, ServiceVariant $variant)
    {
        $service = $variant->service;

        if ($service->seller_id !== Auth::id()) {
            abort(403);
        }

        if (!in_array($service->status, [ServiceStatus::APPROVED, ServiceStatus::HIDDEN])) {
            return redirect()->back()->with('error', 'Chỉ có thể chỉnh sửa giá khi dịch vụ đã được duyệt!');
        }

        $request->validate([
            'price' => 'required|numeric|min:0',
        ]);

        $variant->update(['price' => $request->price]);

        return redirect()->back()->with('success', 'Đã cập nhật giá biến thể!');
    }

    public function updateImage(Request $request, Service $service)
    {
        if ($service->seller_id !== Auth::id()) {
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
            if ($service->image) {
                ImageHelper::delete($service->image);
            }

            $imagePath = ImageHelper::optimizeAndSave($request->file('image'), 'services', null, 85, true);
            $service->update(['image' => $imagePath]);

            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Đã cập nhật ảnh dịch vụ thành công!',
                    'image_url' => \Illuminate\Support\Facades\Storage::url($imagePath)
                ]);
            }

            return redirect()->back()->with('success', 'Đã cập nhật ảnh dịch vụ thành công!');
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

    public function destroy(Service $service)
    {
        if ($service->seller_id !== Auth::id()) {
            abort(403);
        }

        $hasOrders = DB::table('service_orders')
            ->join('service_variants', 'service_orders.service_variant_id', '=', 'service_variants.id')
            ->where('service_variants.service_id', $service->id)
            ->exists();

        if ($hasOrders) {
            return redirect()->back()->with('error', 'Không thể xóa dịch vụ đã có đơn hàng!');
        }

        DB::beginTransaction();
        try {
            if ($service->image) {
                ImageHelper::delete($service->image);
            }

            $service->variants()->delete();
            $service->delete();

            DB::commit();

            return redirect()->route('seller.services.index')
                ->with('success', 'Đã xóa dịch vụ thành công!');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($e->getMessage());
            return redirect()->back()
                ->with('error', 'Có lỗi xảy ra, vui lòng thử lại sau.');
        }
    }
}
