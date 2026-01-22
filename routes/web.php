<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Client\AuthController;
use App\Http\Controllers\Client\HomeController;
use App\Http\Controllers\Client\OrderController;
use App\Http\Controllers\Client\DepositController;
use App\Http\Controllers\Client\WithdrawalController;
use App\Http\Controllers\Client\ProductController;
use App\Http\Controllers\Client\ProfileController;
use App\Http\Controllers\Client\ServiceController;
use App\Http\Controllers\Client\TwoFactorController;
use App\Http\Controllers\Client\AuthGoogleController;
use App\Http\Controllers\Client\SellerProfileController;
use App\Http\Controllers\Client\SellerRegistrationController;
use App\Http\Controllers\Client\ContactController;
use App\Http\Controllers\Client\FAQController;
use App\Http\Controllers\Client\ShareController;
use App\Http\Controllers\Client\PublicShareController;
use App\Http\Controllers\Client\FavoriteController;
use App\Http\Controllers\Client\TermOfServiceController;
use App\Http\Controllers\Client\SitemapController;
use App\Http\Controllers\Client\ReviewController;

// Sitemap and Robots
Route::get('sitemap.xml', [SitemapController::class, 'index'])->name('sitemap');
Route::get('robots.txt', [SitemapController::class, 'robots'])->name('robots');

Route::post('/deposit/callback', [DepositController::class, 'callback'])->name('deposit.callback');

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/api/categories', [HomeController::class, 'getCategories'])->name('api.categories');
Route::get('/products', [ProductController::class, 'index'])->name('products.index');
Route::get('/products/{product:slug}', [ProductController::class, 'show'])->name('products.show');
Route::get('/services', [ServiceController::class, 'index'])->name('services.index');
Route::get('/services/{service:slug}', [ServiceController::class, 'show'])->name('services.show');

// Public Reviews API
Route::get('/api/products/{product}/reviews', [ReviewController::class, 'getProductReviews'])->name('api.products.reviews');
Route::get('/api/products/{product}/review-stats', [ReviewController::class, 'getProductReviewStats'])->name('api.products.review-stats');
Route::get('/api/services/{service}/reviews', [ReviewController::class, 'getServiceReviews'])->name('api.services.reviews');
Route::get('/api/services/{service}/review-stats', [ReviewController::class, 'getServiceReviewStats'])->name('api.services.review-stats');

Route::get('/contact', [ContactController::class, 'index'])->name('contact.index');
Route::post('/contact', [ContactController::class, 'store'])->name('contact.store');

Route::get('/faqs', [FAQController::class, 'index'])->name('faqs.index');

Route::get('/terms-of-service', [TermOfServiceController::class, 'index'])->name('terms-of-service.index');
Route::get('/api/terms-of-service/summary', [TermOfServiceController::class, 'getSummary'])->name('terms-of-service.get-summary');

// Public Shares - Note: Keep manage routes before {slug} route to avoid conflicts
Route::get('/shares', [PublicShareController::class, 'index'])->name('shares.index');

Route::get('/shop/{sellerSlug}', [SellerProfileController::class, 'show'])->name('seller.profile');

Route::get('/2fa/verify', [TwoFactorController::class, 'showVerifyForm'])->name('2fa.verify');
Route::post('/2fa/verify', [TwoFactorController::class, 'verify'])->name('2fa.verify.post');

Route::group(['middleware' => ['auth', 'user.active']], function () {
    Route::get('logout', [AuthController::class, 'logout'])->name('logout');
    Route::post('/products/buy', [ProductController::class, 'buy'])->name('products.buy');
    Route::post('/services/buy', [ServiceController::class, 'buy'])->name('services.buy');
    Route::get('/profile', [ProfileController::class, 'index'])->name('profile.index');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::get('/profile/transactions', [ProfileController::class, 'transactions'])->name('profile.transactions');
    Route::get('/profile/change-password', [ProfileController::class, 'showChangePassword'])->name('profile.change-password');
    Route::post('/profile/change-password', [ProfileController::class, 'updatePassword'])->name('profile.change-password.post');

    // Favorites
    Route::post('/favorites/toggle', [FavoriteController::class, 'toggle'])->name('favorites.toggle');
    Route::get('/favorites', [FavoriteController::class, 'index'])->name('favorites.index');

    // Orders
    Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/{slug}', [OrderController::class, 'show'])->name('orders.show');
    Route::post('/orders/{order:slug}/dispute', [OrderController::class, 'createDispute'])->name('orders.dispute');
    Route::post('/orders/{order:slug}/confirm', [OrderController::class, 'confirmOrder'])->name('orders.confirm');
    Route::post('/disputes/{dispute:slug}/withdraw', [OrderController::class, 'withdrawDispute'])->name('disputes.withdraw');
    
    // Service Orders
    Route::post('/service-orders/{serviceOrder:slug}/confirm', [OrderController::class, 'confirmServiceOrder'])->name('service-orders.confirm');
    Route::post('/service-orders/{serviceOrder:slug}/dispute', [OrderController::class, 'createServiceDispute'])->name('service-orders.dispute');
    Route::post('/service-disputes/{dispute:slug}/withdraw', [OrderController::class, 'withdrawServiceDispute'])->name('service-disputes.withdraw');
    
    // Reviews
    Route::post('/orders/{order:slug}/review', [ReviewController::class, 'storeProductReview'])->name('orders.review');
    Route::post('/service-orders/{serviceOrder:slug}/review', [ReviewController::class, 'storeServiceReview'])->name('service-orders.review');
    
    // Product Values
    Route::get('/product-values/{value:slug}/data', [OrderController::class, 'getValueData'])->name('product-values.data');

    // Deposit (Nạp tiền)
    Route::get('/deposit', [DepositController::class, 'index'])->name('deposit.index');
    Route::post('/deposit', [DepositController::class, 'store'])->name('deposit.store');
    Route::get('/deposit/sse', [DepositController::class, 'sseTransactionUpdates'])->name('deposit.sse');
    Route::get('/deposit/check-status', [DepositController::class, 'checkStatus'])->name('deposit.check-status');

    // Withdrawal (Rút tiền - chỉ cho seller)
    Route::get('/withdrawal', [WithdrawalController::class, 'index'])->name('withdrawal.index');
    Route::post('/withdrawal', [WithdrawalController::class, 'store'])->name('withdrawal.store');
    Route::post('/withdrawal/{withdrawal:slug}/verify-otp', [WithdrawalController::class, 'verifyOtp'])->name('withdrawal.verify-otp');
    Route::post('/withdrawal/{withdrawal:slug}/resend-otp', [WithdrawalController::class, 'resendOtp'])->name('withdrawal.resend-otp');
    Route::post('/withdrawal/{withdrawal:slug}/cancel', [WithdrawalController::class, 'cancel'])->name('withdrawal.cancel');
    Route::post('/withdrawal/clear-bank-info', [WithdrawalController::class, 'clearBankInfo'])->name('withdrawal.clear-bank-info');

    Route::prefix('security')->group(function () {
        Route::get('/two-factor', [TwoFactorController::class, 'index'])->name('security.two-factor');
        Route::post('/two-factor/enable', [TwoFactorController::class, 'enable'])->name('2fa.enable');
        Route::post('/two-factor/confirm', [TwoFactorController::class, 'confirm'])->name('2fa.confirm');
        Route::post('/two-factor/disable', [TwoFactorController::class, 'disable'])->name('2fa.disable');
        Route::post('/two-factor/recovery-codes', [TwoFactorController::class, 'getRecoveryCodes'])->name('2fa.recovery-codes');
        Route::post('/two-factor/regenerate-recovery-codes', [TwoFactorController::class, 'regenerateRecoveryCodes'])->name('2fa.regenerate-recovery-codes');
    });

    Route::prefix('seller')->group(function () {
        Route::get('/register', [SellerRegistrationController::class, 'create'])->name('seller.register');
        Route::post('/register', [SellerRegistrationController::class, 'store'])->name('seller.register.store');
    });

    Route::middleware('check.role:seller,admin')->prefix('shares/manage')->name('shares.manage.')->group(function () {
        Route::get('/', [ShareController::class, 'index'])->name('index');
        Route::get('/create', [ShareController::class, 'create'])->name('create');
        Route::post('/', [ShareController::class, 'store'])->name('store');
        Route::get('/{share:slug}/edit', [ShareController::class, 'edit'])->name('edit');
        Route::put('/{share:slug}', [ShareController::class, 'update'])->name('update');
        Route::delete('/{share:slug}', [ShareController::class, 'destroy'])->name('destroy');
        Route::post('/{share:slug}/toggle-visibility', [ShareController::class, 'toggleVisibility'])->name('toggle-visibility');
        Route::post('/upload-image', [ShareController::class, 'uploadImage'])->name('upload-image');
    });
});

Route::get('/shares/{slug}', [PublicShareController::class, 'show'])->name('shares.show');


Route::group(['middleware' => 'guest'], function () {
    Route::get('sign-in', function () {
        return view('client.pages.auth.login');
    })->name('sign-in');

    Route::post('sign-in', [AuthController::class, 'login'])->name('sign-in.post');

    Route::get('sign-up', function () {
        return view('client.pages.auth.register');
    })->name('sign-up');

    Route::post('sign-up', [AuthController::class, 'register'])->name('sign-up.post');

    Route::get('forgot-password', function () {
        return view('client.pages.auth.forgot-password');
    })->name('forgot-password');

    Route::post('forgot-password', [AuthController::class, 'forgotPassword'])->name('forgot-password.post');

    Route::get('verify-email/{token}', [AuthController::class, 'verifyEmail'])->name('verify-email');

    Route::get('reset-password/{token}', [AuthController::class, 'showResetPasswordForm'])->name('reset-password');
    Route::post('reset-password', [AuthController::class, 'resetPassword'])->name('reset-password.post');

    Route::get('auth/google', [AuthGoogleController::class, 'redirectToGoogle'])->name('login.google');
    Route::get('auth/google/callback', [AuthGoogleController::class, 'handleGoogleCallback'])->name('auth.google.callback');
});

