<?php

namespace App\Http\Controllers\Client;

use App\Models\Order;
use App\Models\Config;
use App\Models\Product;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        if (!$user) {
            return redirect()->route('sign-in');
        }

        $wallet = $user->wallet;
        $balance = $wallet ? number_format($wallet->balance, 0, ',', '.') : '0';

        $totalTransactionAmount = 0;
        if ($wallet) {
            $totalTransactionAmount = $wallet->transactions()
                ->where('type', '!=', 'refund')
                ->sum('amount');
        }

        $levelAmount = Config::getConfig('level_amount');

        $currentLevel = floor($totalTransactionAmount / $levelAmount);

        $nextLevelAmount = ($currentLevel + 1) * $levelAmount;

        $progressPercent = $totalTransactionAmount > 0
            ? min(100, ($totalTransactionAmount % $levelAmount) * 100 / $levelAmount)
            : 0;


        $shopsCount = 0;
        if ($user->role === 'seller') {
            $shopsCount = Product::where('seller_id', $user->id)->distinct()->count('seller_id');
        }

        $soldCount = Product::where('seller_id', $user->id)
            ->withSum('variants', 'sold_count')
            ->get()
            ->sum(function ($product) {
                return $product->variants_sum_sold_count ?? 0;
            });

        // sau ghép model post vào đây
        $postsCount = 0;

        // tạm thời đóng
        // $lastLoginDate = $user->created_at->format('D M d Y');
        // $lastLoginDevice = 'Chrome';

        $settings = [
            'api_buy_enabled' => true,
            'two_factor_enabled' => $user->hasTwoFactorEnabled(),
            'telegram_connected' => false,
        ];

        return view('client.pages.profile.index', [
            'user' => $user,
            'balance' => $balance,
            'currentLevel' => $currentLevel,
            'nextLevelAmount' => $nextLevelAmount,
            'progressPercent' => $progressPercent,
            'totalTransactionAmount' => $totalTransactionAmount,
            'shopsCount' => $shopsCount,
            'soldCount' => $soldCount,
            'postsCount' => $postsCount,
            // 'lastLoginDate' => $lastLoginDate,
            // 'lastLoginDevice' => $lastLoginDevice,
            'settings' => $settings,
        ]);
    }
}
