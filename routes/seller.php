<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Seller\SellerDashboardController;

Route::group(['as' => 'seller.'], function () {

    Route::group(['middleware' => ['auth', 'check.role:seller']], function () {
        Route::get('/', [SellerDashboardController::class, 'index'])->name('dashboard');
    });
});
