<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\TermOfService;
use App\Models\SeoSetting;
use Artesaos\SEOTools\Facades\SEOTools;
use Artesaos\SEOTools\Facades\SEOMeta;
use Artesaos\SEOTools\Facades\OpenGraph;
use Artesaos\SEOTools\Facades\TwitterCard;

class TermOfServiceController extends Controller
{
    /**
     * Display terms of service page
     */
    public function index()
    {
        $terms = TermOfService::getLatest();

        if (!$terms) {
            $terms = TermOfService::create([
                'title' => 'Điều khoản sử dụng',
                'content' => 'Nội dung điều khoản sử dụng chi tiết sẽ được cập nhật tại đây...',
                'summary' => 'Bằng việc sử dụng website này, bạn đồng ý với các điều khoản sử dụng của chúng tôi. Vui lòng đọc kỹ các điều khoản trước khi sử dụng dịch vụ.',
                'is_active' => true,
            ]);
        }

        $seoSetting = SeoSetting::getByPageKey('terms_of_service');
        
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
            SEOTools::setTitle('Điều khoản sử dụng - ' . config('app.name'));
            SEOTools::setDescription('Điều khoản sử dụng và chính sách của ' . config('app.name'));
            SEOTools::setCanonical(url()->current());
        }

        return view('client.pages.terms-of-service.index', compact('terms'));
    }

    /**
     * Get terms summary for popup
     */
    public function getSummary()
    {
        $terms = TermOfService::getLatest();

        if (!$terms || !$terms->is_active) {
            return response()->json([
                'success' => false,
                'message' => 'Không có điều khoản nào.',
            ]);
        }

        return response()->json([
            'success' => true,
            'title' => $terms->title,
            'summary' => $terms->summary ?? 'Bằng việc sử dụng website này, bạn đồng ý với các điều khoản sử dụng của chúng tôi.',
            'link' => route('terms-of-service.index'),
        ]);
    }
}
