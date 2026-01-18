<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        using: function () {
            Route::middleware('web')
                ->group(base_path('routes/web.php'));

            Route::middleware('web')
                ->prefix('admin')
                ->group(base_path('routes/admin.php'));

            Route::middleware('web')
                ->prefix('seller')
                ->group(base_path('routes/seller.php'));
        },
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->redirectGuestsTo(fn() => route('sign-in'));

        $middleware->alias([
            'check.role' => \App\Http\Middleware\CheckRole::class,
            '2fa.require' => \App\Http\Middleware\RequireTwoFactor::class,
            '2fa.enabled' => \App\Http\Middleware\EnsureTwoFactorEnabled::class,
            'seller.not.banned' => \App\Http\Middleware\CheckSellerBanned::class,
        ]);

        $middleware->web([
          
        ]);

        $middleware->validateCsrfTokens(except: [

        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        // ...
    })
    ->create();
