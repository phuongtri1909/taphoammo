<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ShareCategory;
use Illuminate\Http\Request;

class ShareCategoryController extends Controller
{

    public function index(Request $request)
    {
        $query = ShareCategory::withCount('shares');

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        $categories = $query->ordered()->paginate(15);

        return view('admin.pages.share-categories.index', compact('categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:500',
            'order' => 'nullable|integer|min:0',
            'is_active' => 'boolean',
        ], [
            'name.required' => 'Tên danh mục là bắt buộc.',
            'name.max' => 'Tên danh mục không được vượt quá 255 ký tự.',
            'description.max' => 'Mô tả không được vượt quá 500 ký tự.',
            'order.integer' => 'Thứ tự phải là số nguyên.',
            'order.min' => 'Thứ tự phải lớn hơn 0.',
            'is_active.boolean' => 'Trạng thái không hợp lệ.',
        ]);

        ShareCategory::create([
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'order' => $validated['order'] ?? 0,
            'is_active' => $request->boolean('is_active', true),
        ]);

        return redirect()->route('admin.share-categories.index')->with('success', 'Danh mục đã được tạo thành công!');
    }

    public function update(Request $request, ShareCategory $shareCategory)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:500',
            'order' => 'nullable|integer|min:0',
            'is_active' => 'boolean',
        ], [
            'name.required' => 'Tên danh mục là bắt buộc.',
            'name.max' => 'Tên danh mục không được vượt quá 255 ký tự.',
            'description.max' => 'Mô tả không được vượt quá 500 ký tự.',
            'order.integer' => 'Thứ tự phải là số nguyên.',
            'order.min' => 'Thứ tự phải lớn hơn 0.',
            'is_active.boolean' => 'Trạng thái không hợp lệ.',
        ]);

        $shareCategory->update([
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'order' => $validated['order'] ?? 0,
            'is_active' => $request->boolean('is_active', true),
        ]);

        return redirect()->route('admin.share-categories.index')->with('success', 'Danh mục đã được cập nhật!');
    }

    public function destroy(ShareCategory $shareCategory)
    {
        if ($shareCategory->shares()->count() > 0) {
            return redirect()->route('admin.share-categories.index')
                ->with('error', 'Không thể xóa danh mục có bài viết.');
        }

        $shareCategory->delete();

        return redirect()->route('admin.share-categories.index')->with('success', 'Danh mục đã được xóa!');
    }
}
