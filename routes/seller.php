<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Artisan;
use App\Http\Controllers\Admin\SocialController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Admin\LogoSiteController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\SeoController;
use App\Http\Controllers\Admin\GoogleSettingController;

Route::group(['as' => 'seller.'], function () {

    Route::group(['middleware' => ['auth', 'check.role:seller']], function () {
        Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    });
});
