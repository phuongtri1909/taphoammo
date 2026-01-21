<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\FAQ;
use App\Models\SeoSetting;
use Illuminate\Http\Request;
use Artesaos\SEOTools\Facades\SEOTools;
use Artesaos\SEOTools\Facades\SEOMeta;
use Artesaos\SEOTools\Facades\OpenGraph;
use Artesaos\SEOTools\Facades\TwitterCard;

class FAQController extends Controller
{
    /**
     * Display FAQs page
     */
    public function index()
    {
        $seoSetting = SeoSetting::getByPageKey('faqs');
        
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
            SEOTools::setTitle('Câu hỏi thường gặp - ' . config('app.name'));
            SEOTools::setDescription('Giải đáp các thắc mắc thường gặp tại ' . config('app.name'));
            SEOTools::setCanonical(url()->current());
        }

        $faqs = FAQ::active()->ordered()->get();

        return view('client.pages.faqs.index', compact('faqs'));
    }
}
