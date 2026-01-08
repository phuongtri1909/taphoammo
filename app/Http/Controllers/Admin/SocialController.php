<?php

namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;

use App\Models\Social;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Cache;

class SocialController extends Controller
{
    /**
     * Hiển thị danh sách các mạng xã hội
     */
    public function index()
    {
        $socials = Social::orderBy('sort_order')->get();
        
        // Danh sách biểu tượng FontAwesome cho dropdown
        $fontAwesomeIcons = [
            // Brand icons
            'fab fa-facebook-f' => 'Facebook',
            'fab fa-twitter' => 'Twitter',
            'fab fa-instagram' => 'Instagram',
            'fab fa-youtube' => 'YouTube',
            'fab fa-tiktok' => 'TikTok',
            'fab fa-pinterest' => 'Pinterest',
            'fab fa-discord' => 'Discord',
            'fab fa-telegram' => 'Telegram',
            'fab fa-linkedin' => 'LinkedIn',
            'fab fa-github' => 'GitHub',
            'fab fa-reddit' => 'Reddit',
            'fab fa-snapchat' => 'Snapchat',
            'fab fa-whatsapp' => 'WhatsApp',
            'fab fa-line' => 'Line',
            
            // Regular icons
            'fas fa-envelope' => 'Email',
            'fas fa-globe' => 'Website',
            'fas fa-phone' => 'Phone',
            'fas fa-map-marker-alt' => 'Location',
            'fas fa-rss' => 'RSS Feed',
            
            // Custom SVG icons
            'custom-zalo' => 'Zalo',
        ];
        
        return view('admin.pages.socials.index', compact('socials', 'fontAwesomeIcons'));
    }

    /**
     * Hiển thị form tạo mới mạng xã hội
     */
    public function create()
    {
        // Danh sách biểu tượng FontAwesome cho dropdown
        $fontAwesomeIcons = [
            // Brand icons
            'fab fa-facebook-f' => 'Facebook',
            'fab fa-twitter' => 'Twitter',
            'fab fa-instagram' => 'Instagram',
            'fab fa-youtube' => 'YouTube',
            'fab fa-tiktok' => 'TikTok',
            'fab fa-pinterest' => 'Pinterest',
            'fab fa-discord' => 'Discord',
            'fab fa-telegram' => 'Telegram',
            'fab fa-linkedin' => 'LinkedIn',
            'fab fa-github' => 'GitHub',
            'fab fa-reddit' => 'Reddit',
            'fab fa-snapchat' => 'Snapchat',
            'fab fa-whatsapp' => 'WhatsApp',
            'fab fa-line' => 'Line',
            
            // Regular icons
            'fas fa-envelope' => 'Email',
            'fas fa-globe' => 'Website',
            'fas fa-phone' => 'Phone',
            'fas fa-map-marker-alt' => 'Location',
            'fas fa-rss' => 'RSS Feed',
            
            // Custom SVG icons
            'custom-zalo' => 'Zalo',
        ];
        
        return view('admin.pages.socials.create', compact('fontAwesomeIcons'));
    }

    /**
     * Lưu mạng xã hội mới vào database
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'url' => [
                'required',
                'string',
                'max:255',
                function ($attribute, $value, $fail) {
                    if (Str::startsWith($value, ['mailto:', 'tel:', 'sms:'])) {
                        return;
                    }
                    
                    if (Str::startsWith($value, ['javascript:', 'data:'])) {
                        $fail('Đường dẫn không được chứa mã JavaScript hoặc dữ liệu trực tiếp.');
                        return;
                    }
                    
                    if (!filter_var($value, FILTER_VALIDATE_URL)) {
                        $fail('Đường dẫn phải là một URL hợp lệ hoặc định dạng đặc biệt như mailto:, tel:.');
                    }
                },
            ],
            'icon' => 'required|string|max:255',
            'sort_order' => 'nullable|integer|min:0',
        ],
        [
            'name.required' => 'Tên mạng xã hội là bắt buộc.',
            'name.string' => 'Tên mạng xã hội phải là một chuỗi.',
            'name.max' => 'Tên mạng xã hội không được vượt quá 255 ký tự.',
            'url.required' => 'Đường dẫn là bắt buộc.',
            'url.string' => 'Đường dẫn phải là một chuỗi.',
            'url.max' => 'Đường dẫn không được vượt quá 255 ký tự.',
            'icon.required' => 'Icon là bắt buộc.',
            'icon.string' => 'Icon phải là một chuỗi.',
            'icon.max' => 'Icon không được vượt quá 255 ký tự.',
        ]);

        // Tạo key từ tên
        $key = Str::slug($request->name);
        
        // Kiểm tra key đã tồn tại chưa
        $existingKey = Social::where('key', $key)->first();
        if ($existingKey) {
            $key = $key . '-' . uniqid();
        }
        
        Social::create([
            'name' => $request->name,
            'url' => $request->url,
            'icon' => $request->icon,
            'key' => $key,
            'is_active' => $request->has('is_active') ? 1 : 0,
            'sort_order' => $request->sort_order ?? 0,
        ]);
        
        // Xóa cache
        Cache::forget('socials');

        return redirect()->route('admin.socials.index')
            ->with('success', 'Đã thêm mạng xã hội mới thành công!');
    }

    /**
     * Hiển thị form chỉnh sửa mạng xã hội
     */
    public function edit(Social $social)
    {
        // Danh sách biểu tượng FontAwesome cho dropdown
        $fontAwesomeIcons = [
            // Brand icons
            'fab fa-facebook-f' => 'Facebook',
            'fab fa-twitter' => 'Twitter',
            'fab fa-instagram' => 'Instagram',
            'fab fa-youtube' => 'YouTube',
            'fab fa-tiktok' => 'TikTok',
            'fab fa-pinterest' => 'Pinterest',
            'fab fa-discord' => 'Discord',
            'fab fa-telegram' => 'Telegram',
            'fab fa-linkedin' => 'LinkedIn',
            'fab fa-github' => 'GitHub',
            'fab fa-reddit' => 'Reddit',
            'fab fa-snapchat' => 'Snapchat',
            'fab fa-whatsapp' => 'WhatsApp',
            'fab fa-line' => 'Line',
            
            // Regular icons
            'fas fa-envelope' => 'Email',
            'fas fa-globe' => 'Website',
            'fas fa-phone' => 'Phone',
            'fas fa-map-marker-alt' => 'Location',
            'fas fa-rss' => 'RSS Feed',
            
            // Custom SVG icons
            'custom-zalo' => 'Zalo',
        ];
        
        return view('admin.pages.socials.edit', compact('social', 'fontAwesomeIcons'));
    }

    /**
     * Cập nhật thông tin mạng xã hội trong database
     */
    public function update(Request $request, Social $social)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'url' => [
                'required',
                'string',
                'max:255',
                function ($attribute, $value, $fail) {
                    if (Str::startsWith($value, ['mailto:', 'tel:', 'sms:'])) {
                        return;
                    }
                    if (Str::startsWith($value, ['javascript:', 'data:'])) {
                        $fail('Đường dẫn không được chứa mã JavaScript hoặc dữ liệu trực tiếp.');
                        return;
                    }
                    
                    if (!filter_var($value, FILTER_VALIDATE_URL)) {
                        $fail('Đường dẫn phải là một URL hợp lệ hoặc định dạng đặc biệt như mailto:, tel:.');
                    }
                },
            ],
            'icon' => 'required|string|max:255',
            'sort_order' => 'nullable|integer|min:0',
        ],
        [
            'name.required' => 'Tên mạng xã hội là bắt buộc.',
            'name.string' => 'Tên mạng xã hội phải là một chuỗi.',
            'name.max' => 'Tên mạng xã hội không được vượt quá 255 ký tự.',
            'url.required' => 'Đường dẫn là bắt buộc.',
            'url.string' => 'Đường dẫn phải là một chuỗi.',
            'url.max' => 'Đường dẫn không được vượt quá 255 ký tự.',
            'icon.required' => 'Icon là bắt buộc.',
            'icon.string' => 'Icon phải là một chuỗi.',
            'icon.max' => 'Icon không được vượt quá 255 ký tự.',
        ]);

        $social->update([
            'name' => $request->name,
            'url' => $request->url,
            'icon' => $request->icon,
            'is_active' => $request->has('is_active') ? 1 : 0,
            'sort_order' => $request->sort_order ?? 0,
        ]);
        
        // Xóa cache
        Cache::forget('socials');

        return redirect()->route('admin.socials.index')
            ->with('success', 'Đã cập nhật mạng xã hội thành công!');
    }

    /**
     * Xóa mạng xã hội khỏi database
     */
    public function destroy(Social $social)
    {
        $social->delete();
        
        // Xóa cache
        Cache::forget('socials');
        
        return redirect()->route('admin.socials.index')
            ->with('success', 'Đã xóa mạng xã hội thành công!');
    }
}