<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\HeaderConfig;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Helpers\ImageHelper;
use Illuminate\Support\Facades\Storage;

class HeaderConfigController extends Controller
{
    /**
     * Display header configs index
     */
    public function index()
    {
        $supportBar = HeaderConfig::getSupportBar();
        $promotionalBanner = HeaderConfig::getPromotionalBanner();
        $searchBackground = HeaderConfig::getSearchBackground();

        if (!$supportBar) {
            $supportBar = HeaderConfig::create([
                'key' => 'support_bar',
                'label' => 'Thanh hỗ trợ',
                'is_active' => true,
                'config_data' => [
                    'facebook_url' => '',
                    'facebook_text' => '',
                    'email' => '',
                    'email_text' => '',
                    'operating_hours_text' => '',
                ],
            ]);
        }

        if (!$promotionalBanner) {
            $promotionalBanner = HeaderConfig::create([
                'key' => 'promotional_banner',
                'label' => 'Banner quảng cáo',
                'is_active' => true,
                'config_data' => [
                    'content' => '',
                ],
            ]);
        }

        if (!$searchBackground) {
            $searchBackground = HeaderConfig::create([
                'key' => 'search_background',
                'label' => 'Background tìm kiếm',
                'is_active' => true,
                'config_data' => [
                    'background_image' => null,
                ],
            ]);
        }

        return view('admin.pages.header-configs.index', compact('supportBar', 'promotionalBanner', 'searchBackground'));
    }

    /**
     * Update support bar config
     */
    public function updateSupportBar(Request $request)
    {
        $validated = $request->validate([
            'label' => 'nullable|string|max:255',
            'facebook_url' => 'nullable|url|max:500',
            'facebook_text' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'email_text' => 'nullable|string|max:255',
            'operating_hours_text' => 'nullable|string|max:255',
            'is_active' => 'nullable|boolean',
        ], [
            'facebook_url.url' => 'URL Facebook không hợp lệ.',
            'email.email' => 'Email không hợp lệ.',
        ]);

        DB::transaction(function () use ($validated) {
            $config = HeaderConfig::firstOrCreate(
                ['key' => 'support_bar'],
                ['label' => 'Thanh hỗ trợ']
            );

            $config->label = $validated['label'] ?? 'Thanh hỗ trợ';
            $config->is_active = isset($validated['is_active']) ? (bool)$validated['is_active'] : true;
            
            $configData = [
                'facebook_url' => $validated['facebook_url'] ?? '',
                'facebook_text' => $validated['facebook_text'] ?? '',
                'email' => $validated['email'] ?? '',
                'email_text' => $validated['email_text'] ?? '',
                'operating_hours_text' => $validated['operating_hours_text'] ?? '',
            ];

            $config->config_data = $configData;
            $config->save();
        });

        return redirect()->route('admin.header-configs.index')
            ->with('success', 'Cập nhật thanh hỗ trợ thành công!');
    }

    /**
     * Update promotional banner config
     */
    public function updatePromotionalBanner(Request $request)
    {
        $validated = $request->validate([
            'label' => 'nullable|string|max:255',
            'content' => 'required|string|max:2000',
            'is_active' => 'nullable|boolean',
        ], [
            'content.required' => 'Vui lòng nhập nội dung banner.',
            'content.max' => 'Nội dung banner không được vượt quá 2000 ký tự.',
        ]);

        DB::transaction(function () use ($validated) {
            $config = HeaderConfig::firstOrCreate(
                ['key' => 'promotional_banner'],
                ['label' => 'Banner quảng cáo']
            );

            $config->label = $validated['label'] ?? 'Banner quảng cáo';
            $config->is_active = isset($validated['is_active']) ? (bool)$validated['is_active'] : true;
            $config->config_data = [
                'content' => $validated['content'],
            ];
            $config->save();
        });

        return redirect()->route('admin.header-configs.index')
            ->with('success', 'Cập nhật banner quảng cáo thành công!');
    }

    /**
     * Update search background config
     */
    public function updateSearchBackground(Request $request)
    {
        $validated = $request->validate([
            'label' => 'nullable|string|max:255',
            'background_image' => 'nullable|image|mimes:jpeg,jpg,png,gif,webp|max:10240',
            'is_active' => 'nullable|boolean',
        ], [
            'background_image.image' => 'File phải là hình ảnh.',
            'background_image.mimes' => 'Chỉ chấp nhận file ảnh (JPG, PNG, GIF, WEBP).',
            'background_image.max' => 'Kích thước file tối đa 10MB.',
        ]);

        DB::transaction(function () use ($validated, $request) {
            $config = HeaderConfig::firstOrCreate(
                ['key' => 'search_background'],
                ['label' => 'Background tìm kiếm']
            );

            $config->label = $validated['label'] ?? 'Background tìm kiếm';
            $config->is_active = isset($validated['is_active']) ? (bool)$validated['is_active'] : true;
            
            $configData = $config->config_data ?? [];
            
            if ($request->hasFile('background_image')) {
                if (isset($configData['background_image']) && $configData['background_image']) {
                    ImageHelper::delete($configData['background_image']);
                }

                $backgroundPath = ImageHelper::optimizeAndSave(
                    $request->file('background_image'),
                    'search-backgrounds',
                    1920,
                    85
                );

                $configData['background_image'] = $backgroundPath;
            }

            $config->config_data = $configData;
            $config->save();
        });

        return redirect()->route('admin.header-configs.index')
            ->with('success', 'Cập nhật background tìm kiếm thành công!');
    }
}
