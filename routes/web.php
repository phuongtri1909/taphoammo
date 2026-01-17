<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Client\AuthController;
use App\Http\Controllers\Client\AuthGoogleController;
use App\Http\Controllers\Client\HomeController;
use App\Http\Controllers\Client\ProductController;
use App\Http\Controllers\Client\ServiceController;

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/api/categories', [HomeController::class, 'getCategories'])->name('api.categories');
Route::get('/products', [ProductController::class, 'index'])->name('products.index');
Route::get('/products/{product:slug}', [ProductController::class, 'show'])->name('products.show');
Route::get('/services', [ServiceController::class, 'index'])->name('services.index');

Route::group(['middleware' => 'auth'], function () {
    Route::get('logout', [AuthController::class, 'logout'])->name('logout');
    Route::post('/products/buy', [ProductController::class, 'buy'])->name('products.buy');
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

