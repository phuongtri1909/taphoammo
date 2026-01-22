<?php

namespace App\Http\Controllers\Client;

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
    public function index(Request $request)
    {
        $user = Auth::user();
        
        $query = Share::with('category')
            ->byUser($user->id);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('content', 'like', "%{$search}%");
            });
        }

        $shares = $query->orderByDesc('created_at')->paginate(10);

        $counts = [
            'all' => Share::byUser($user->id)->count(),
            'draft' => Share::byUser($user->id)->draft()->count(),
            'pending' => Share::byUser($user->id)->pending()->count(),
            'approved' => Share::byUser($user->id)->approved()->count(),
        ];

        return view('client.pages.shares.index', compact('shares', 'counts'));
    }

    public function create()
    {
        $categories = ShareCategory::active()->ordered()->get();
        return view('client.pages.shares.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'excerpt' => 'nullable|string|max:500',
            'content' => 'required|string',
            'share_category_id' => 'required|exists:share_categories,id',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
            'action' => 'required|in:draft,submit',
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
        ]);

        $user = Auth::user();
        
        if ($request->action === 'submit') {
            $status = $user->role === 'admin' ? ShareStatus::APPROVED : ShareStatus::PENDING;
        } else {
            $status = ShareStatus::DRAFT;
        }

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = ImageHelper::optimizeAndSave($request->file('image'), 'shares', 1200);
        }

        $share = Share::create([
            'title' => $validated['title'],
            'excerpt' => $validated['excerpt'],
            'content' => $validated['content'],
            'share_category_id' => $validated['share_category_id'],
            'user_id' => $user->id,
            'status' => $status,
            'image' => $imagePath,
            'approved_by' => $user->role === 'admin' && $request->action === 'submit' ? $user->id : null,
            'approved_at' => $user->role === 'admin' && $request->action === 'submit' ? now() : null,
        ]);

        $message = $status === ShareStatus::PENDING 
            ? 'Bài viết đã được gửi duyệt thành công!' 
            : ($status === ShareStatus::APPROVED ? 'Bài viết đã được đăng thành công!' : 'Bài viết đã được lưu nháp!');

        return redirect()->route('shares.manage.index')->with('success', $message);
    }

    public function edit(Share $share)
    {
        if ($share->user_id !== Auth::id()) {
            abort(403, 'Bạn không có quyền chỉnh sửa bài viết này.');
        }

        if (!$share->canEdit()) {
            return redirect()->route('shares.manage.index')
                ->with('error', 'Bài viết đang chờ duyệt hoặc đã được duyệt không thể chỉnh sửa.');
        }

        $categories = ShareCategory::active()->ordered()->get();
        return view('client.pages.shares.edit', compact('share', 'categories'));
    }

    public function update(Request $request, Share $share)
    {
        if ($share->user_id !== Auth::id()) {
            abort(403, 'Bạn không có quyền chỉnh sửa bài viết này.');
        }

        if (!$share->canEdit()) {
            return redirect()->route('shares.manage.index')
                ->with('error', 'Bài viết đang chờ duyệt hoặc đã được duyệt không thể chỉnh sửa.');
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'excerpt' => 'nullable|string|max:500',
            'content' => 'required|string',
            'share_category_id' => 'required|exists:share_categories,id',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
            'action' => 'required|in:draft,submit',
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
        ]);

        $user = Auth::user();
        
        if ($request->action === 'submit') {
            $status = $user->role === 'admin' ? ShareStatus::APPROVED : ShareStatus::PENDING;
        } else {
            $status = ShareStatus::DRAFT;
        }

        if ($request->hasFile('image')) {
            ImageHelper::delete($share->image);
            $share->image = ImageHelper::optimizeAndSave($request->file('image'), 'shares', 1200);
        }

        $share->update([
            'title' => $validated['title'],
            'excerpt' => $validated['excerpt'],
            'content' => $validated['content'],
            'share_category_id' => $validated['share_category_id'],
            'status' => $status,
            'rejection_reason' => null,
            'approved_by' => $user->role === 'admin' && $request->action === 'submit' ? $user->id : null,
            'approved_at' => $user->role === 'admin' && $request->action === 'submit' ? now() : null,
        ]);

        $message = $status === ShareStatus::PENDING 
            ? 'Bài viết đã được gửi duyệt lại!' 
            : ($status === ShareStatus::APPROVED ? 'Bài viết đã được cập nhật và đăng!' : 'Bài viết đã được lưu nháp!');

        return redirect()->route('shares.manage.index')->with('success', $message);
    }

    public function toggleVisibility(Share $share)
    {
        if ($share->user_id !== Auth::id()) {
            return response()->json(['success' => false, 'message' => 'Bạn không có quyền thực hiện thao tác này.'], 403);
        }

        if ($share->canHide()) {
            $share->update(['status' => ShareStatus::HIDDEN]);
            return response()->json(['success' => true, 'message' => 'Bài viết đã được ẩn.']);
        }

        if ($share->canUnhide()) {
            $share->update(['status' => ShareStatus::APPROVED]);
            return response()->json(['success' => true, 'message' => 'Bài viết đã được hiện lại.']);
        }

        return response()->json(['success' => false, 'message' => 'Không thể thực hiện thao tác này.'], 400);
    }

    public function destroy(Share $share)
    {
        if ($share->user_id !== Auth::id()) {
            return redirect()->route('shares.manage.index')
                ->with('error', 'Bạn không có quyền xóa bài viết này.');
        }

        $share->delete();

        return redirect()->route('shares.manage.index')->with('success', 'Bài viết đã được xóa.');
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
