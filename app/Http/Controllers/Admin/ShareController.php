<?php

namespace App\Http\Controllers\Admin;

use App\Enums\ShareStatus;
use App\Helpers\ImageHelper;
use App\Http\Controllers\Controller;
use App\Models\Share;
use App\Models\ShareCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class ShareController extends Controller
{
    /**
     * Display a listing of shares.
     */
    public function index(Request $request)
    {
        $query = Share::with(['category', 'author']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('category')) {
            $query->where('share_category_id', $request->category);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhereHas('author', function ($q) use ($search) {
                        $q->where('full_name', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%");
                    });
            });
        }

        $shares = $query->orderByDesc('created_at')->paginate(15);
        $categories = ShareCategory::ordered()->get();

        $counts = [
            'all' => Share::count(),
            'pending' => Share::pending()->count(),
            'approved' => Share::approved()->count(),
            'draft' => Share::draft()->count(),
        ];

        return view('admin.pages.shares.index', compact('shares', 'categories', 'counts'));
    }

    public function create()
    {
        $categories = ShareCategory::active()->ordered()->get();
        return view('admin.pages.shares.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'excerpt' => 'nullable|string|max:500',
            'content' => 'required|string',
            'share_category_id' => 'required|exists:share_categories,id',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
            'status' => 'required|in:' . implode(',', ShareStatus::adminCanSetValues()),
        ], [
            'title.required' => 'Tiêu đề là bắt buộc.',
            'title.max' => 'Tiêu đề không được vượt quá 255 ký tự.',
            'excerpt.max' => 'Mô tả ngắn không được vượt quá 500 ký tự.',
            'content.required' => 'Nội dung là bắt buộc.',
            'share_category_id.required' => 'Vui lòng chọn danh mục.',
            'share_category_id.exists' => 'Danh mục không hợp lệ.',
            'image.image' => 'Tệp tải lên phải là hình ảnh.',
            'image.mimes' => 'Ảnh phải có định dạng: jpeg, png, jpg, gif, webp.',
            'image.max' => 'Ảnh không được vượt quá 5MB.',
            'status.required' => 'Trạng thái là bắt buộc.',
            'status.in' => 'Trạng thái không hợp lệ.',
        ]);

        $user = Auth::user();
        $status = $request->status === 'approved' ? ShareStatus::APPROVED : ShareStatus::DRAFT;

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = ImageHelper::optimizeAndSave($request->file('image'), 'shares', 1200);
        }

        Share::create([
            'title' => $validated['title'],
            'slug' => Str::slug($validated['title']) . '-' . Str::random(6),
            'excerpt' => $validated['excerpt'],
            'content' => $validated['content'],
            'share_category_id' => $validated['share_category_id'],
            'user_id' => $user->id,
            'status' => $status,
            'image' => $imagePath,
            'approved_by' => $status === ShareStatus::APPROVED ? $user->id : null,
            'approved_at' => $status === ShareStatus::APPROVED ? now() : null,
        ]);

        return redirect()->route('admin.shares.index')->with('success', 'Bài viết đã được tạo thành công!');
    }

    public function show(Share $share)
    {
        $share->load(['category', 'author', 'approvedByUser']);
        return view('admin.pages.shares.show', compact('share'));
    }

    public function edit(Share $share)
    {
        $categories = ShareCategory::active()->ordered()->get();
        return view('admin.pages.shares.edit', compact('share', 'categories'));
    }

    public function update(Request $request, Share $share)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'excerpt' => 'nullable|string|max:500',
            'content' => 'required|string',
            'share_category_id' => 'required|exists:share_categories,id',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
            'status' => 'required|in:' . implode(',', ShareStatus::adminCanSetValues()),
            'rejection_reason' => 'nullable|string|max:1000',
        ], [
            'title.required' => 'Tiêu đề là bắt buộc.',
            'title.max' => 'Tiêu đề không được vượt quá 255 ký tự.',
            'excerpt.max' => 'Mô tả ngắn không được vượt quá 500 ký tự.',
            'content.required' => 'Nội dung là bắt buộc.',
            'share_category_id.required' => 'Vui lòng chọn danh mục.',
            'share_category_id.exists' => 'Danh mục không hợp lệ.',
            'image.image' => 'Tệp tải lên phải là hình ảnh.',
            'image.mimes' => 'Ảnh phải có định dạng: jpeg, png, jpg, gif, webp.',
            'image.max' => 'Ảnh không được vượt quá 5MB.',
            'status.required' => 'Trạng thái là bắt buộc.',
            'status.in' => 'Trạng thái không hợp lệ.',
            'rejection_reason.max' => 'Lý do từ chối không được vượt quá 1000 ký tự.',
        ]);

        $user = Auth::user();
        $status = ShareStatus::from($request->status);

        if ($request->hasFile('image')) {
            ImageHelper::delete($share->image);
            $share->image = ImageHelper::optimizeAndSave($request->file('image'), 'shares', 1200);
        }

        $updateData = [
            'title' => $validated['title'],
            'excerpt' => $validated['excerpt'],
            'content' => $validated['content'],
            'share_category_id' => $validated['share_category_id'],
            'status' => $status,
        ];

        if ($status === ShareStatus::APPROVED && $share->status !== ShareStatus::APPROVED) {
            $updateData['approved_by'] = $user->id;
            $updateData['approved_at'] = now();
            $updateData['rejection_reason'] = null;
        } elseif ($status === ShareStatus::REJECTED) {
            $updateData['rejection_reason'] = $validated['rejection_reason'];
        }

        $share->update($updateData);

        return redirect()->route('admin.shares.index')->with('success', 'Bài viết đã được cập nhật!');
    }

    public function approve(Share $share)
    {
        if (!$share->isPending()) {
            return response()->json(['success' => false, 'message' => 'Bài viết không ở trạng thái chờ duyệt.'], 400);
        }

        $share->update([
            'status' => ShareStatus::APPROVED,
            'approved_by' => Auth::id(),
            'approved_at' => now(),
            'rejection_reason' => null,
        ]);

        return response()->json(['success' => true, 'message' => 'Bài viết đã được duyệt!']);
    }

    public function reject(Request $request, Share $share)
    {
        $request->validate([
            'rejection_reason' => 'required|string|max:1000',
        ], [
            'rejection_reason.required' => 'Vui lòng nhập lý do từ chối.',
            'rejection_reason.max' => 'Lý do từ chối không được vượt quá 1000 ký tự.',
        ]);

        if (!$share->isPending()) {
            return response()->json(['success' => false, 'message' => 'Bài viết không ở trạng thái chờ duyệt.'], 400);
        }

        $share->update([
            'status' => ShareStatus::REJECTED,
            'rejection_reason' => $request->rejection_reason,
        ]);

        return response()->json(['success' => true, 'message' => 'Bài viết đã bị từ chối!']);
    }

    public function destroy(Share $share)
    {
        $share->delete();

        return redirect()->route('admin.shares.index')->with('success', 'Bài viết đã được xóa!');
    }

    public function uploadImage(Request $request)
    {
        $request->validate([
            'upload' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
        ], [
            'upload.required' => 'Ảnh là bắt buộc.',
            'upload.image' => 'Ảnh phải là định dạng hình ảnh.',
            'upload.mimes' => 'Ảnh phải có định dạng: jpeg, png, jpg, gif, webp.',
            'upload.max' => 'Ảnh không được vượt quá 5MB.',
        ]);

        if ($request->hasFile('upload')) {
            $path = ImageHelper::optimizeAndSave($request->file('upload'), 'shares/content', 1000, 80);
            $url = asset('storage/' . $path);

            return response()->json([
                'uploaded' => true,
                'url' => $url,
            ]);
        }

        return response()->json([
            'uploaded' => false,
            'error' => ['message' => 'Không thể tải ảnh lên.'],
        ], 400);
    }
}
