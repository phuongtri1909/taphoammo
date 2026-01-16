<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Seller\SellerDashboardController;
use App\Http\Controllers\Seller\ProductController;

Route::group(['as' => 'seller.'], function () {

    Route::group(['middleware' => ['auth', 'check.role:seller']], function () {
        Route::get('/', [SellerDashboardController::class, 'index'])->name('dashboard');

        // Products
        Route::get('products', [ProductController::class, 'index'])->name('products.index');
        Route::get('products/create', [ProductController::class, 'create'])->name('products.create');
        Route::post('products', [ProductController::class, 'store'])->name('products.store');
        Route::get('products/{product:slug}', [ProductController::class, 'show'])->name('products.show');
        Route::get('products/{product:slug}/edit', [ProductController::class, 'edit'])->name('products.edit');
        Route::put('products/{product:slug}', [ProductController::class, 'update'])->name('products.update');
        Route::delete('products/{product:slug}', [ProductController::class, 'destroy'])->name('products.destroy');
        Route::patch('products/{product:slug}/status', [ProductController::class, 'updateStatus'])->name('products.update-status');
        
        Route::patch('variants/{variant:slug}/status', [ProductController::class, 'updateVariantStatus'])->name('products.update-variant-status');
        
        Route::post('variants/{variant:slug}/values', [ProductController::class, 'storeValues'])->name('products.store-values');
        Route::put('values/{value:slug}', [ProductController::class, 'updateValue'])->name('products.update-value');
        Route::delete('values/{value:slug}', [ProductController::class, 'destroyValue'])->name('products.destroy-value');
    });
});
