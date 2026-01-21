<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withSchedule(function (Schedule $schedule) {
        $schedule->command('orders:complete-expired --chunk=50')
            ->everyFiveMinutes()
            ->withoutOverlapping()
            ->runInBackground()
            ->appendOutputTo(storage_path('logs/complete-expired-orders.log'));

        $schedule->command('disputes:auto-approve --chunk=50')
            ->everyTenMinutes()
            ->withoutOverlapping()
            ->runInBackground()
            ->appendOutputTo(storage_path('logs/auto-approve-disputes.log'));

        // Service Orders - Auto refund if seller doesn't confirm completion
        $schedule->command('service-orders:auto-refund-expired --chunk=50')
            ->everyFiveMinutes()
            ->withoutOverlapping()
            ->runInBackground()
            ->appendOutputTo(storage_path('logs/service-orders-auto-refund.log'));

        // Service Orders - Auto complete if buyer doesn't respond after seller confirms
        $schedule->command('service-orders:auto-complete-expired --chunk=50')
            ->everyFiveMinutes()
            ->withoutOverlapping()
            ->runInBackground()
            ->appendOutputTo(storage_path('logs/service-orders-auto-complete.log'));
    })
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
        $middleware->redirectGuestsTo(function ($request) {
            // Nếu request expects JSON, return null để Laravel trả về 401 JSON response
            if ($request->expectsJson()) {
                return null;
            }
            return route('sign-in');
        });

        $middleware->alias([
            'check.role' => \App\Http\Middleware\CheckRole::class,
            '2fa.require' => \App\Http\Middleware\RequireTwoFactor::class,
            '2fa.enabled' => \App\Http\Middleware\EnsureTwoFactorEnabled::class,
            'seller.not.banned' => \App\Http\Middleware\CheckSellerBanned::class,
            'user.active' => \App\Http\Middleware\CheckUserActive::class,
        ]);

        $middleware->web([
          \App\Http\Middleware\SecureFileUpload::class,
        ]);

        $middleware->validateCsrfTokens(except: [
            '/deposit/callback',
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->render(function (\Illuminate\Auth\AuthenticationException $e, $request) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Vui lòng đăng nhập để tiếp tục.'
                ], 401);
            }
        });
    })
    ->create();
