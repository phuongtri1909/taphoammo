@extends('client.layouts.app')

@section('title', 'Gian hàng không khả dụng - ' . config('app.name'))

@section('content')
<div class="w-full bg-gradient-to-br from-gray-50 via-white to-gray-50 min-h-screen py-8 md:py-12">
    <div class="w-full max-w-xl mx-auto px-3 sm:px-4 md:px-6">
        <div class="bg-white rounded-xl shadow-lg border border-gray-100 overflow-hidden">
            <div class="p-8 text-center">
                <!-- Icon -->
                <div class="w-20 h-20 mx-auto bg-red-100 rounded-full flex items-center justify-center mb-6">
                    <i class="fas fa-store-slash text-3xl text-red-500"></i>
                </div>

                <h1 class="text-xl font-bold text-gray-900 mb-2">Gian hàng không khả dụng</h1>
                <p class="text-sm text-gray-600 mb-6">
                    Gian hàng này hiện đang bị tạm khóa và không thể truy cập. 
                </p>

                <div class="bg-gray-50 rounded-lg p-4 mb-6">
                    <p class="text-sm text-gray-500">
                        Nếu bạn cần hỗ trợ hoặc có thắc mắc, vui lòng liên hệ bộ phận hỗ trợ khách hàng.
                    </p>
                </div>

                <a href="{{ route('home') }}" class="inline-flex items-center justify-center w-full py-3 px-4 bg-gradient-to-r from-primary to-primary-6 text-white font-semibold rounded-lg hover:shadow-lg transition-all">
                    <i class="fas fa-home mr-2"></i>
                    Về trang chủ
                </a>
            </div>
        </div>
    </div>
</div>
@endsection


