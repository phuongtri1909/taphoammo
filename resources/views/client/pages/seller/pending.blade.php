@extends('client.layouts.app')

@section('title', 'Đang chờ duyệt - ' . config('app.name'))

@section('content')
<div class="w-full bg-gradient-to-br from-gray-50 via-white to-gray-50 min-h-screen py-4 md:py-6">
    <div class="w-full max-w-xl mx-auto px-3 sm:px-4 md:px-6">
        <!-- Main Card -->
        <div class="bg-white rounded-xl shadow-lg border border-gray-100 overflow-hidden">
            <div class="p-6 md:p-8 text-center">
                <!-- Icon -->
                <div class="w-20 h-20 mx-auto bg-yellow-100 rounded-full flex items-center justify-center mb-6">
                    <i class="fas fa-clock text-3xl text-yellow-600"></i>
                </div>

                <h1 class="text-xl font-bold text-gray-900 mb-2">Đơn đăng ký đang được xem xét</h1>
                <p class="text-sm text-gray-600 mb-6">
                    Cảm ơn bạn đã đăng ký bán hàng trên {{ config('app.name') }}. Chúng tôi đang xem xét đơn của bạn.
                </p>

                <!-- Status Timeline -->
                <div class="text-left mb-6">
                    <div class="flex items-center gap-3 mb-4">
                        <div class="w-8 h-8 bg-green-500 rounded-full flex items-center justify-center flex-shrink-0">
                            <i class="fas fa-check text-white text-sm"></i>
                        </div>
                        <div>
                            <p class="font-medium text-gray-900">Đã gửi đơn đăng ký</p>
                            <p class="text-xs text-gray-500">{{ $pendingRegistration->created_at->format('d/m/Y H:i') }}</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-3 mb-4">
                        <div class="w-8 h-8 bg-yellow-500 rounded-full flex items-center justify-center flex-shrink-0 animate-pulse">
                            <i class="fas fa-spinner fa-spin text-white text-sm"></i>
                        </div>
                        <div>
                            <p class="font-medium text-gray-900">Đang chờ duyệt</p>
                            <p class="text-xs text-gray-500">Thường xử lý trong 24-48 giờ</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-3 opacity-50">
                        <div class="w-8 h-8 bg-gray-300 rounded-full flex items-center justify-center flex-shrink-0">
                            <i class="fas fa-store text-white text-sm"></i>
                        </div>
                        <div>
                            <p class="font-medium text-gray-900">Trở thành người bán</p>
                            <p class="text-xs text-gray-500">Bắt đầu bán hàng trên nền tảng</p>
                        </div>
                    </div>
                </div>

                <!-- Registration Info -->
                <div class="bg-gray-50 rounded-lg p-4 text-left mb-6">
                    <h3 class="font-semibold text-gray-900 mb-3">Thông tin đã đăng ký</h3>
                    <div class="space-y-2 text-sm">
                        <div class="flex justify-between">
                            <span class="text-gray-600">Số điện thoại:</span>
                            <span class="font-medium text-gray-900">{{ $pendingRegistration->phone }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Ngân hàng:</span>
                            <span class="font-medium text-gray-900">{{ $pendingRegistration->bank_name }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Số tài khoản:</span>
                            <span class="font-medium text-gray-900">{{ $pendingRegistration->bank_account_number }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Chủ tài khoản:</span>
                            <span class="font-medium text-gray-900">{{ $pendingRegistration->bank_account_name }}</span>
                        </div>
                    </div>
                </div>

                <a href="{{ route('home') }}" class="inline-flex items-center justify-center w-full py-3 px-4 bg-gradient-to-r from-primary to-primary-6 text-white font-semibold rounded-lg hover:shadow-lg transition-all">
                    <i class="fas fa-home mr-2"></i>
                    Quay lại trang chủ
                </a>
            </div>
        </div>

        <!-- Contact Note -->
        <div class="mt-4 p-4 bg-blue-50 border border-blue-200 rounded-lg">
            <p class="text-sm text-blue-800">
                <i class="fas fa-info-circle mr-2"></i>
                Nếu có thắc mắc, vui lòng liên hệ bộ phận hỗ trợ qua email: <strong>support@{{ str_replace('https://', '', config('app.url')) }}</strong>
            </p>
        </div>
    </div>
</div>
@endsection


