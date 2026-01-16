<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\SubCategory;
use App\Enums\CommonStatus;
use Illuminate\Http\Request;

class SubCategoryController extends Controller
{
    public function index()
    {
        $subCategories = SubCategory::with('category')
            ->withCount('products')
            ->ordered()
            ->paginate(30);

        $categories = Category::active()->ordered()->get();

        return view('admin.pages.subcategories.index', compact('subCategories', 'categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'category_id' => 'required|exists:categories,id',
            'name' => 'required|string|max:255',
            'field_name' => 'nullable|string|max:255',
            'description' => 'nullable|string|max:1000',
            'order' => 'nullable|integer|min:0',
            'status' => 'required|in:active,inactive',
        ], [
            'category_id.required' => 'Vui lòng chọn danh mục cha.',
            'category_id.exists' => 'Danh mục cha không tồn tại.',
            'name.required' => 'Tên danh mục con là bắt buộc.',
            'name.max' => 'Tên danh mục con không được vượt quá 255 ký tự.',
            'description.max' => 'Mô tả không được vượt quá 1000 ký tự.',
        ]);

        $exists = SubCategory::where('category_id', $request->category_id)
            ->where('name', $request->name)
            ->exists();
        
        if ($exists) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Tên danh mục con đã tồn tại trong danh mục cha này!');
        }

        SubCategory::create([
            'category_id' => $request->category_id,
            'name' => $request->name,
            'field_name' => $request->field_name,
            'description' => $request->description,
            'order' => $request->order ?? 0,
            'status' => $request->status,
        ]);

        return redirect()->route('admin.subcategories.index')
            ->with('success', 'Đã thêm danh mục con mới thành công!');
    }

    public function update(Request $request, SubCategory $subcategory)
    {
        $request->validate([
            'category_id' => 'required|exists:categories,id',
            'name' => 'required|string|max:255',
            'field_name' => 'nullable|string|max:255',
            'description' => 'nullable|string|max:1000',
            'order' => 'nullable|integer|min:0',
            'status' => 'required|in:active,inactive',
        ], [
            'category_id.required' => 'Vui lòng chọn danh mục cha.',
            'category_id.exists' => 'Danh mục cha không tồn tại.',
            'name.required' => 'Tên danh mục con là bắt buộc.',
            'name.max' => 'Tên danh mục con không được vượt quá 255 ký tự.',
            'description.max' => 'Mô tả không được vượt quá 1000 ký tự.',
        ]);

        $exists = SubCategory::where('category_id', $request->category_id)
            ->where('name', $request->name)
            ->where('id', '!=', $subcategory->id)
            ->exists();
        
        if ($exists) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Tên danh mục con đã tồn tại trong danh mục cha này!');
        }

        $subcategory->update([
            'category_id' => $request->category_id,
            'name' => $request->name,
            'field_name' => $request->field_name,
            'description' => $request->description,
            'order' => $request->order ?? 0,
            'status' => $request->status,
        ]);

        return redirect()->route('admin.subcategories.index')
            ->with('success', 'Đã cập nhật danh mục con thành công!');
    }

    public function destroy(SubCategory $subcategory)
    {
        if ($subcategory->products()->count() > 0) {
            return redirect()->route('admin.subcategories.index')
                ->with('error', 'Không thể xóa danh mục con có chứa sản phẩm!');
        }

        $subcategory->delete();

        return redirect()->route('admin.subcategories.index')
            ->with('success', 'Đã xóa danh mục con thành công!');
    }
}

