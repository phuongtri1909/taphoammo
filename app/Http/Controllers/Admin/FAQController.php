<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FAQ;
use Illuminate\Http\Request;

class FAQController extends Controller
{
    public function index(Request $request)
    {
        $query = FAQ::query();

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('question', 'like', "%{$search}%")
                    ->orWhere('answer', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('is_active', $request->input('status') === 'active');
        }

        $faqs = $query->orderBy('order')->orderBy('id')->paginate(15);

        return view('admin.pages.faqs.index', compact('faqs'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'question' => 'required|string|max:500',
            'answer' => 'required|string|max:5000',
            'order' => 'nullable|integer|min:0',
            'is_active' => 'boolean',
        ], [
            'question.required' => 'Câu hỏi là bắt buộc.',
            'question.max' => 'Câu hỏi không được vượt quá 500 ký tự.',
            'answer.required' => 'Câu trả lời là bắt buộc.',
            'answer.max' => 'Câu trả lời không được vượt quá 5000 ký tự.',
            'order.integer' => 'Thứ tự phải là số nguyên.',
            'order.min' => 'Thứ tự phải lớn hơn hoặc bằng 0.',
        ]);

        FAQ::create([
            'question' => $validated['question'],
            'answer' => $validated['answer'],
            'order' => $validated['order'] ?? 0,
            'is_active' => $validated['is_active'] ?? true,
        ]);

        return redirect()->route('admin.faqs.index')
            ->with('success', 'Đã thêm FAQ mới thành công!');
    }


    public function update(Request $request, FAQ $faq)
    {
        $validated = $request->validate([
            'question' => 'required|string|max:500',
            'answer' => 'required|string|max:5000',
            'order' => 'nullable|integer|min:0',
            'is_active' => 'boolean',
        ], [
            'question.required' => 'Câu hỏi là bắt buộc.',
            'question.max' => 'Câu hỏi không được vượt quá 500 ký tự.',
            'answer.required' => 'Câu trả lời là bắt buộc.',
            'answer.max' => 'Câu trả lời không được vượt quá 5000 ký tự.',
            'order.integer' => 'Thứ tự phải là số nguyên.',
            'order.min' => 'Thứ tự phải lớn hơn hoặc bằng 0.',
        ]);

        $faq->update([
            'question' => $validated['question'],
            'answer' => $validated['answer'],
            'order' => $validated['order'] ?? 0,
            'is_active' => $validated['is_active'] ?? false,
        ]);

        return redirect()->route('admin.faqs.index')
            ->with('success', 'Đã cập nhật FAQ thành công!');
    }

    /**
     * Remove the specified FAQ
     */
    public function destroy(FAQ $faq)
    {
        $faq->delete();

        return redirect()->route('admin.faqs.index')
            ->with('success', 'Đã xóa FAQ thành công!');
    }
}
