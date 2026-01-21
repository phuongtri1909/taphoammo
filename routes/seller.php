<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Seller\SellerDashboardController;
use App\Http\Controllers\Seller\ProductController;
use App\Http\Controllers\Seller\ServiceController;
use App\Http\Controllers\Seller\RefundController;
use App\Http\Controllers\Seller\OrderController;
use App\Http\Controllers\Seller\ServiceOrderController;
use App\Http\Controllers\Seller\ServiceDisputeController;
use App\Http\Controllers\Seller\FeaturedController;
use App\Http\Controllers\Seller\AuctionController;

Route::group(['as' => 'seller.'], function () {

    Route::group(['middleware' => ['auth', 'check.role:seller', 'seller.not.banned']], function () {
        Route::get('/', [SellerDashboardController::class, 'index'])->name('dashboard');
        Route::get('/dashboard/chart-data', [SellerDashboardController::class, 'chartData'])->name('dashboard.chart-data');

        Route::get('products', [ProductController::class, 'index'])->name('products.index');
        Route::get('products/create', [ProductController::class, 'create'])->name('products.create');
        Route::get('products/{product:slug}', [ProductController::class, 'show'])->name('products.show');
        
        Route::post('products', [ProductController::class, 'store'])->name('products.store');
        Route::get('products/{product:slug}/edit', [ProductController::class, 'edit'])->name('products.edit');
        Route::put('products/{product:slug}', [ProductController::class, 'update'])->name('products.update');
        Route::post('products/{product:slug}/update-image', [ProductController::class, 'updateImage'])->name('products.update-image');
        Route::delete('products/{product:slug}', [ProductController::class, 'destroy'])->name('products.destroy');
        Route::patch('products/{product:slug}/status', [ProductController::class, 'updateStatus'])->name('products.update-status');
        
        Route::patch('variants/{variant:slug}/status', [ProductController::class, 'updateVariantStatus'])->name('products.update-variant-status');
        Route::patch('variants/{variant:slug}/price', [ProductController::class, 'updateVariantPrice'])->name('products.update-variant-price');
        
        Route::post('variants/{variant:slug}/values', [ProductController::class, 'storeValues'])->name('products.store-values');
        Route::put('values/{value:slug}', [ProductController::class, 'updateValue'])->name('products.update-value');
        Route::delete('values/{value:slug}', [ProductController::class, 'destroyValue'])->name('products.destroy-value');

        // Services
        Route::get('services', [ServiceController::class, 'index'])->name('services.index');
        Route::get('services/create', [ServiceController::class, 'create'])->name('services.create');
        Route::get('services/{service:slug}', [ServiceController::class, 'show'])->name('services.show');
        Route::post('services', [ServiceController::class, 'store'])->name('services.store');
        Route::get('services/{service:slug}/edit', [ServiceController::class, 'edit'])->name('services.edit');
        Route::put('services/{service:slug}', [ServiceController::class, 'update'])->name('services.update');
        Route::post('services/{service:slug}/update-image', [ServiceController::class, 'updateImage'])->name('services.update-image');
        Route::delete('services/{service:slug}', [ServiceController::class, 'destroy'])->name('services.destroy');
        Route::patch('services/{service:slug}/status', [ServiceController::class, 'updateStatus'])->name('services.update-status');
        
        Route::patch('service-variants/{variant:slug}/status', [ServiceController::class, 'updateVariantStatus'])->name('services.update-variant-status');
        Route::patch('service-variants/{variant:slug}/price', [ServiceController::class, 'updateVariantPrice'])->name('services.update-variant-price');

        // Refunds & Disputes
        Route::get('refunds', [RefundController::class, 'index'])->name('refunds.index');
        Route::get('refunds/{refund:slug}', [RefundController::class, 'showRefund'])->name('refunds.show');
        Route::get('refunds/disputes/{dispute:slug}', [RefundController::class, 'showDispute'])->name('refunds.dispute.show');
        Route::post('refunds/disputes/{dispute:slug}/accept', [RefundController::class, 'acceptDispute'])->name('refunds.dispute.accept');
        Route::post('refunds/disputes/{dispute:slug}/reject', [RefundController::class, 'rejectDispute'])->name('refunds.dispute.reject');

        // Orders
        Route::get('orders', [OrderController::class, 'index'])->name('orders.index');
        Route::get('orders/{order:slug}', [OrderController::class, 'show'])->name('orders.show');
        
        // Service Orders
        Route::get('service-orders', [ServiceOrderController::class, 'index'])->name('service-orders.index');
        Route::get('service-orders/{serviceOrder:slug}', [ServiceOrderController::class, 'show'])->name('service-orders.show');
        Route::post('service-orders/{serviceOrder:slug}/reject', [ServiceOrderController::class, 'rejectOrder'])->name('service-orders.reject');
        Route::post('service-orders/{serviceOrder:slug}/confirm-completion', [ServiceOrderController::class, 'confirmCompletion'])->name('service-orders.confirm-completion');
        Route::post('service-orders/{serviceOrder:slug}/disputes/{dispute:slug}/accept', [ServiceOrderController::class, 'acceptDispute'])->name('service-orders.disputes.accept');
        Route::post('service-orders/{serviceOrder:slug}/disputes/{dispute:slug}/reject', [ServiceOrderController::class, 'rejectDispute'])->name('service-orders.disputes.reject');

        // Service Disputes
        Route::get('service-disputes', [ServiceDisputeController::class, 'index'])->name('service-disputes.index');
        Route::get('service-disputes/{dispute:slug}', [ServiceDisputeController::class, 'show'])->name('service-disputes.show');
        Route::post('service-disputes/{dispute:slug}/accept', [ServiceDisputeController::class, 'accept'])->name('service-disputes.accept');
        Route::post('service-disputes/{dispute:slug}/reject', [ServiceDisputeController::class, 'reject'])->name('service-disputes.reject');

        // Auctions
        Route::get('auctions', [AuctionController::class, 'index'])->name('auctions.index');
        Route::get('auctions/history', [AuctionController::class, 'history'])->name('auctions.history');
        Route::get('auctions/{auction:slug}', [AuctionController::class, 'show'])->name('auctions.show');
        Route::post('auctions/{auction:slug}/bid', [AuctionController::class, 'bid'])->name('auctions.bid');

        // Featured
        Route::get('featured', [FeaturedController::class, 'index'])->name('featured.index');
        Route::post('featured/product', [FeaturedController::class, 'featureProduct'])->name('featured.product');
        Route::post('featured/service', [FeaturedController::class, 'featureService'])->name('featured.service');
        Route::get('featured/{slug}', [FeaturedController::class, 'show'])->name('featured.show');
    });
});
