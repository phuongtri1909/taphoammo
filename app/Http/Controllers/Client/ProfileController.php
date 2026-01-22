<?php

namespace App\Http\Controllers\Client;

use App\Models\Order;
use App\Models\Config;
use App\Models\Product;
use App\Helpers\ImageHelper;
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

    public function update(Request $request)
    {
        $user = Auth::user();

        if (!$user) {
            return redirect()->route('sign-in');
        }

        $request->validate([
            'full_name' => 'nullable|string|max:255',
            'avatar' => 'nullable|image|mimes:jpeg,jpg,png,webp|max:5120',
        ], [
            'full_name.string' => 'Họ tên không hợp lệ.',
            'full_name.max' => 'Họ tên không được vượt quá 255 ký tự.',
            'avatar.image' => 'File phải là ảnh.',
            'avatar.mimes' => 'Ảnh phải là định dạng: jpeg, jpg, png, webp.',
            'avatar.max' => 'Kích thước ảnh tối đa 5MB.',
        ]);

        try {
            $updateData = [];
            
            if ($request->filled('full_name')) {
                $updateData['full_name'] = $request->full_name;
            }

            if ($request->hasFile('avatar')) {
                
                if ($user->avatar) {
                    ImageHelper::delete($user->avatar);
                }

                try {
                    $avatarPath = ImageHelper::optimizeAndSave($request->file('avatar'), 'avatars', null, 85, true);
                    
                    if ($avatarPath) {
                        $updateData['avatar'] = $avatarPath;
                    } else {
                    }
                } catch (\Exception $e) {
                    throw $e;
                }
            } else {
            }

            if (!empty($updateData)) {
                
                $updated = $user->update($updateData);
                
                if (!$updated) {
                    \Log::error('Failed to update user profile', ['user_id' => $user->id]);
                    throw new \Exception('Không thể cập nhật thông tin người dùng.');
                }
                
                $user->refresh();
                
            } else {
                if ($request->ajax() || $request->wantsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Không có dữ liệu nào để cập nhật.'
                    ], 400);
                }
                return redirect()->back()->with('error', 'Không có dữ liệu nào để cập nhật.');
            }

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Đã cập nhật thông tin thành công!',
                    'user' => [
                        'full_name' => $user->full_name,
                        'avatar_url' => $user->avatar ? Storage::url($user->avatar) : null,
                    ]
                ]);
            }

            return redirect()->route('profile.index')->with('success', 'Đã cập nhật thông tin thành công!');
        } catch (\Exception $e) {
            \Log::error('Error updating profile: ' . $e->getMessage());
            
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Có lỗi xảy ra, vui lòng thử lại sau.'
                ], 500);
            }

            return redirect()->back()->with('error', 'Có lỗi xảy ra, vui lòng thử lại sau.')->withInput();
        }
    }
}
