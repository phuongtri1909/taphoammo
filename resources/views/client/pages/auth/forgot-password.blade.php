@extends('client.layouts.auth')

@section('title', 'Quên mật khẩu')

@section('header')
    <h2 class="text-2xl md:text-xl sm:text-lg font-semibold text-[#002740] mb-2">Quên mật khẩu</h2>
    <p class="text-sm sm:text-xs text-[#7E899C]">Nhập email để nhận link đặt lại mật khẩu</p>
@endsection

@section('content')
    <!-- Google Login Button -->
    <div class="mb-6">
        <a href="{{ route('login.google') }}" class="w-full flex items-center justify-center gap-3 py-3.5 px-6 border-2 border-gray-300 rounded-full bg-white text-sm font-semibold text-gray-700 hover:bg-gray-50 transition-all shadow-sm hover:shadow-md">
            <svg width="20" height="20" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/>
                <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/>
            </svg>
            <span>Đăng nhập với Google</span>
        </a>
    </div>

    <!-- Divider -->
    <div class="relative mb-6">
        <div class="absolute inset-0 flex items-center">
            <div class="w-full border-t border-gray-300"></div>
        </div>
        <div class="relative flex justify-center text-sm">
            <span class="px-4 bg-white text-gray-500 font-medium">hoặc</span>
        </div>
    </div>

    <form method="POST" action="{{ route('forgot-password.post') }}" class="login-form">
        @csrf

        <div class="mb-6">
            <label for="email" class="block text-sm font-normal text-[#002740] mb-2">Email</label>
            <input type="email" class="w-full py-3.5 px-4 rounded-full border border-[#E7ECF1] bg-[#F8F9FB] text-sm text-primary-8 transition-all login-input @error('email') border-red-500 @enderror" id="email" name="email" value="{{ old('email') }}" placeholder="Nhập email của bạn" required autofocus>
            @error('email')
                <div class="text-red-500 text-sm mt-1">
                    {{ $message }}
                </div>
            @enderror
        </div>

        <div class="w-full">
            <button type="submit" class="w-full py-3.5 px-6 bg-gradient-to-r from-primary to-primary-6 border-none rounded-full text-white text-sm font-bold cursor-pointer transition-all shadow-lg btn-login">
                Gửi link đặt lại mật khẩu
            </button>
        </div>
    </form>

    <div class="mt-6 text-center">
        <p class="text-sm font-normal text-[#7E899C] m-0">
            <a href="{{ route('sign-in') }}" class="text-sm text-[#E82A00] underline font-medium transition-opacity register-link">Quay lại đăng nhập</a>
        </p>
    </div>
@endsection
