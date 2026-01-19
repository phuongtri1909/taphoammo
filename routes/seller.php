<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Seller\SellerDashboardController;
use App\Http\Controllers\Seller\ProductController;
use App\Http\Controllers\Seller\RefundController;
use App\Http\Controllers\Seller\OrderController;

Route::group(['as' => 'seller.'], function () {

    Route::group(['middleware' => ['auth', 'check.role:seller', 'seller.not.banned']], function () {
        Route::get('/', [SellerDashboardController::class, 'index'])->name('dashboard');

        Route::get('products', [ProductController::class, 'index'])->name('products.index');
        Route::get('products/create', [ProductController::class, 'create'])->name('products.create');
        Route::get('products/{product:slug}', [ProductController::class, 'show'])->name('products.show');
        
        Route::post('products', [ProductController::class, 'store'])->name('products.store');
        Route::get('products/{product:slug}/edit', [ProductController::class, 'edit'])->name('products.edit');
        Route::put('products/{product:slug}', [ProductController::class, 'update'])->name('products.update');
        Route::delete('products/{product:slug}', [ProductController::class, 'destroy'])->name('products.destroy');
        Route::patch('products/{product:slug}/status', [ProductController::class, 'updateStatus'])->name('products.update-status');
        
        Route::patch('variants/{variant:slug}/status', [ProductController::class, 'updateVariantStatus'])->name('products.update-variant-status');
        
        Route::post('variants/{variant:slug}/values', [ProductController::class, 'storeValues'])->name('products.store-values');
        Route::put('values/{value:slug}', [ProductController::class, 'updateValue'])->name('products.update-value');
        Route::delete('values/{value:slug}', [ProductController::class, 'destroyValue'])->name('products.destroy-value');

        // Refunds & Disputes
        Route::get('refunds', [RefundController::class, 'index'])->name('refunds.index');
        Route::get('refunds/{refund:slug}', [RefundController::class, 'showRefund'])->name('refunds.show');
        Route::get('refunds/disputes/{dispute:slug}', [RefundController::class, 'showDispute'])->name('refunds.dispute.show');
        Route::post('refunds/disputes/{dispute:slug}/accept', [RefundController::class, 'acceptDispute'])->name('refunds.dispute.accept');
        Route::post('refunds/disputes/{dispute:slug}/reject', [RefundController::class, 'rejectDispute'])->name('refunds.dispute.reject');

        // Orders
        Route::get('orders', [OrderController::class, 'index'])->name('orders.index');
        Route::get('orders/{order:slug}', [OrderController::class, 'show'])->name('orders.show');
    });
});
