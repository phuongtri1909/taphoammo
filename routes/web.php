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


Route::post('/deposit/callback', [DepositController::class, 'callback'])->name('deposit.callback');

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/api/categories', [HomeController::class, 'getCategories'])->name('api.categories');
Route::get('/products', [ProductController::class, 'index'])->name('products.index');
Route::get('/products/{product:slug}', [ProductController::class, 'show'])->name('products.show');
Route::get('/services', [ServiceController::class, 'index'])->name('services.index');

Route::get('/shop/{sellerSlug}', [SellerProfileController::class, 'show'])->name('seller.profile');

Route::get('/2fa/verify', [TwoFactorController::class, 'showVerifyForm'])->name('2fa.verify');
Route::post('/2fa/verify', [TwoFactorController::class, 'verify'])->name('2fa.verify.post');

Route::group(['middleware' => ['auth', 'user.active']], function () {
    Route::get('logout', [AuthController::class, 'logout'])->name('logout');
    Route::post('/products/buy', [ProductController::class, 'buy'])->name('products.buy');
    Route::get('/profile', [ProfileController::class, 'index'])->name('profile.index');
    Route::get('/profile/transactions', [ProfileController::class, 'transactions'])->name('profile.transactions');

    // Orders
    Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/{order:slug}', [OrderController::class, 'show'])->name('orders.show');
    Route::post('/orders/{order:slug}/dispute', [OrderController::class, 'createDispute'])->name('orders.dispute');
    Route::post('/orders/{order:slug}/confirm', [OrderController::class, 'confirmOrder'])->name('orders.confirm');
    Route::post('/disputes/{dispute:slug}/withdraw', [OrderController::class, 'withdrawDispute'])->name('disputes.withdraw');
    
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
});


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

