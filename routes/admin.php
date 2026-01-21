<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Artisan;
use App\Http\Controllers\Admin\SeoController;
use App\Http\Controllers\Admin\BankController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\ServiceOrderController;
use App\Http\Controllers\Admin\RefundController;
use App\Http\Controllers\Admin\SellerController;
use App\Http\Controllers\Admin\SocialController;
use App\Http\Controllers\Admin\DisputeController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\LogoSiteController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\SubCategoryController;
use App\Http\Controllers\Admin\GoogleSettingController;
use App\Http\Controllers\Admin\WithdrawalController;
use App\Http\Controllers\Admin\ConfigController;
use App\Http\Controllers\Admin\ManualWalletAdjustmentController;
use App\Http\Controllers\Admin\ServiceDisputeController;
use App\Http\Controllers\Admin\FooterContentController;
use App\Http\Controllers\Admin\ContactLinkController;
use App\Http\Controllers\Admin\ContactSubmissionController;
use App\Http\Controllers\Admin\DepositController;
use App\Http\Controllers\Admin\FAQController;
use App\Http\Controllers\Admin\ShareController;
use App\Http\Controllers\Admin\ShareCategoryController;
use App\Http\Controllers\Admin\TermOfServiceController;
use App\Http\Controllers\Admin\ServiceCategoryController;
use App\Http\Controllers\Admin\ServiceSubCategoryController;
use App\Http\Controllers\Admin\ServiceController;
use App\Http\Controllers\Admin\HeaderConfigController;
use App\Http\Controllers\Admin\AuctionController;
use App\Http\Controllers\Admin\FeaturedHistoryController;

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
        Route::get('/dashboard/chart-data', [DashboardController::class, 'chartData'])->name('dashboard.chart-data');

        Route::get('logo-site', [LogoSiteController::class, 'edit'])->name('logo-site.edit');
        Route::put('logo-site', [LogoSiteController::class, 'update'])->name('logo-site.update');
        Route::delete('logo-site/delete-logo', [LogoSiteController::class, 'deleteLogo'])->name('logo-site.delete-logo');
        Route::delete('logo-site/delete-favicon', [LogoSiteController::class, 'deleteFavicon'])->name('logo-site.delete-favicon');

        Route::resource('socials', SocialController::class)->except(['show']);

        Route::get('setting', [SettingController::class, 'index'])->name('setting.index');
        Route::put('setting/smtp', [SettingController::class, 'updateSMTP'])->name('setting.update.smtp');
        Route::put('setting/google', [GoogleSettingController::class, 'updateGoogle'])->name('setting.update.google');

        Route::resource('seo', SeoController::class)->except(['show', 'create', 'store', 'destroy']);

        // Configs
        Route::get('configs', [ConfigController::class, 'index'])->name('configs.index');
        Route::put('configs/bulk-update', [ConfigController::class, 'bulkUpdate'])->name('configs.bulk-update');
        Route::put('configs/{config}', [ConfigController::class, 'update'])->name('configs.update');

        Route::get('categories', [CategoryController::class, 'index'])->name('categories.index');
        Route::post('categories', [CategoryController::class, 'store'])->name('categories.store');
        Route::put('categories/{category:slug}', [CategoryController::class, 'update'])->name('categories.update');
        Route::delete('categories/{category:slug}', [CategoryController::class, 'destroy'])->name('categories.destroy');
        
        // SubCategories
        Route::get('subcategories', [SubCategoryController::class, 'index'])->name('subcategories.index');
        Route::post('subcategories', [SubCategoryController::class, 'store'])->name('subcategories.store');
        Route::put('subcategories/{subcategory:slug}', [SubCategoryController::class, 'update'])->name('subcategories.update');
        Route::delete('subcategories/{subcategory:slug}', [SubCategoryController::class, 'destroy'])->name('subcategories.destroy');

        // Service Categories
        Route::get('service-categories', [ServiceCategoryController::class, 'index'])->name('service-categories.index');
        Route::post('service-categories', [ServiceCategoryController::class, 'store'])->name('service-categories.store');
        Route::put('service-categories/{serviceCategory:slug}', [ServiceCategoryController::class, 'update'])->name('service-categories.update');
        Route::delete('service-categories/{serviceCategory:slug}', [ServiceCategoryController::class, 'destroy'])->name('service-categories.destroy');

        // Service SubCategories
        Route::get('service-subcategories', [ServiceSubCategoryController::class, 'index'])->name('service-subcategories.index');
        Route::post('service-subcategories', [ServiceSubCategoryController::class, 'store'])->name('service-subcategories.store');
        Route::put('service-subcategories/{serviceSubcategory:slug}', [ServiceSubCategoryController::class, 'update'])->name('service-subcategories.update');
        Route::delete('service-subcategories/{serviceSubcategory:slug}', [ServiceSubCategoryController::class, 'destroy'])->name('service-subcategories.destroy');

        // Products
        Route::get('products/pending', [ProductController::class, 'pending'])->name('products.pending');
        Route::get('products/{product:slug}/review', [ProductController::class, 'review'])->name('products.review');
        Route::post('products/{product:slug}/approve', [ProductController::class, 'approve'])->name('products.approve');
        Route::post('products/{product:slug}/reject', [ProductController::class, 'reject'])->name('products.reject');
        Route::post('products/{product:slug}/ban', [ProductController::class, 'ban'])->name('products.ban');
        Route::post('products/{product:slug}/unban', [ProductController::class, 'unban'])->name('products.unban');
        Route::get('products', [ProductController::class, 'index'])->name('products.index');
        Route::get('products/{product:slug}', [ProductController::class, 'show'])->name('products.show');

        // Services
        Route::get('services/pending', [ServiceController::class, 'pending'])->name('services.pending');
        Route::get('services/{service:slug}/review', [ServiceController::class, 'review'])->name('services.review');
        Route::post('services/{service:slug}/approve', [ServiceController::class, 'approve'])->name('services.approve');
        Route::post('services/{service:slug}/reject', [ServiceController::class, 'reject'])->name('services.reject');
        Route::post('services/{service:slug}/ban', [ServiceController::class, 'ban'])->name('services.ban');
        Route::post('services/{service:slug}/unban', [ServiceController::class, 'unban'])->name('services.unban');
        Route::get('services', [ServiceController::class, 'index'])->name('services.index');
        Route::get('services/{service:slug}', [ServiceController::class, 'show'])->name('services.show');

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

        // Refunds
        Route::get('refunds', [RefundController::class, 'index'])->name('refunds.index');
        Route::get('refunds/{refund:slug}', [RefundController::class, 'show'])->name('refunds.show');
        Route::post('refunds/{refund:slug}/approve', [RefundController::class, 'approve'])->name('refunds.approve');
        Route::post('refunds/{refund:slug}/reject', [RefundController::class, 'reject'])->name('refunds.reject');

        // Disputes
        Route::get('disputes', [DisputeController::class, 'index'])->name('disputes.index');
        Route::get('disputes/{dispute:slug}', [DisputeController::class, 'show'])->name('disputes.show');
        Route::post('disputes/{dispute:slug}/approve', [DisputeController::class, 'approve'])->name('disputes.approve');
        Route::post('disputes/{dispute:slug}/reject', [DisputeController::class, 'reject'])->name('disputes.reject');

        // Orders
        Route::get('orders', [OrderController::class, 'index'])->name('orders.index');
        Route::get('orders/{order:slug}', [OrderController::class, 'show'])->name('orders.show');
        
        // Service Orders
        Route::get('service-orders', [ServiceOrderController::class, 'index'])->name('service-orders.index');
        Route::get('service-orders/{serviceOrder:slug}', [ServiceOrderController::class, 'show'])->name('service-orders.show');
        
        // Service Disputes
        Route::get('service-disputes', [ServiceDisputeController::class, 'index'])->name('service-disputes.index');
        Route::get('service-disputes/{dispute:slug}', [ServiceDisputeController::class, 'show'])->name('service-disputes.show');
        Route::post('service-disputes/{dispute:slug}/accept', [ServiceDisputeController::class, 'accept'])->name('service-disputes.accept');
        Route::post('service-disputes/{dispute:slug}/reject', [ServiceDisputeController::class, 'reject'])->name('service-disputes.reject');
    
        Route::resource('banks', BankController::class)->except(['create', 'edit', 'show']);
    
        // Deposits
        Route::get('deposits', [DepositController::class, 'index'])->name('deposits.index');
        Route::get('deposits/{deposit:slug}', [DepositController::class, 'show'])->name('deposits.show');

        // Withdrawals
        Route::get('withdrawals', [WithdrawalController::class, 'index'])->name('withdrawals.index');
        Route::get('withdrawals/{withdrawal:slug}', [WithdrawalController::class, 'show'])->name('withdrawals.show');
        Route::post('withdrawals/{withdrawal:slug}/process', [WithdrawalController::class, 'process'])->name('withdrawals.process');
        Route::post('withdrawals/{withdrawal:slug}/complete', [WithdrawalController::class, 'complete'])->name('withdrawals.complete');
        Route::post('withdrawals/{withdrawal:slug}/reject', [WithdrawalController::class, 'reject'])->name('withdrawals.reject');

        // Manual Wallet Adjustments
        Route::get('manual-wallet-adjustments', [ManualWalletAdjustmentController::class, 'index'])->name('manual-wallet-adjustments.index');
        Route::get('manual-wallet-adjustments/create', [ManualWalletAdjustmentController::class, 'create'])->name('manual-wallet-adjustments.create');
        Route::post('manual-wallet-adjustments', [ManualWalletAdjustmentController::class, 'store'])->name('manual-wallet-adjustments.store');
        Route::get('manual-wallet-adjustments/search-users', [ManualWalletAdjustmentController::class, 'searchUsers'])->name('manual-wallet-adjustments.search-users');
        Route::get('manual-wallet-adjustments/{manualWalletAdjustment:slug}', [ManualWalletAdjustmentController::class, 'show'])->name('manual-wallet-adjustments.show');

        // Footer Content
        Route::get('footer-contents', [FooterContentController::class, 'index'])->name('footer-contents.index');
        Route::put('footer-contents/{footerContent}', [FooterContentController::class, 'update'])->name('footer-contents.update');

        // Terms of Service
        Route::get('terms-of-service', [TermOfServiceController::class, 'index'])->name('terms-of-service.index');
        Route::put('terms-of-service/{termOfService}', [TermOfServiceController::class, 'update'])->name('terms-of-service.update');

        // Header Configs
        Route::get('header-configs', [HeaderConfigController::class, 'index'])->name('header-configs.index');
        Route::post('header-configs/update-support-bar', [HeaderConfigController::class, 'updateSupportBar'])->name('header-configs.update-support-bar');
        Route::post('header-configs/update-promotional-banner', [HeaderConfigController::class, 'updatePromotionalBanner'])->name('header-configs.update-promotional-banner');
        Route::post('header-configs/update-search-background', [HeaderConfigController::class, 'updateSearchBackground'])->name('header-configs.update-search-background');

        // Contact Links
        Route::resource('contact-links', ContactLinkController::class)->except(['show']);

        // Contact Submissions
        Route::get('contact-submissions', [ContactSubmissionController::class, 'index'])->name('contact-submissions.index');
        Route::get('contact-submissions/{contactSubmission}', [ContactSubmissionController::class, 'show'])->name('contact-submissions.show');
        Route::put('contact-submissions/{contactSubmission}', [ContactSubmissionController::class, 'update'])->name('contact-submissions.update');
        Route::post('contact-submissions/{contactSubmission}/mark-read', [ContactSubmissionController::class, 'markAsRead'])->name('contact-submissions.mark-read');

        // FAQs
        Route::resource('faqs', FAQController::class)->except(['show']);

        // Share Categories
        Route::resource('share-categories', ShareCategoryController::class)->except(['show', 'create', 'edit']);

        // Shares
        Route::get('shares', [ShareController::class, 'index'])->name('shares.index');
        Route::get('shares/create', [ShareController::class, 'create'])->name('shares.create');
        Route::post('shares', [ShareController::class, 'store'])->name('shares.store');
        Route::get('shares/{share:slug}', [ShareController::class, 'show'])->name('shares.show');
        Route::get('shares/{share:slug}/edit', [ShareController::class, 'edit'])->name('shares.edit');
        Route::put('shares/{share:slug}', [ShareController::class, 'update'])->name('shares.update');
        Route::delete('shares/{share:slug}', [ShareController::class, 'destroy'])->name('shares.destroy');
        Route::post('shares/{share:slug}/approve', [ShareController::class, 'approve'])->name('shares.approve');
        Route::post('shares/{share:slug}/reject', [ShareController::class, 'reject'])->name('shares.reject');
        Route::post('shares/upload-image', [ShareController::class, 'uploadImage'])->name('shares.upload-image');

        // Auctions
        Route::resource('auctions', AuctionController::class);
        Route::post('auctions/{auction:slug}/start', [AuctionController::class, 'start'])->name('auctions.start');
        Route::post('auctions/{auction:slug}/cancel', [AuctionController::class, 'cancel'])->name('auctions.cancel');

        // Featured Histories
        Route::get('featured-histories', [FeaturedHistoryController::class, 'index'])->name('featured-histories.index');
        Route::get('featured-histories/{featuredHistory:slug}', [FeaturedHistoryController::class, 'show'])->name('featured-histories.show');
    });
});
