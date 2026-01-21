<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Service;
use App\Models\Share;
use App\Models\User;
use App\Models\Category;
use App\Models\SubCategory;
use App\Models\ServiceCategory;
use App\Models\ServiceSubCategory;
use App\Models\ShareCategory;

class SitemapController extends Controller
{
    /**
     * Generate sitemap.xml
     */
    public function index()
    {
        $urls = [];

        // ===== STATIC PAGES =====
        
        // Home page
        $urls[] = [
            'loc' => url('/'),
            'lastmod' => now()->toAtomString(),
            'changefreq' => 'daily',
            'priority' => '1.0'
        ];

        // Products index page
        $urls[] = [
            'loc' => route('products.index'),
            'lastmod' => now()->toAtomString(),
            'changefreq' => 'hourly',
            'priority' => '0.9'
        ];

        // Services index page
        $urls[] = [
            'loc' => route('services.index'),
            'lastmod' => now()->toAtomString(),
            'changefreq' => 'hourly',
            'priority' => '0.9'
        ];

        // Contact page
        $urls[] = [
            'loc' => route('contact.index'),
            'lastmod' => now()->toAtomString(),
            'changefreq' => 'monthly',
            'priority' => '0.7'
        ];

        // FAQs page
        $urls[] = [
            'loc' => route('faqs.index'),
            'lastmod' => now()->toAtomString(),
            'changefreq' => 'weekly',
            'priority' => '0.7'
        ];

        // Terms of Service page
        $urls[] = [
            'loc' => route('terms-of-service.index'),
            'lastmod' => now()->toAtomString(),
            'changefreq' => 'monthly',
            'priority' => '0.5'
        ];

        // Shares index page
        $urls[] = [
            'loc' => route('shares.index'),
            'lastmod' => now()->toAtomString(),
            'changefreq' => 'daily',
            'priority' => '0.8'
        ];

        // Auth pages
        $urls[] = [
            'loc' => route('sign-in'),
            'lastmod' => now()->toAtomString(),
            'changefreq' => 'monthly',
            'priority' => '0.5'
        ];

        $urls[] = [
            'loc' => route('sign-up'),
            'lastmod' => now()->toAtomString(),
            'changefreq' => 'monthly',
            'priority' => '0.5'
        ];

        // ===== CATEGORY PAGES =====
        
        // Product Categories
        $categories = Category::active()->get();
        foreach ($categories as $category) {
            $urls[] = [
                'loc' => route('products.index', ['category' => $category->slug]),
                'lastmod' => $category->updated_at->toAtomString(),
                'changefreq' => 'daily',
                'priority' => '0.8'
            ];
        }

        // Product SubCategories
        $subCategories = SubCategory::active()->get();
        foreach ($subCategories as $subCategory) {
            $urls[] = [
                'loc' => route('products.index', ['subcategory' => $subCategory->slug]),
                'lastmod' => $subCategory->updated_at->toAtomString(),
                'changefreq' => 'daily',
                'priority' => '0.7'
            ];
        }

        // Service Categories
        $serviceCategories = ServiceCategory::active()->get();
        foreach ($serviceCategories as $serviceCategory) {
            $urls[] = [
                'loc' => route('services.index', ['category' => $serviceCategory->slug]),
                'lastmod' => $serviceCategory->updated_at->toAtomString(),
                'changefreq' => 'daily',
                'priority' => '0.8'
            ];
        }

        // Service SubCategories
        $serviceSubCategories = ServiceSubCategory::active()->get();
        foreach ($serviceSubCategories as $serviceSubCategory) {
            $urls[] = [
                'loc' => route('services.index', ['subcategory' => $serviceSubCategory->slug]),
                'lastmod' => $serviceSubCategory->updated_at->toAtomString(),
                'changefreq' => 'daily',
                'priority' => '0.7'
            ];
        }

        // Share Categories
        $shareCategories = ShareCategory::active()->get();
        foreach ($shareCategories as $shareCategory) {
            $urls[] = [
                'loc' => route('shares.index', ['category' => $shareCategory->slug]),
                'lastmod' => $shareCategory->updated_at->toAtomString(),
                'changefreq' => 'daily',
                'priority' => '0.7'
            ];
        }

        // ===== DYNAMIC PAGES =====
        
        // Product detail pages
        $products = Product::visibleToClient()->orderBy('updated_at', 'desc')->get();
        foreach ($products as $product) {
            $urls[] = [
                'loc' => route('products.show', $product->slug),
                'lastmod' => $product->updated_at->toAtomString(),
                'changefreq' => 'daily',
                'priority' => '0.8'
            ];
        }

        // Service detail pages
        $services = Service::visibleToClient()->orderBy('updated_at', 'desc')->get();
        foreach ($services as $service) {
            $urls[] = [
                'loc' => route('services.show', $service->slug),
                'lastmod' => $service->updated_at->toAtomString(),
                'changefreq' => 'daily',
                'priority' => '0.8'
            ];
        }

        // Share (Blog) detail pages
        $shares = Share::approved()->orderBy('updated_at', 'desc')->get();
        foreach ($shares as $share) {
            $urls[] = [
                'loc' => route('shares.show', $share->slug),
                'lastmod' => $share->updated_at->toAtomString(),
                'changefreq' => 'weekly',
                'priority' => '0.7'
            ];
        }

        // Seller profile pages
        $sellers = User::where('role', 'seller')
            ->whereNull('seller_banned_at')
            ->get();
        foreach ($sellers as $seller) {
            $urls[] = [
                'loc' => route('seller.profile', $seller->full_name),
                'lastmod' => $seller->updated_at->toAtomString(),
                'changefreq' => 'weekly',
                'priority' => '0.6'
            ];
        }

        $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        $xml .= view('sitemap.index', compact('urls'))->render();
        
        return response($xml, 200)
            ->header('Content-Type', 'application/xml');
    }

    /**
     * Generate robots.txt
     */
    public function robots()
    {
        $sitemapUrl = url('sitemap.xml');
        
        $content = "User-agent: *\n";
        $content .= "Allow: /\n\n";
        
        $content .= "# Sitemap\n";
        $content .= "Sitemap: {$sitemapUrl}\n\n";
        
        $content .= "# Allow main pages\n";
        $content .= "Allow: /\n";
        $content .= "Allow: /products\n";
        $content .= "Allow: /products/*\n";
        $content .= "Allow: /services\n";
        $content .= "Allow: /services/*\n";
        $content .= "Allow: /shares\n";
        $content .= "Allow: /shares/*\n";
        $content .= "Allow: /shop/*\n";
        $content .= "Allow: /contact\n";
        $content .= "Allow: /faqs\n";
        $content .= "Allow: /terms-of-service\n";
        $content .= "Allow: /sign-in\n";
        $content .= "Allow: /sign-up\n\n";
        
        $content .= "# Disallow admin area\n";
        $content .= "Disallow: /admin/\n";
        $content .= "Disallow: /admin\n\n";
        
        $content .= "# Disallow seller management area\n";
        $content .= "Disallow: /seller/\n";
        $content .= "Disallow: /seller\n\n";
        
        $content .= "# Disallow private/auth pages\n";
        $content .= "Disallow: /profile\n";
        $content .= "Disallow: /orders\n";
        $content .= "Disallow: /orders/*\n";
        $content .= "Disallow: /deposit\n";
        $content .= "Disallow: /withdrawal\n";
        $content .= "Disallow: /favorites\n";
        $content .= "Disallow: /logout\n";
        $content .= "Disallow: /forgot-password\n";
        $content .= "Disallow: /verify-email/*\n";
        $content .= "Disallow: /reset-password/*\n";
        $content .= "Disallow: /2fa/*\n";
        $content .= "Disallow: /security/*\n";
        $content .= "Disallow: /shares/manage\n";
        $content .= "Disallow: /shares/manage/*\n\n";
        
        $content .= "# Disallow API endpoints\n";
        $content .= "Disallow: /api/*\n";
        $content .= "Disallow: /deposit/callback\n";
        $content .= "Disallow: /deposit/sse\n";
        $content .= "Disallow: /deposit/check-status\n\n";
        
        $content .= "# Disallow storage and assets\n";
        $content .= "Disallow: /storage/\n";
        $content .= "Disallow: /hot\n";
        
        return response($content, 200)->header('Content-Type', 'text/plain');
    }
}
