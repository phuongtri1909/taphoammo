<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ServiceCategory;
use App\Models\ServiceSubCategory;
use App\Enums\CommonStatus;
use Illuminate\Http\Request;

class ServiceSubCategoryController extends Controller
{
    public function index()
    {
        $subCategories = ServiceSubCategory::with('serviceCategory')
            ->withCount('services')
            ->ordered()
            ->paginate(30);

        $categories = ServiceCategory::active()->ordered()->get();

        return view('admin.pages.service-subcategories.index', compact('subCategories', 'categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'service_category_id' => 'required|exists:service_categories,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'order' => 'nullable|integer|min:0',
            'status' => 'required|in:active,inactive',
        ], [
            'service_category_id.required' => 'Vui lòng chọn danh mục cha.',
            'service_category_id.exists' => 'Danh mục cha không tồn tại.',
            'name.required' => 'Tên danh mục con là bắt buộc.',
            'name.max' => 'Tên danh mục con không được vượt quá 255 ký tự.',
            'description.max' => 'Mô tả không được vượt quá 1000 ký tự.',
        ]);

        $exists = ServiceSubCategory::where('service_category_id', $request->service_category_id)
            ->where('name', $request->name)
            ->exists();
        
        if ($exists) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Tên danh mục con đã tồn tại trong danh mục cha này!');
        }

        ServiceSubCategory::create([
            'service_category_id' => $request->service_category_id,
            'name' => $request->name,
            'description' => $request->description,
            'order' => $request->order ?? 0,
            'status' => $request->status,
        ]);

        return redirect()->route('admin.service-subcategories.index')
            ->with('success', 'Đã thêm danh mục con dịch vụ mới thành công!');
    }

    public function update(Request $request, ServiceSubCategory $serviceSubcategory)
    {
        $request->validate([
            'service_category_id' => 'required|exists:service_categories,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'order' => 'nullable|integer|min:0',
            'status' => 'required|in:active,inactive',
        ], [
            'service_category_id.required' => 'Vui lòng chọn danh mục cha.',
            'service_category_id.exists' => 'Danh mục cha không tồn tại.',
            'name.required' => 'Tên danh mục con là bắt buộc.',
            'name.max' => 'Tên danh mục con không được vượt quá 255 ký tự.',
            'description.max' => 'Mô tả không được vượt quá 1000 ký tự.',
        ]);

        $exists = ServiceSubCategory::where('service_category_id', $request->service_category_id)
            ->where('name', $request->name)
            ->where('id', '!=', $serviceSubcategory->id)
            ->exists();
        
        if ($exists) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Tên danh mục con đã tồn tại trong danh mục cha này!');
        }

        $serviceSubcategory->update([
            'service_category_id' => $request->service_category_id,
            'name' => $request->name,
            'description' => $request->description,
            'order' => $request->order ?? 0,
            'status' => $request->status,
        ]);

        return redirect()->route('admin.service-subcategories.index')
            ->with('success', 'Đã cập nhật danh mục con dịch vụ thành công!');
    }

    public function destroy(ServiceSubCategory $serviceSubcategory)
    {
        if ($serviceSubcategory->services()->count() > 0) {
            return redirect()->route('admin.service-subcategories.index')
                ->with('error', 'Không thể xóa danh mục con có chứa dịch vụ!');
        }

        $serviceSubcategory->delete();

        return redirect()->route('admin.service-subcategories.index')
            ->with('success', 'Đã xóa danh mục con dịch vụ thành công!');
    }
}
