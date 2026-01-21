<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Share;
use App\Models\ShareCategory;
use App\Models\SeoSetting;
use Illuminate\Http\Request;
use Artesaos\SEOTools\Facades\SEOTools;
use Artesaos\SEOTools\Facades\SEOMeta;
use Artesaos\SEOTools\Facades\OpenGraph;
use Artesaos\SEOTools\Facades\TwitterCard;

class PublicShareController extends Controller
{
    /**
     * Display a listing of approved shares.
     */
    public function index(Request $request)
    {
        $seoSetting = SeoSetting::getByPageKey('shares');
        
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
            SEOTools::setTitle('Chia sẻ kinh nghiệm MMO - ' . config('app.name'));
            SEOTools::setDescription('Chia sẻ kinh nghiệm, thủ thuật kiếm tiền online MMO từ cộng đồng ' . config('app.name'));
            SEOTools::setCanonical(url()->current());
        }

        $query = Share::with(['category', 'author'])
            ->published();

        if ($request->filled('category')) {
            $category = ShareCategory::where('slug', $request->category)->first();
            if ($category) {
                $query->where('share_category_id', $category->id);
            }
        }

        if ($request->filled('author')) {
            $authorName = $request->author;
            $query->whereHas('author', function ($authorQuery) use ($authorName) {
                $authorQuery->where('full_name', $authorName);
            });
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('content', 'like', "%{$search}%")
                    ->orWhere('excerpt', 'like', "%{$search}%")
                    ->orWhereHas('author', function ($authorQuery) use ($search) {
                        $authorQuery->where('full_name', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%");
                    });
            });
        }

        $shares = $query->paginate(12);
        $categories = ShareCategory::active()->ordered()->withCount('approvedShares')->get();
        $featuredShares = Share::published()->orderByDesc('views')->take(5)->get();

        return view('client.pages.shares.public.index', compact('shares', 'categories', 'featuredShares'));
    }

    /**
     * Display the specified share.
     */
    public function show(string $slug)
    {
        $share = Share::with(['category', 'author'])
            ->where('slug', $slug)
            ->approved()
            ->firstOrFail();

        $share->incrementViews();

        $baseSeo = SeoSetting::getByPageKey('shares');
        $seoData = SeoSetting::getShareSeo($share, $baseSeo);
        
        SEOTools::setTitle($seoData->title);
        SEOTools::setDescription($seoData->description);
        SEOMeta::setKeywords($seoData->keywords);
        SEOTools::setCanonical(route('shares.show', $share->slug));

        OpenGraph::setTitle($seoData->title);
        OpenGraph::setDescription($seoData->description);
        OpenGraph::setUrl(route('shares.show', $share->slug));
        OpenGraph::setSiteName(config('app.name'));
        OpenGraph::addProperty('type', 'article');
        OpenGraph::addImage($seoData->thumbnail);

        TwitterCard::setTitle($seoData->title);
        TwitterCard::setDescription($seoData->description);
        TwitterCard::setType('summary_large_image');
        TwitterCard::addImage($seoData->thumbnail);

        $relatedShares = Share::with(['category', 'author'])
            ->where('share_category_id', $share->share_category_id)
            ->where('id', '!=', $share->id)
            ->published()
            ->take(4)
            ->get();

        $categories = ShareCategory::active()->ordered()->withCount('approvedShares')->get();

        return view('client.pages.shares.public.show', compact('share', 'relatedShares', 'categories'));
    }
}
