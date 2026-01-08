<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Artisan;
use App\Http\Controllers\Admin\SocialController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Admin\LogoSiteController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\SeoController;
use App\Http\Controllers\Admin\GoogleSettingController;

Route::group(['as' => 'admin.'], function () {
    Route::get('/clear-cache', function () {
        Artisan::call('cache:clear');
        Artisan::call('config:clear');
        Artisan::call('view:clear');
        Artisan::call('route:clear');
        return 'Cache cleared';
    })->name('clear.cache');

    Route::group(['middleware' => ['auth', 'check.role:admin']], function () {
        Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

        Route::get('logo-site', [LogoSiteController::class, 'edit'])->name('logo-site.edit');
        Route::put('logo-site', [LogoSiteController::class, 'update'])->name('logo-site.update');
        Route::delete('logo-site/delete-logo', [LogoSiteController::class, 'deleteLogo'])->name('logo-site.delete-logo');
        Route::delete('logo-site/delete-favicon', [LogoSiteController::class, 'deleteFavicon'])->name('logo-site.delete-favicon');

        Route::resource('socials', SocialController::class)->except(['show']);

        Route::get('setting', [SettingController::class, 'index'])->name('setting.index');
        Route::put('setting/smtp', [SettingController::class, 'updateSMTP'])->name('setting.update.smtp');
        Route::put('setting/google', [GoogleSettingController::class, 'updateGoogle'])->name('setting.update.google');

        Route::resource('seo', SeoController::class)->except(['show', 'create', 'store', 'destroy']);
    });

    Route::group(['middleware' => 'guest'], function () {
        Route::get('login', function () {
            return view('admin.pages.auth.login');
        })->name('login');
    });
});
