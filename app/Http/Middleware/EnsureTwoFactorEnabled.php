<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureTwoFactorEnabled
{
    /**
     * Handle an incoming request.
     * This middleware ensures user has 2FA enabled before accessing certain routes
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user && !$user->hasTwoFactorEnabled()) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Bạn cần bật bảo mật 2 lớp (2FA) để sử dụng chức năng này.',
                    'redirect' => route('security.two-factor')
                ], 403);
            }

            return redirect()->route('security.two-factor')
                ->with('warning', 'Bạn cần bật bảo mật 2 lớp (2FA) để sử dụng chức năng này.');
        }

        return $next($request);
    }
}


