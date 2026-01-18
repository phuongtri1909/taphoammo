<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Artisan;
use App\Http\Controllers\Admin\SocialController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Admin\LogoSiteController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\SeoController;
use App\Http\Controllers\Admin\GoogleSettingController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\SubCategoryController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\SellerController;

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

        Route::get('categories', [CategoryController::class, 'index'])->name('categories.index');
        Route::post('categories', [CategoryController::class, 'store'])->name('categories.store');
        Route::put('categories/{category:slug}', [CategoryController::class, 'update'])->name('categories.update');
        Route::delete('categories/{category:slug}', [CategoryController::class, 'destroy'])->name('categories.destroy');
        
        // SubCategories
        Route::get('subcategories', [SubCategoryController::class, 'index'])->name('subcategories.index');
        Route::post('subcategories', [SubCategoryController::class, 'store'])->name('subcategories.store');
        Route::put('subcategories/{subcategory:slug}', [SubCategoryController::class, 'update'])->name('subcategories.update');
        Route::delete('subcategories/{subcategory:slug}', [SubCategoryController::class, 'destroy'])->name('subcategories.destroy');

        // Products
        Route::get('products/pending', [ProductController::class, 'pending'])->name('products.pending');
        Route::get('products/{product:slug}/review', [ProductController::class, 'review'])->name('products.review');
        Route::post('products/{product:slug}/approve', [ProductController::class, 'approve'])->name('products.approve');
        Route::post('products/{product:slug}/reject', [ProductController::class, 'reject'])->name('products.reject');
        Route::post('products/{product:slug}/ban', [ProductController::class, 'ban'])->name('products.ban');
        Route::post('products/{product:slug}/unban', [ProductController::class, 'unban'])->name('products.unban');
        Route::get('products', [ProductController::class, 'index'])->name('products.index');
        Route::get('products/{product:slug}', [ProductController::class, 'show'])->name('products.show');

        // Sellers Management
        Route::get('sellers', [SellerController::class, 'index'])->name('sellers.index');
        Route::get('sellers/{seller:full_name}', [SellerController::class, 'show'])->name('sellers.show');
        Route::post('sellers/{seller:full_name}/ban', [SellerController::class, 'ban'])->name('sellers.ban');
        Route::post('sellers/{seller:full_name}/unban', [SellerController::class, 'unban'])->name('sellers.unban');
        
        // Seller Registrations
        Route::get('seller-registrations', [SellerController::class, 'pendingRegistrations'])->name('seller-registrations.index');
        Route::get('seller-registrations/{registration:slug}', [SellerController::class, 'reviewRegistration'])->name('seller-registrations.review');
        Route::post('seller-registrations/{registration:slug}/approve', [SellerController::class, 'approveRegistration'])->name('seller-registrations.approve');
        Route::post('seller-registrations/{registration:slug}/reject', [SellerController::class, 'rejectRegistration'])->name('seller-registrations.reject');
    });
});
