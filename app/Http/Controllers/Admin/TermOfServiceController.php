<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TermOfService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TermOfServiceController extends Controller
{
    /**
     * Display terms of service edit form
     */
    public function index()
    {
        $terms = TermOfService::latest()->first();
        
        if (!$terms) {
            $terms = TermOfService::create([
                'title' => 'Điều khoản sử dụng',
                'content' => 'Nội dung điều khoản sử dụng...',
                'summary' => 'Bằng việc sử dụng website này, bạn đồng ý với các điều khoản sử dụng của chúng tôi.',
                'is_active' => true,
            ]);
        }

        return view('admin.pages.terms-of-service.index', compact('terms'));
    }

    /**
     * Update terms of service
     */
    public function update(Request $request, TermOfService $termOfService)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'summary' => 'nullable|string|max:500',
            'is_active' => 'nullable|boolean',
        ], [
            'title.required' => 'Vui lòng nhập tiêu đề.',
            'content.required' => 'Vui lòng nhập nội dung điều khoản.',
            'summary.max' => 'Tóm tắt không được vượt quá 500 ký tự.',
        ]);

        DB::transaction(function () use ($termOfService, $validated) {
            $termOfService->title = $validated['title'];
            $termOfService->content = $validated['content'];
            $termOfService->summary = $validated['summary'] ?? '';
            $termOfService->is_active = isset($validated['is_active']) ? (bool)$validated['is_active'] : true;
            $termOfService->save();
        });

        return redirect()->route('admin.terms-of-service.index')
            ->with('success', 'Cập nhật điều khoản sử dụng thành công!');
    }
}
