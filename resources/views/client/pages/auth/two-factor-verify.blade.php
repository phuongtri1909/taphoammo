@extends('client.layouts.auth')

@section('title', 'Xác thực 2 lớp')

@section('header')
    <h2 class="text-2xl md:text-xl sm:text-lg font-semibold text-[#002740] mb-2">Xác thực 2 lớp</h2>
    <p class="text-sm sm:text-xs text-[#7E899C]">Nhập mã xác thực từ ứng dụng Google Authenticator</p>
@endsection

@section('content')
    @if(session('warning'))
        <div class="mb-4 p-4 bg-yellow-50 border border-yellow-200 rounded-lg">
            <p class="text-sm text-yellow-800">{{ session('warning') }}</p>
        </div>
    @endif

    <div class="mb-6 text-center">
        <div class="w-20 h-20 mx-auto bg-gradient-to-br from-primary/10 to-primary-6/10 rounded-full flex items-center justify-center mb-4">
            <i class="fas fa-shield-alt text-3xl text-primary"></i>
        </div>
        <p class="text-sm text-gray-600">
            Mở ứng dụng xác thực của bạn và nhập mã 6 số để tiếp tục đăng nhập.
        </p>
    </div>

    <form method="POST" action="{{ route('2fa.verify.post') }}" class="2fa-form">
        @csrf

        <div class="mb-6">
            <label for="code" class="block text-sm font-normal text-[#002740] mb-2">Mã xác thực</label>
            <input type="text" 
                class="w-full py-3.5 px-4 rounded-full border border-[#E7ECF1] bg-[#F8F9FB] text-sm text-primary-8 transition-all text-center tracking-[0.5em] font-mono text-lg @error('code') border-red-500 @enderror" 
                id="code" 
                name="code" 
                placeholder="000000" 
                maxlength="10"
                autocomplete="one-time-code"
                required 
                autofocus>
            @error('code')
                <div class="text-red-500 text-sm mt-1">
                    {{ $message }}
                </div>
            @enderror
        </div>

        <div class="w-full mb-4">
            <button type="submit" class="w-full py-3.5 px-6 bg-gradient-to-r from-primary to-primary-6 border-none rounded-full text-white text-sm font-bold cursor-pointer transition-all shadow-lg btn-login">
                Xác thực
            </button>
        </div>
    </form>

    <div class="text-center">
        <p class="text-xs text-gray-500 mb-2">Không truy cập được vào ứng dụng xác thực?</p>
        <button type="button" onclick="showRecoveryInput()" class="text-sm text-primary hover:underline font-medium">
            Sử dụng mã khôi phục
        </button>
    </div>

    <div id="recoverySection" class="hidden mt-4 p-4 bg-gray-50 rounded-lg">
        <p class="text-sm text-gray-600 mb-3">
            Nhập một trong các mã khôi phục 8 ký tự mà bạn đã lưu khi bật 2FA.
        </p>
        <p class="text-xs text-orange-600">
            <i class="fas fa-exclamation-triangle mr-1"></i>
            Lưu ý: Mỗi mã khôi phục chỉ có thể sử dụng một lần.
        </p>
    </div>

    <div class="mt-6 text-center">
        <a href="{{ route('sign-in') }}" class="text-sm text-gray-500 hover:text-primary">
            <i class="fas fa-arrow-left mr-1"></i> Quay lại đăng nhập
        </a>
    </div>
@endsection

@push('scripts')
<script>
    document.getElementById('code').addEventListener('input', function(e) {
        this.value = this.value.replace(/[^0-9A-Za-z]/g, '').toUpperCase();
    });

    function showRecoveryInput() {
        document.getElementById('recoverySection').classList.toggle('hidden');
    }
</script>
@endpush


