<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FooterContent;
use Illuminate\Http\Request;

class FooterContentController extends Controller
{

    public function index()
    {
        $contents = FooterContent::orderBy('order')->get();
        
        $sections = ['contact' => 'Liên hệ', 'information' => 'Thông tin', 'seller_registration' => 'Đăng ký bán hàng'];
        
        foreach ($sections as $section => $title) {
            $content = FooterContent::where('section', $section)->first();
            if (!$content) {
                FooterContent::create([
                    'section' => $section,
                    'title' => $title,
                    'description' => '',
                    'order' => array_search($section, array_keys($sections)),
                ]);
            }
        }
        
        $contents = FooterContent::orderBy('order')->get();
        
        return view('admin.pages.footer-contents.index', compact('contents'));
    }

    public function update(Request $request, FooterContent $footerContent)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:2000',
        ], [
            'title.required' => 'Tiêu đề là bắt buộc.',
            'title.max' => 'Tiêu đề không được vượt quá 255 ký tự.',
            'description.max' => 'Mô tả không được vượt quá 2000 ký tự.',
        ]);

        $footerContent->update([
            'title' => $request->title,
            'description' => $request->description,
        ]);

        return redirect()->route('admin.footer-contents.index')
            ->with('success', 'Đã cập nhật nội dung footer thành công!');
    }
}
