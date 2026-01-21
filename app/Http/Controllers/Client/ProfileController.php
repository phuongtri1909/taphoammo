<?php

namespace App\Http\Controllers\Client;

use App\Models\Order;
use App\Models\Config;
use App\Models\Product;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use App\Models\WalletTransaction;
use App\Enums\WalletTransactionStatus;

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

    public function transactions(Request $request)
    {
        $user = Auth::user();

        if (!$user) {
            return redirect()->route('sign-in');
        }

        $wallet = $user->wallet;
        
        if (!$wallet) {
            $transactions = WalletTransaction::whereRaw('1 = 0')->paginate(20);
        } else {
            $query = $wallet->transactions()
                ->with([
                    'order',
                    'refund.order'
                ])
                ->latest();

            if ($request->has('status') && $request->status) {
                $query->where('status', $request->status);
            }

            $transactions = $query->paginate(20);
        }

        return view('client.pages.profile.transactions', [
            'transactions' => $transactions,
            'user' => $user,
        ]);
    }

    public function showChangePassword()
    {
        $user = Auth::user();

        if (!$user) {
            return redirect()->route('sign-in');
        }

        return view('client.pages.profile.change-password', [
            'user' => $user,
        ]);
    }

    public function updatePassword(Request $request)
    {
        $user = Auth::user();

        if (!$user) {
            return redirect()->route('sign-in');
        }

        $request->validate([
            'current_password' => 'required',
            'password' => 'required|min:8|confirmed',
        ], [
            'current_password.required' => 'Vui lòng nhập mật khẩu hiện tại.',
            'password.required' => 'Vui lòng nhập mật khẩu mới.',
            'password.min' => 'Mật khẩu mới phải có ít nhất 8 ký tự.',
            'password.confirmed' => 'Mật khẩu xác nhận không khớp.',
        ]);

        if (!Hash::check($request->current_password, $user->password)) {
            throw ValidationException::withMessages([
                'current_password' => ['Mật khẩu hiện tại không đúng.'],
            ]);
        }

        $user->update([
            'password' => Hash::make($request->password),
        ]);

        return redirect()->route('profile.change-password')->with('success', 'Mật khẩu đã được thay đổi thành công!');
    }
}
