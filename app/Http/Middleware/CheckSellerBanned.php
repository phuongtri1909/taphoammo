<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckSellerBanned
{
    /**
     * Handle an incoming request.
     * This middleware checks if seller is banned and restricts write operations
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (!$user || !$user->isSeller()) {
            return $next($request);
        }

        if ($user->isSellerBanned()) {
            if (!$request->isMethod('GET')) {
                if ($request->expectsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Tài khoản seller của bạn đã bị khóa. Lý do: ' . ($user->seller_ban_reason ?? 'Không có lý do cụ thể'),
                        'banned' => true,
                        'ban_reason' => $user->seller_ban_reason,
                    ], 403);
                }

                return redirect()->route('seller.dashboard')
                    ->with('error', 'Tài khoản seller của bạn đã bị khóa. Lý do: ' . ($user->seller_ban_reason ?? 'Không có lý do cụ thể'));
            }
        }

        return $next($request);
    }
}


