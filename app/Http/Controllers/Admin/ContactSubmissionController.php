<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ContactSubmission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ContactSubmissionController extends Controller
{

    public function index(Request $request)
    {
        $query = ContactSubmission::with(['user', 'respondedBy'])->latest();

        if ($request->filled('status')) {
            if ($request->status === 'unread') {
                $query->whereNull('read_at');
            } elseif ($request->status === 'read') {
                $query->whereNotNull('read_at')->whereNull('responded_at');
            } elseif ($request->status === 'responded') {
                $query->whereNotNull('responded_at');
            }
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('email', 'like', "%{$search}%")
                  ->orWhere('subject', 'like', "%{$search}%")
                  ->orWhere('message', 'like', "%{$search}%");
            });
        }

        $submissions = $query->paginate(20);

        return view('admin.pages.contact-submissions.index', compact('submissions'));
    }

    public function show(ContactSubmission $contactSubmission)
    {
        $contactSubmission->load(['user', 'respondedBy']);
        
        if (!$contactSubmission->isRead()) {
            $contactSubmission->markAsRead();
        }

        return view('admin.pages.contact-submissions.show', compact('contactSubmission'));
    }

    public function update(Request $request, ContactSubmission $contactSubmission)
    {
        $request->validate([
            'admin_response' => 'required|string|max:2000',
        ], [
            'admin_response.required' => 'Nội dung phản hồi là bắt buộc.',
            'admin_response.max' => 'Nội dung phản hồi không được vượt quá 2000 ký tự.',
        ]);

        $contactSubmission->markAsResponded(Auth::id(), $request->admin_response);

        return redirect()->route('admin.contact-submissions.show', $contactSubmission)
            ->with('success', 'Đã gửi phản hồi thành công!');
    }

    public function markAsRead(ContactSubmission $contactSubmission)
    {
        $contactSubmission->markAsRead();

        return redirect()->back()
            ->with('success', 'Đã đánh dấu là đã đọc!');
    }
}
