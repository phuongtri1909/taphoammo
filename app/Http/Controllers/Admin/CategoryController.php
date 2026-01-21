<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Enums\CommonStatus;
use Illuminate\Http\Request;
use App\Helpers\ImageHelper;
use Illuminate\Support\Facades\Storage;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::withCount('subCategories')
            ->ordered()
            ->get();

        return view('admin.pages.categories.index', compact('categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:categories,name',
            'description' => 'nullable|string|max:1000',
            'order' => 'nullable|integer|min:0',
            'status' => 'required|in:active,inactive',
            'icon_file' => 'nullable|image|mimes:jpeg,jpg,png,gif,webp,svg|max:5120',
            'icon_svg' => 'nullable|string|max:5000',
        ], [
            'name.required' => 'Tên danh mục là bắt buộc.',
            'name.unique' => 'Tên danh mục đã tồn tại.',
            'name.max' => 'Tên danh mục không được vượt quá 255 ký tự.',
            'description.max' => 'Mô tả không được vượt quá 1000 ký tự.',
            'icon_file.image' => 'File phải là hình ảnh.',
            'icon_file.mimes' => 'Chỉ chấp nhận file ảnh (JPG, PNG, GIF, WEBP, SVG).',
            'icon_file.max' => 'Kích thước file tối đa 5MB.',
            'icon_svg.max' => 'Mã SVG không được vượt quá 5000 ký tự.',
        ]);

        $icon = null;

        if ($request->hasFile('icon_file')) {
            $file = $request->file('icon_file');
            $extension = strtolower($file->getClientOriginalExtension());
            
            if ($extension === 'svg') {
                $iconPath = 'category-icons/' . uniqid() . '_' . time() . '.svg';
                Storage::disk('public')->put($iconPath, file_get_contents($file->getRealPath()));
                $icon = $iconPath;
            } else {
                $icon = ImageHelper::optimizeAndSave(
                    $file,
                    'category-icons',
                    200,
                    85
                );
            }
        } 
        elseif ($request->filled('icon_svg')) {
            $svgContent = $request->icon_svg;
            if (strpos($svgContent, '<svg') !== false || strpos($svgContent, '<?xml') !== false) {
                $iconPath = 'category-icons/' . uniqid() . '_' . time() . '.svg';
                Storage::disk('public')->put($iconPath, $svgContent);
                $icon = $iconPath;
            } else {
                $icon = $request->icon_svg;
            }
        }

        Category::create([
            'name' => $request->name,
            'description' => $request->description,
            'order' => $request->order ?? 0,
            'status' => $request->status,
            'icon' => $icon,
        ]);

        return redirect()->route('admin.categories.index')
            ->with('success', 'Đã thêm danh mục mới thành công!');
    }

    public function update(Request $request, Category $category)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:categories,name,' . $category->id,
            'description' => 'nullable|string|max:1000',
            'order' => 'nullable|integer|min:0',
            'status' => 'required|in:active,inactive',
            'icon_file' => 'nullable|image|mimes:jpeg,jpg,png,gif,webp,svg|max:5120',
            'icon_svg' => 'nullable|string|max:5000',
        ], [
            'name.required' => 'Tên danh mục là bắt buộc.',
            'name.unique' => 'Tên danh mục đã tồn tại.',
            'name.max' => 'Tên danh mục không được vượt quá 255 ký tự.',
            'description.max' => 'Mô tả không được vượt quá 1000 ký tự.',
            'icon_file.image' => 'File phải là hình ảnh.',
            'icon_file.mimes' => 'Chỉ chấp nhận file ảnh (JPG, PNG, GIF, WEBP, SVG).',
            'icon_file.max' => 'Kích thước file tối đa 5MB.',
            'icon_svg.max' => 'Mã SVG không được vượt quá 5000 ký tự.',
        ]);

        $icon = $category->icon;

        if ($request->hasFile('icon_file')) {
            if ($category->icon && Storage::disk('public')->exists($category->icon)) {
                ImageHelper::delete($category->icon);
            }

            $file = $request->file('icon_file');
            $extension = strtolower($file->getClientOriginalExtension());
            
            if ($extension === 'svg') {
                $iconPath = 'category-icons/' . uniqid() . '_' . time() . '.svg';
                Storage::disk('public')->put($iconPath, file_get_contents($file->getRealPath()));
                $icon = $iconPath;
            } else {
                $icon = ImageHelper::optimizeAndSave(
                    $file,
                    'category-icons',
                    null,
                    85
                );
            }
        } 
        elseif ($request->filled('icon_svg')) {
            if ($category->icon && Storage::disk('public')->exists($category->icon)) {
                ImageHelper::delete($category->icon);
            }

            $svgContent = $request->icon_svg;
            if (strpos($svgContent, '<svg') !== false || strpos($svgContent, '<?xml') !== false) {
                $iconPath = 'category-icons/' . uniqid() . '_' . time() . '.svg';
                Storage::disk('public')->put($iconPath, $svgContent);
                $icon = $iconPath;
            } else {
                $icon = $request->icon_svg;
            }
        }

        $category->update([
            'name' => $request->name,
            'description' => $request->description,
            'order' => $request->order ?? 0,
            'status' => $request->status,
            'icon' => $icon,
        ]);

        return redirect()->route('admin.categories.index')
            ->with('success', 'Đã cập nhật danh mục thành công!');
    }

    public function destroy(Category $category)
    {
        if ($category->subCategories()->count() > 0) {
            return redirect()->route('admin.categories.index')
                ->with('error', 'Không thể xóa danh mục có chứa danh mục con!');
        }

        $category->delete();

        return redirect()->route('admin.categories.index')
            ->with('success', 'Đã xóa danh mục thành công!');
    }
}

