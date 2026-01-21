<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\ContactLink;
use App\Models\ContactSubmission;
use App\Models\FooterContent;
use App\Models\Social;
use App\Models\SeoSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Artesaos\SEOTools\Facades\SEOTools;
use Artesaos\SEOTools\Facades\SEOMeta;
use Artesaos\SEOTools\Facades\OpenGraph;
use Artesaos\SEOTools\Facades\TwitterCard;

class ContactController extends Controller
{
    /**
     * Display the contact page
     */
    public function index()
    {
        $seoSetting = SeoSetting::getByPageKey('contact');
        
        if ($seoSetting) {
            SEOTools::setTitle($seoSetting->title);
            SEOTools::setDescription($seoSetting->description);
            SEOMeta::setKeywords($seoSetting->keywords);
            SEOTools::setCanonical(url()->current());

            OpenGraph::setTitle($seoSetting->title);
            OpenGraph::setDescription($seoSetting->description);
            OpenGraph::setUrl(url()->current());
            OpenGraph::setSiteName(config('app.name'));
            if ($seoSetting->thumbnail) {
                OpenGraph::addImage($seoSetting->thumbnail_url);
            }

            TwitterCard::setTitle($seoSetting->title);
            TwitterCard::setDescription($seoSetting->description);
            TwitterCard::setType('summary_large_image');
        } else {
            SEOTools::setTitle('Liên hệ - ' . config('app.name'));
            SEOTools::setDescription('Liên hệ với ' . config('app.name') . ' để được hỗ trợ.');
            SEOTools::setCanonical(url()->current());
        }

        $contactContent = FooterContent::where('section', 'contact')->first();
        
        $contactLinks = ContactLink::active()->orderBy('order')->get();
        
        $socials = Social::active()->orderBy('sort_order')->get();

        return view('client.pages.contact.index', compact('contactContent', 'contactLinks', 'socials'));
    }

    /**
     * Store contact submission
     */
    public function store(Request $request)
    {
        $maxAttempts = 3;
        $decaySeconds = 3600;
        
        if (Auth::check()) {
            $key = 'contact_submission:user:' . Auth::id();
        } else {
            $key = 'contact_submission:ip:' . $request->ip();
        }
        
        if (RateLimiter::tooManyAttempts($key, $maxAttempts)) {
            $seconds = RateLimiter::availableIn($key);
            $minutes = ceil($seconds / 60);
            
            $message = $minutes > 1 
                ? "Bạn đã gửi quá nhiều tin nhắn. Vui lòng thử lại sau {$minutes} phút."
                : "Bạn đã gửi quá nhiều tin nhắn. Vui lòng thử lại sau {$seconds} giây.";
            
            return response()->json([
                'success' => false,
                'message' => $message
            ], 429);
        }

        $request->validate([
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|max:20',
            'subject' => 'required|string|max:255',
            'message' => 'required|string|max:2000',
        ], [
            'email.required' => 'Email là bắt buộc.',
            'email.email' => 'Email không hợp lệ.',
            'email.max' => 'Email không được vượt quá 255 ký tự.',
            'phone.max' => 'Số điện thoại không được vượt quá 20 ký tự.',
            'subject.required' => 'Chủ đề là bắt buộc.',
            'subject.max' => 'Chủ đề không được vượt quá 255 ký tự.',
            'message.required' => 'Nội dung là bắt buộc.',
            'message.max' => 'Nội dung không được vượt quá 2000 ký tự.',
        ]);

        RateLimiter::hit($key, $decaySeconds);

        ContactSubmission::create([
            'email' => $request->email,
            'phone' => $request->phone,
            'subject' => $request->subject,
            'message' => $request->message,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'user_id' => Auth::id(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Cảm ơn bạn đã liên hệ! Chúng tôi sẽ phản hồi sớm nhất có thể.'
        ]);
    }
}
