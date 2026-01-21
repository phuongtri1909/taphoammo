<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ContactLink;
use Illuminate\Http\Request;

class ContactLinkController extends Controller
{

    public function index()
    {
        $links = ContactLink::orderBy('order')->paginate(20);
        
        $fontAwesomeIcons = [
            'fas fa-comment-dots' => 'Chat',
            'fab fa-facebook' => 'Facebook',
            'fas fa-envelope' => 'Email',
            'fas fa-phone' => 'Phone',
            'fas fa-clock' => 'Clock',
            'fab fa-zalo' => 'Zalo',
            'fas fa-question-circle' => 'Question',
        ];
        
        return view('admin.pages.contact-links.index', compact('links', 'fontAwesomeIcons'));
    }

    public function create()
    {
        $fontAwesomeIcons = [
            'fas fa-comment-dots' => 'Chat',
            'fab fa-facebook' => 'Facebook',
            'fas fa-envelope' => 'Email',
            'fas fa-phone' => 'Phone',
            'fas fa-clock' => 'Clock',
            'fab fa-zalo' => 'Zalo',
            'fas fa-question-circle' => 'Question',
        ];
        
        return view('admin.pages.contact-links.create', compact('fontAwesomeIcons'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'url' => 'required|string|max:500',
            'icon' => 'nullable|string|max:255',
            'order' => 'nullable|integer|min:0',
        ], [
            'name.required' => 'Tên liên hệ là bắt buộc.',
            'name.max' => 'Tên liên hệ không được vượt quá 255 ký tự.',
            'url.required' => 'URL là bắt buộc.',
            'url.max' => 'URL không được vượt quá 500 ký tự.',
            'icon.max' => 'Icon không được vượt quá 255 ký tự.',
        ]);

        ContactLink::create([
            'name' => $request->name,
            'url' => $request->url,
            'icon' => $request->icon,
            'order' => $request->order ?? 0,
            'is_active' => $request->has('is_active') ? 1 : 0,
        ]);

        return redirect()->route('admin.contact-links.index')
            ->with('success', 'Đã thêm liên hệ mới thành công!');
    }

    public function edit(ContactLink $contactLink)
    {
        $fontAwesomeIcons = [
            'fas fa-comment-dots' => 'Chat',
            'fab fa-facebook' => 'Facebook',
            'fas fa-envelope' => 'Email',
            'fas fa-phone' => 'Phone',
            'fas fa-clock' => 'Clock',
            'fab fa-zalo' => 'Zalo',
            'fas fa-question-circle' => 'Question',
        ];
        
        return view('admin.pages.contact-links.edit', compact('contactLink', 'fontAwesomeIcons'));
    }

    public function update(Request $request, ContactLink $contactLink)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'url' => 'required|string|max:500',
            'icon' => 'nullable|string|max:255',
            'order' => 'nullable|integer|min:0',
        ], [
            'name.required' => 'Tên liên hệ là bắt buộc.',
            'name.max' => 'Tên liên hệ không được vượt quá 255 ký tự.',
            'url.required' => 'URL là bắt buộc.',
            'url.max' => 'URL không được vượt quá 500 ký tự.',
            'icon.max' => 'Icon không được vượt quá 255 ký tự.',
        ]);

        $contactLink->update([
            'name' => $request->name,
            'url' => $request->url,
            'icon' => $request->icon,
            'order' => $request->order ?? 0,
            'is_active' => $request->has('is_active') ? 1 : 0,
        ]);

        return redirect()->route('admin.contact-links.index')
            ->with('success', 'Đã cập nhật liên hệ thành công!');
    }

    public function destroy(ContactLink $contactLink)
    {
        $contactLink->delete();

        return redirect()->route('admin.contact-links.index')
            ->with('success', 'Đã xóa liên hệ thành công!');
    }
}
