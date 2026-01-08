<?php

namespace App\Providers;

use App\Models\ProductVariant;
use App\Models\Promotion;
use App\Observers\ProductVariantObserver;
use App\Observers\PromotionObserver;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Schema::defaultStringLength(191);

        $logoSite = null;
        try {
            if (Schema::hasTable('logo_sites')) {
                $logoSite = \App\Models\LogoSite::first();
            }
        } catch (\Exception $e) {
        }

        $logoPath = $logoSite && $logoSite->logo
            ? Storage::url($logoSite->logo)
            : asset('images/logo/logo-site.webp');

        $faviconPath = $logoSite && $logoSite->favicon
            ? Storage::url($logoSite->favicon)
            : asset('favicon.ico');

        view()->share('faviconPath', $faviconPath);
        view()->share('logoPath', $logoPath);
    }
}
