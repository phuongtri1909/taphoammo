<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Services\TwoFactorService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TwoFactorController extends Controller
{
    protected TwoFactorService $twoFactorService;

    public function __construct(TwoFactorService $twoFactorService)
    {
        $this->twoFactorService = $twoFactorService;
    }

    public function index()
    {
        $user = Auth::user();
        return view('client.pages.security.two-factor', compact('user'));
    }

    public function enable(Request $request)
    {
        $user = Auth::user();

        if ($user->hasTwoFactorEnabled()) {
            return response()->json([
                'success' => false,
                'message' => 'Bảo mật 2 lớp đã được bật.'
            ], 400);
        }

        try {
            $data = $this->twoFactorService->initializeSetup($user);
            return response()->json([
                'success' => true,
                'secret' => $data['secret'],
                'qr_code' => $data['qr_code'],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function confirm(Request $request)
    {
        $request->validate([
            'code' => 'required|string|size:6',
        ], [
            'code.required' => 'Vui lòng nhập mã xác thực.',
            'code.size' => 'Mã xác thực phải có 6 ký tự.',
        ]);

        $user = Auth::user();

        try {
            $result = $this->twoFactorService->confirmSetup($user, $request->code);
            return response()->json([
                'success' => true,
                'message' => $result['message'],
                'recovery_codes' => $result['recovery_codes'],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }


    public function disable(Request $request)
    {
        $request->validate([
            'password' => 'required|string',
        ], [
            'password.required' => 'Vui lòng nhập mật khẩu.',
        ]);

        $user = Auth::user();

        try {
            $this->twoFactorService->disable($user, $request->password);
            return response()->json([
                'success' => true,
                'message' => 'Đã tắt bảo mật 2 lớp.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }

    public function showVerifyForm()
    {
        if (!session('2fa_user_id')) {
            return redirect()->route('sign-in');
        }

        return view('client.pages.auth.two-factor-verify');
    }

    public function verify(Request $request)
    {
        $request->validate([
            'code' => 'required|string',
        ], [
            'code.required' => 'Vui lòng nhập mã xác thực.',
        ]);

        $userId = session('2fa_user_id');
        if (!$userId) {
            return redirect()->route('sign-in')->with('error', 'Phiên đăng nhập đã hết hạn.');
        }

        $user = \App\Models\User::find($userId);
        if (!$user) {
            return redirect()->route('sign-in')->with('error', 'Không tìm thấy tài khoản.');
        }

        try {
            if ($this->twoFactorService->verifyLogin($user, $request->code)) {
                session()->forget('2fa_user_id');
                Auth::login($user);
                session(['2fa_verified' => true]);

                return redirect()->intended(route('home'));
            }

            return back()->withErrors(['code' => 'Mã xác thực không chính xác.']);
        } catch (\Exception $e) {
            return back()->withErrors(['code' => $e->getMessage()]);
        }
    }

    public function getRecoveryCodes(Request $request)
    {
        $request->validate([
            'password' => 'required|string',
        ]);

        $user = Auth::user();

        if (!\Hash::check($request->password, $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Mật khẩu không chính xác.'
            ], 400);
        }

        if (!$user->hasTwoFactorEnabled()) {
            return response()->json([
                'success' => false,
                'message' => 'Bảo mật 2 lớp chưa được bật.'
            ], 400);
        }

        $recoveryCodes = json_decode(decrypt($user->two_factor_recovery_codes), true);

        return response()->json([
            'success' => true,
            'recovery_codes' => $recoveryCodes,
        ]);
    }

    public function regenerateRecoveryCodes(Request $request)
    {
        $request->validate([
            'password' => 'required|string',
        ], [
            'password.required' => 'Vui lòng nhập mật khẩu.',
        ]);

        $user = Auth::user();

        if (!\Hash::check($request->password, $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Mật khẩu không chính xác.'
            ], 400);
        }

        if (!$user->hasTwoFactorEnabled()) {
            return response()->json([
                'success' => false,
                'message' => 'Bảo mật 2 lớp chưa được bật.'
            ], 400);
        }

        $recoveryCodes = $this->twoFactorService->generateRecoveryCodes();
        $user->update([
            'two_factor_recovery_codes' => encrypt(json_encode($recoveryCodes)),
        ]);

        return response()->json([
            'success' => true,
            'recovery_codes' => $recoveryCodes,
            'message' => 'Đã tạo mã khôi phục mới.'
        ]);
    }
}


