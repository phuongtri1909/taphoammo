<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RequireTwoFactor
{
    /**
     * Handle an incoming request.
     * This middleware checks if user has 2FA enabled and verified in current session
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user && $user->hasTwoFactorEnabled() && !session('2fa_verified')) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Yêu cầu xác thực 2FA.',
                    'requires_2fa' => true
                ], 403);
            }

            session(['url.intended' => $request->url()]);
            session(['2fa_user_id' => $user->id]);
            
            auth()->logout();

            return redirect()->route('2fa.verify')
                ->with('warning', 'Vui lòng xác thực 2 lớp để tiếp tục.');
        }

        return $next($request);
    }
}


