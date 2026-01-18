<?php

namespace App\Http\Controllers\Client;

use Illuminate\Http\Request;
use App\Models\SellerRegistration;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Enums\SellerRegistrationStatus;

class SellerRegistrationController extends Controller
{

    public function create()
    {
        $user = Auth::user();

        if (!$user->canRegisterAsSeller()) {
            return redirect()->route('home')->with('error', 'Bạn đã đăng ký bán hàng.');
        }

        if (!$user->hasTwoFactorEnabled()) {
            return redirect()->route('security.two-factor')->with('warning', 'Bạn cần bật bảo mật 2 lớp (2FA) trước khi đăng ký bán hàng.');
        }

        $pendingRegistration = $user->sellerRegistration()->where('status', 'pending')->first();
        if ($pendingRegistration) {
            return view('client.pages.seller.pending', compact('pendingRegistration'));
        }

        $rejectedRegistration = $user->sellerRegistration()->where('status', 'rejected')->latest()->first();

        return view('client.pages.seller.register', compact('user', 'rejectedRegistration'));
    }


    public function store(Request $request)
    {
        $user = Auth::user();

        if (!$user->canRegisterAsSeller()) {
            return response()->json([
                'success' => false,
                'message' => 'Bạn đã đăng ký bán hàng.'
            ], 403);
        }

        if (!$user->hasTwoFactorEnabled()) {
            return response()->json([
                'success' => false,
                'message' => 'Bạn cần bật bảo mật 2 lớp (2FA) trước khi đăng ký bán hàng.',
                'redirect' => route('security.two-factor')
            ], 403);
        }

        if ($user->hasPendingSellerRegistration()) {
            return response()->json([
                'success' => false,
                'message' => 'Bạn đã có đơn đăng ký đang chờ duyệt.'
            ], 400);
        }

        $request->validate([
            'phone' => 'required|string|max:20|regex:/^[0-9+\-\s]+$/',
            'bank_name' => 'required|string|max:100',
            'custom_bank_name' => 'required_if:bank_name,Khác|string|max:100|nullable',
            'bank_account_number' => 'required|string|max:50|regex:/^[0-9]+$/',
            'bank_account_name' => 'required|string|max:100',
            'facebook_url' => 'nullable|url|max:255',
            'telegram_username' => 'nullable|string|max:100',
            'terms' => 'required|accepted',
        ], [
            'phone.required' => 'Vui lòng nhập số điện thoại.',
            'phone.regex' => 'Số điện thoại không hợp lệ.',
            'bank_name.required' => 'Vui lòng chọn ngân hàng.',
            'custom_bank_name.required_if' => 'Vui lòng nhập tên ngân hàng.',
            'custom_bank_name.max' => 'Tên ngân hàng không được quá 100 ký tự.',
            'bank_account_number.required' => 'Vui lòng nhập số tài khoản.',
            'bank_account_number.regex' => 'Số tài khoản chỉ được chứa số.',
            'bank_account_name.required' => 'Vui lòng nhập tên chủ tài khoản.',
            'facebook_url.url' => 'Link Facebook không hợp lệ.',
            'terms.required' => 'Vui lòng đồng ý với điều khoản dịch vụ và chính sách bán hàng.',
            'terms.accepted' => 'Bạn phải đồng ý với điều khoản dịch vụ và chính sách bán hàng để tiếp tục.',
        ]);

        $finalBankName = $request->bank_name === 'Khác' 
            ? $request->custom_bank_name 
            : $request->bank_name;

        if (!$finalBankName) {
            return response()->json([
                'success' => false,
                'message' => 'Vui lòng nhập tên ngân hàng.'
            ], 422);
        }

        $user->sellerRegistration()->where('status', 'rejected')->delete();

        $registration = SellerRegistration::create([
            'user_id' => $user->id,
            'phone' => $request->phone,
            'bank_name' => $finalBankName,
            'bank_account_number' => $request->bank_account_number,
            'bank_account_name' => strtoupper($request->bank_account_name),
            'facebook_url' => $request->facebook_url,
            'telegram_username' => $request->telegram_username,
            'status' => SellerRegistrationStatus::PENDING->value,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Đăng ký bán hàng thành công! Chúng tôi sẽ xem xét và phản hồi trong thời gian sớm nhất.',
            'redirect' => route('seller.register')
        ]);
    }
}


