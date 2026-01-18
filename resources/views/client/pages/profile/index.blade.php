@extends('client.layouts.app')

@section('title', 'Trang cá nhân - ' . config('app.name'))

@section('content')
    <div class="w-full bg-gradient-to-br from-gray-50 via-white to-gray-50 min-h-screen py-4 md:py-6">
        <div class="w-full max-w-7xl mx-auto px-3 sm:px-4 md:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 lg:gap-5">
                <div class="lg:col-span-2 space-y-4">
                    <div
                        class="bg-white rounded-xl shadow-lg border border-gray-100 overflow-hidden transform transition-all duration-300 hover:shadow-xl animate-fadeIn">
                        <div class="p-4 md:p-5">
                            <div class="flex items-center justify-between mb-3">
                                <h3 class="text-lg font-bold text-gray-900">Level: {{ $currentLevel }}</h3>
                                <div
                                    class="w-12 h-12 bg-gradient-to-br from-primary to-primary-6 rounded-full flex items-center justify-center shadow-md">
                                    <span class="text-lg font-bold text-white">{{ $currentLevel }}</span>
                                </div>
                            </div>

                            <div class="mb-3">
                                <div class="w-full bg-gray-200 rounded-full h-3 overflow-hidden shadow-inner">
                                    <div class="h-full bg-gradient-to-r from-primary via-primary-6 to-primary rounded-full transition-all duration-1000 ease-out relative overflow-hidden progress-bar"
                                        style="width: {{ $progressPercent }}%">
                                        <div class="absolute inset-0 bg-primary animate-shimmer"></div>
                                    </div>
                                </div>
                            </div>

                            <p class="text-xs text-red-500 font-medium">
                                <i class="fas fa-arrow-up mr-1"></i>
                                Hãy mua/bán thêm
                                {{ number_format($nextLevelAmount - $totalTransactionAmount, 0, ',', '.') }}₫ để đạt level
                                tiếp theo!
                            </p>
                        </div>
                    </div>

                    <div class="bg-white rounded-xl shadow-lg border border-gray-100 overflow-hidden transform transition-all duration-300 hover:shadow-xl animate-fadeIn"
                        style="animation-delay: 0.1s">
                        <div class="p-4 md:p-5">
                            <h3 class="text-lg font-bold text-gray-900 mb-4">Thông tin tài khoản</h3>

                            <div class="space-y-2.5">
                                <div
                                    class="flex items-center justify-between py-2 border-b border-gray-100 hover:bg-gray-50 px-2.5 rounded-md transition-all duration-200">
                                    <div class="flex items-center gap-2.5">
                                        <div class="w-8 h-8 bg-primary/10 rounded-lg flex items-center justify-center">
                                            <i class="fas fa-user text-primary text-xs"></i>
                                        </div>
                                        <span class="text-xs font-medium text-gray-600">Tài khoản</span>
                                    </div>
                                    <div class="text-center">
                                        <span class="text-xs font-bold text-gray-900">@ {{ $user->full_name }}</span>
                                        <br>
                                        <span class="text-xs font-bold text-gray-600">{{ $user->email }}</span>
                                    </div>
                                </div>

                                <div
                                    class="flex items-center justify-between py-2 border-b border-gray-100 hover:bg-gray-50 px-2.5 rounded-md transition-all duration-200">
                                    <div class="flex items-center gap-2.5">
                                        <div class="w-8 h-8 bg-green-100 rounded-lg flex items-center justify-center">
                                            <i class="fas fa-wallet text-green-600 text-xs"></i>
                                        </div>
                                        <span class="text-xs font-medium text-gray-600">Số dư</span>
                                    </div>
                                    <span class="text-xs font-bold text-green-600">{{ $balance }} Vnd</span>
                                </div>

                                <div
                                    class="flex items-center justify-between py-2 border-b border-gray-100 hover:bg-gray-50 px-2.5 rounded-md transition-all duration-200">
                                    <div class="flex items-center gap-2.5">
                                        <div class="w-8 h-8 bg-purple-100 rounded-lg flex items-center justify-center">
                                            <i class="fas fa-calendar text-purple-600 text-xs"></i>
                                        </div>
                                        <span class="text-xs font-medium text-gray-600">Ngày đăng kí</span>
                                    </div>
                                    <span
                                        class="text-xs font-bold text-gray-900">{{ $user->created_at->format('d/m/Y - H:i:s') }}</span>
                                </div>

                                <div
                                    class="flex items-center justify-between py-2 border-b border-gray-100 hover:bg-gray-50 px-2.5 rounded-md transition-all duration-200">
                                    <div class="flex items-center gap-2.5">
                                        <div class="w-8 h-8 bg-orange-100 rounded-lg flex items-center justify-center">
                                            <i class="fas fa-store text-orange-600 text-xs"></i>
                                        </div>
                                        <span class="text-xs font-medium text-gray-600">Số gian hàng</span>
                                    </div>
                                    <span
                                        class="text-xs font-bold text-gray-900">{{ number_format($shopsCount, 0, ',', '.') }}
                                        Gian hàng</span>
                                </div>

                                <div
                                    class="flex items-center justify-between py-2 border-b border-gray-100 hover:bg-gray-50 px-2.5 rounded-md transition-all duration-200">
                                    <div class="flex items-center gap-2.5">
                                        <div class="w-8 h-8 bg-red-100 rounded-lg flex items-center justify-center">
                                            <i class="fas fa-shopping-cart text-red-600 text-xs"></i>
                                        </div>
                                        <span class="text-xs font-medium text-gray-600">Số bán</span>
                                    </div>
                                    <span
                                        class="text-xs font-bold text-gray-900">{{ number_format($soldCount, 0, ',', '.') }}
                                        Sản phẩm</span>
                                </div>

                                <div
                                    class="flex items-center justify-between py-2 hover:bg-gray-50 px-2.5 rounded-md transition-all duration-200">
                                    <div class="flex items-center gap-2.5">
                                        <div class="w-8 h-8 bg-indigo-100 rounded-lg flex items-center justify-center">
                                            <i class="fas fa-file-alt text-indigo-600 text-xs"></i>
                                        </div>
                                        <span class="text-xs font-medium text-gray-600">Số bài viết</span>
                                    </div>
                                    <span
                                        class="text-xs font-bold text-gray-900">{{ number_format($postsCount, 0, ',', '.') }}
                                        Bài viết</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white rounded-xl shadow-lg border border-gray-100 overflow-hidden transform transition-all duration-300 hover:shadow-xl animate-fadeIn"
                        style="animation-delay: 0.2s">
                        <div class="p-4 md:p-5">
                            <h3 class="text-lg font-bold text-gray-900 mb-4">Cài đặt bảo mật</h3>

                            <div class="space-y-2.5">
                                {{-- <div class="flex items-center justify-between p-3 bg-gradient-to-r from-green-50 to-green-100/50 rounded-lg border border-green-200 hover:shadow-sm transition-all duration-200">
                                <div class="flex items-center gap-2.5 flex-1">
                                    <div class="w-8 h-8 bg-green-500 rounded-full flex items-center justify-center shadow-md">
                                        <i class="fas fa-check text-white text-xs"></i>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-xs font-semibold text-gray-900">Mua bằng API</p>
                                        <p class="text-[10px] text-gray-600 mt-0.5 truncate">Cho phép mua hàng tự động qua API</p>
                                    </div>
                                </div>
                                <span class="text-xs font-bold text-green-600 ml-2">Đang bật</span>
                            </div> --}}

                                <a href="{{ route('security.two-factor') }}"
                                    class="flex items-center justify-between p-3 rounded-lg border transition-all duration-200 cursor-pointer {{ $settings['two_factor_enabled'] ? 'bg-gradient-to-r from-green-50 to-green-100/50 border-green-200 hover:shadow-sm' : 'bg-gradient-to-r from-orange-50 to-orange-100/50 border-orange-200 hover:shadow-sm' }}">
                                    <div class="flex items-center gap-2.5 flex-1">
                                        <div
                                            class="w-8 h-8 rounded-full flex items-center justify-center shadow-md {{ $settings['two_factor_enabled'] ? 'bg-green-500' : 'bg-orange-500' }}">
                                            <i
                                                class="fas {{ $settings['two_factor_enabled'] ? 'fa-check' : 'fa-exclamation-triangle' }} text-white text-xs"></i>
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <p class="text-xs font-semibold text-gray-900">Bảo mật 2 lớp</p>
                                            <p class="text-[10px] text-gray-600 mt-0.5 line-clamp-1">
                                                {{ $settings['two_factor_enabled'] ? 'Đã bật bảo mật 2 lớp' : '(Hãy bảo mật tài khoản bằng mật khẩu 2 lớp!)' }}
                                            </p>
                                        </div>
                                    </div>
                                    <div class="flex items-center gap-2 ml-2">
                                        <span
                                            class="text-xs font-bold {{ $settings['two_factor_enabled'] ? 'text-green-600' : 'text-orange-600' }}">
                                            {{ $settings['two_factor_enabled'] ? 'Đang bật' : 'Chưa bật' }}
                                        </span>
                                        <i class="fas fa-chevron-right text-gray-400 text-xs"></i>
                                    </div>
                                </a>

                                <div
                                    class="flex items-center justify-between p-3 rounded-lg border transition-all duration-200 {{ $settings['telegram_connected'] ? 'bg-gradient-to-r from-green-50 to-green-100/50 border-green-200 hover:shadow-sm' : 'bg-gradient-to-r from-orange-50 to-orange-100/50 border-orange-200 hover:shadow-sm' }}">
                                    <div class="flex items-center gap-2.5 flex-1">
                                        <div
                                            class="w-8 h-8 rounded-full flex items-center justify-center shadow-md {{ $settings['telegram_connected'] ? 'bg-green-500' : 'bg-orange-500' }}">
                                            <i class="fab fa-telegram text-white text-xs"></i>
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <p class="text-xs font-semibold text-gray-900">Kết nối Telegram</p>
                                            <p class="text-[10px] text-gray-600 mt-0.5 line-clamp-1">
                                                {{ $settings['telegram_connected'] ? 'Đã kết nối với Telegram' : '(Bạn có thể gởi và nhận được tin nhắn mời (chưa xem) qua Telegram nếu có kết nối)' }}
                                            </p>
                                        </div>
                                    </div>
                                    <span
                                        class="text-xs font-bold {{ $settings['telegram_connected'] ? 'text-green-600' : 'text-orange-600' }} ml-2">
                                        {{ $settings['telegram_connected'] ? 'Đã kết nối' : 'Chưa kết nối' }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="flex flex-col sm:flex-row gap-3 animate-fadeIn" style="animation-delay: 0.3s">
                        <button onclick="window.location.href='#'"
                            class="flex-1 py-2.5 px-4 bg-gradient-to-r from-primary to-primary-6 hover:from-primary-6 hover:to-primary text-white font-semibold text-xs rounded-lg shadow-md hover:shadow-lg transition-all duration-300 transform hover:scale-[1.01] active:scale-[0.99] focus:outline-none focus:ring-2 focus:ring-primary/50">
                            <i class="fas fa-edit mr-1.5"></i>
                            Chỉnh Sửa
                        </button>
                        <button onclick="window.location.href='#'"
                            class="flex-1 py-2.5 px-4 bg-gradient-to-r from-green-500 to-green-600 hover:from-green-600 hover:to-green-500 text-white font-semibold text-xs rounded-lg shadow-md hover:shadow-lg transition-all duration-300 transform hover:scale-[1.01] active:scale-[0.99] focus:outline-none focus:ring-2 focus:ring-green-500/50">
                            <i class="fas fa-store mr-1.5"></i>
                            Xem tất cả gian hàng
                        </button>
                    </div>
                </div>

                <div class="lg:col-span-1 space-y-4">
                    <div
                        class="bg-white rounded-xl shadow-lg border border-gray-100 overflow-hidden transform transition-all duration-300 hover:shadow-xl animate-slideInRight">
                        <div class="p-4 md:p-5">
                            <div class="flex flex-col items-center">
                                <div class="relative mb-3">
                                    <div
                                        class="w-20 h-20 rounded-full overflow-hidden border-3 border-primary/20 shadow-lg transform transition-all duration-300 hover:scale-110">
                                        @if ($user->avatar)
                                            <img src="{{ Storage::url($user->avatar) }}" alt="{{ $user->full_name }}"
                                                class="w-full h-full object-cover">
                                        @else
                                            <div
                                                class="w-full h-full bg-gradient-to-br from-primary to-primary-6 flex items-center justify-center">
                                                <span
                                                    class="text-2xl font-bold text-white">{{ strtoupper(substr($user->full_name ?? 'U', 0, 1)) }}</span>
                                            </div>
                                        @endif
                                    </div>
                                    <div
                                        class="absolute bottom-0 right-0 w-5 h-5 bg-green-500 rounded-full border-3 border-white shadow-md animate-pulse">
                                    </div>
                                </div>

                                <h3 class="text-base font-bold text-gray-900 mb-1">{{ $user->full_name }}</h3>
                                <div class="flex items-center gap-1.5 mb-4">
                                    <span class="w-1.5 h-1.5 bg-green-500 rounded-full animate-pulse"></span>
                                    <span class="text-xs font-semibold text-green-600">Online</span>
                                </div>

                                <button onclick="window.location.href='#'"
                                    class="w-full py-2 px-4 bg-gradient-to-r from-primary to-primary-6 hover:from-primary-6 hover:to-primary text-white font-semibold text-xs rounded-lg shadow-md hover:shadow-lg transition-all duration-300 transform hover:scale-[1.01] active:scale-[0.99]">
                                    <i class="fas fa-store mr-1.5"></i>
                                    Gian hàng
                                </button>
                            </div>
                        </div>
                    </div>

                    {{-- <div class="bg-white rounded-xl shadow-lg border border-gray-100 overflow-hidden transform transition-all duration-300 hover:shadow-xl animate-slideInRight" style="animation-delay: 0.1s">
                    <div class="p-4 md:p-5">
                        <h3 class="text-base font-bold text-gray-900 mb-4 flex items-center gap-2">
                            <i class="fas fa-history text-primary text-sm"></i>
                            Lịch sử đăng nhập
                        </h3>
                        
                        <div class="space-y-2.5">
                            <div class="flex items-center gap-2.5 p-2.5 bg-gray-50 rounded-lg hover:bg-gray-100 transition-all duration-200">
                                <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                    <i class="fab fa-chrome text-blue-600 text-xs"></i>
                                </div>
                                <div class="flex-1">
                                    <p class="text-xs font-semibold text-gray-900">{{ $lastLoginDate }}</p>
                                    <p class="text-[10px] text-gray-600">Devices: {{ $lastLoginDevice }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div> --}}
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <style>
        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes slideInRight {
            from {
                opacity: 0;
                transform: translateX(20px);
            }

            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        @keyframes shimmer {
            0% {
                background-position: -1000px 0;
            }

            100% {
                background-position: 1000px 0;
            }
        }

        .animate-fadeIn {
            animation: fadeIn 0.6s ease-out forwards;
            opacity: 0;
        }

        .animate-slideInRight {
            animation: slideInRight 0.6s ease-out forwards;
            opacity: 0;
        }

        .progress-bar {
            background-size: 1000px 100%;
            background-image: linear-gradient(90deg,
                    transparent,
                    rgba(255, 255, 255, 0.3),
                    transparent);
        }

        .animate-shimmer {
            animation: shimmer 2s infinite;
        }

        .border-3 {
            border-width: 3px;
        }
    </style>
@endpush
