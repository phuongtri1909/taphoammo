<?php

namespace App\Http\Controllers\Client;

use App\Models\User;
use App\Models\Wallet;
use App\Models\Withdrawal;
use Illuminate\Http\Request;
use App\Services\WithdrawalService;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Helpers\ImageHelper;
use Illuminate\Support\Facades\Storage;

class WithdrawalController extends Controller
{
    protected WithdrawalService $withdrawalService;

    public function __construct(WithdrawalService $withdrawalService)
    {
        $this->withdrawalService = $withdrawalService;
    }

    public function index()
    {
        $user = Auth::user();

        if ($user->role !== User::ROLE_SELLER) {
            abort(403, 'Chỉ người bán hàng mới có thể rút tiền');
        }

        $wallet = Wallet::where('user_id', $user->id)->first();
        $balance = $wallet ? $wallet->balance : 0;

        $withdrawals = Withdrawal::where('user_id', $user->id)
            ->orderByDesc('created_at')
            ->paginate(20);

        return view('client.pages.withdrawal.index', compact('balance', 'withdrawals'));
    }

    public function store(Request $request)
    {
        $user = Auth::user();

        if ($user->role !== 'seller') {
            return response()->json([
                'success' => false,
                'message' => 'Chỉ seller mới có thể rút tiền'
            ], 403);
        }

        $request->validate([
            'amount' => 'required|integer|min:50000',
            'bank_name' => 'required|string|max:255',
            'bank_account_number' => 'required|string|max:50',
            'bank_account_name' => 'required|string|max:255',
            'note' => 'nullable|string|max:500',
            'qr_code' => 'nullable|image|mimes:jpeg,jpg,png,gif,webp|max:5120',
        ], [
            'amount.required' => 'Vui lòng nhập số tiền',
            'amount.integer' => 'Số tiền phải là số nguyên',
            'amount.min' => 'Số tiền tối thiểu là 50,000₫',
            'bank_name.required' => 'Vui lòng nhập tên ngân hàng',
            'bank_account_number.required' => 'Vui lòng nhập số tài khoản',
            'bank_account_name.required' => 'Vui lòng nhập tên chủ tài khoản',
            'qr_code.image' => 'File phải là ảnh',
            'qr_code.mimes' => 'Chỉ chấp nhận file ảnh (JPG, PNG, GIF, WEBP)',
            'qr_code.max' => 'Kích thước file tối đa 5MB',
        ]);

        try {
            if ($request->has('save_bank_info') && $request->input('save_bank_info') == '1') {
                $user->update([
                    'bank_name' => $request->input('bank_name'),
                    'bank_account_number' => $request->input('bank_account_number'),
                    'bank_account_name' => $request->input('bank_account_name'),
                ]);

                if ($request->hasFile('qr_code')) {
                    if ($user->qr_code && Storage::disk('public')->exists($user->qr_code)) {
                        Storage::disk('public')->delete($user->qr_code);
                    }

                    $qrCodePath = ImageHelper::optimizeAndSave(
                        $request->file('qr_code'),
                        'qr-codes',
                        800,
                        85
                    );

                    $user->update(['qr_code' => $qrCodePath]);
                }
            }

            $withdrawal = $this->withdrawalService->createWithdrawal(
                $user,
                $request->input('amount'),
                $request->input('bank_name'),
                $request->input('bank_account_number'),
                $request->input('bank_account_name'),
                $request->input('note')
            );

            return response()->json([
                'success' => true,
                'withdrawal_slug' => $withdrawal->slug,
                'message' => 'Mã OTP đã được gửi đến email của bạn'
            ]);
        } catch (\InvalidArgumentException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Error creating withdrawal: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function verifyOtp(Request $request, Withdrawal $withdrawal)
    {
        $user = Auth::user();

        if ($withdrawal->user_id !== $user->id) {
            return response()->json([
                'success' => false,
                'message' => 'Không có quyền truy cập'
            ], 403);
        }

        $request->validate([
            'otp' => 'required|string|size:6',
        ], [
            'otp.required' => 'Vui lòng nhập mã OTP',
            'otp.size' => 'Mã OTP phải có 6 số',
        ]);

        try {
            $this->withdrawalService->verifyOtp($withdrawal, $request->input('otp'));

            return response()->json([
                'success' => true,
                'message' => 'Xác thực thành công. Yêu cầu rút tiền đang chờ xử lý.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }

    public function resendOtp(Withdrawal $withdrawal)
    {
        $user = Auth::user();

        if ($withdrawal->user_id !== $user->id) {
            return response()->json([
                'success' => false,
                'message' => 'Không có quyền truy cập'
            ], 403);
        }

        try {
            $this->withdrawalService->resendOtp($withdrawal);

            return response()->json([
                'success' => true,
                'message' => 'Mã OTP mới đã được gửi đến email của bạn'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }

    public function cancel(Withdrawal $withdrawal)
    {
        $user = Auth::user();

        if ($withdrawal->user_id !== $user->id) {
            return response()->json([
                'success' => false,
                'message' => 'Không có quyền truy cập'
            ], 403);
        }

        try {
            $this->withdrawalService->cancelWithdrawal($withdrawal);

            return response()->json([
                'success' => true,
                'message' => 'Đã hủy yêu cầu rút tiền và hoàn tiền về ví'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }

    public function clearBankInfo()
    {
        $user = Auth::user();

        if ($user->role !== 'seller') {
            return response()->json([
                'success' => false,
                'message' => 'Chỉ seller mới có thể thực hiện thao tác này'
            ], 403);
        }

        try {
            if ($user->qr_code && Storage::disk('public')->exists($user->qr_code)) {
                Storage::disk('public')->delete($user->qr_code);
            }

            $user->update([
                'bank_name' => null,
                'bank_account_number' => null,
                'bank_account_name' => null,
                'qr_code' => null,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Đã xóa thông tin ngân hàng đã lưu'
            ]);
        } catch (\Exception $e) {
            Log::error('Error clearing bank info: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra, vui lòng thử lại'
            ], 500);
        }
    }
}
