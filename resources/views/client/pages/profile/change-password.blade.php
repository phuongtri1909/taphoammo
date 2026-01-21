@extends('client.layouts.app')

@section('title', 'Đổi mật khẩu - ' . config('app.name'))

@section('content')
    <div class="w-full bg-gradient-to-br from-gray-50 via-white to-gray-50 min-h-screen py-4 md:py-6">
        <div class="w-full max-w-4xl mx-auto px-3 sm:px-4 md:px-6 lg:px-8">
            <div class="space-y-4 lg:space-y-5">
                <div class="bg-white rounded-lg shadow-md border border-gray-100 overflow-hidden transform transition-all duration-300 hover:shadow-lg animate-fadeIn">
                    <div class="p-3">
                        <div class="flex items-center gap-3 mb-2.5">
                            <a href="{{ route('profile.index') }}"
                                class="w-9 h-9 flex items-center justify-center text-gray-500 hover:text-gray-700 hover:bg-gray-100 rounded-lg transition-all duration-200">
                                <i class="fas fa-arrow-left"></i>
                            </a>
                            <div>
                                <h2 class="text-lg font-bold text-gray-900 flex items-center gap-2">
                                    <i class="fas fa-key text-primary text-base"></i>
                                    Đổi mật khẩu
                                </h2>
                                <p class="text-xs text-gray-500 mt-0.5">Bảo vệ tài khoản của bạn bằng mật khẩu mạnh</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow-md border border-gray-100 overflow-hidden transform transition-all duration-300 hover:shadow-lg animate-fadeIn"
                    style="animation-delay: 0.1s">
                    <div class="p-4 md:p-5">
                        @if (session('success'))
                            <div class="mb-4 p-3 bg-green-50 border border-green-200 rounded-lg flex items-center gap-2">
                                <i class="fas fa-check-circle text-green-500"></i>
                                <p class="text-sm text-green-700">{{ session('success') }}</p>
                            </div>
                        @endif

                        <form action="{{ route('profile.change-password.post') }}" method="POST" id="changePasswordForm">
                            @csrf

                            <div class="space-y-4">
                                <div>
                                    <label for="current_password" class="flex items-center gap-2 mb-2">
                                        <div class="w-7 h-7 bg-red-100 rounded-lg flex items-center justify-center">
                                            <i class="fas fa-lock text-red-600 text-xs"></i>
                                        </div>
                                        <span class="text-sm font-semibold text-gray-700">
                                            Mật khẩu hiện tại <span class="text-red-500">*</span>
                                        </span>
                                    </label>
                                    <div class="relative">
                                        <input type="password" id="current_password" name="current_password" required
                                            class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent transition-all text-sm"
                                            placeholder="Nhập mật khẩu hiện tại">
                                        <button type="button" onclick="togglePassword('current_password', this)"
                                            class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-gray-600 transition-colors">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    </div>
                                    @error('current_password')
                                        <p class="mt-1.5 text-xs text-red-500 flex items-center gap-1">
                                            <i class="fas fa-exclamation-circle"></i> {{ $message }}
                                        </p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="password" class="flex items-center gap-2 mb-2">
                                        <div class="w-7 h-7 bg-green-100 rounded-lg flex items-center justify-center">
                                            <i class="fas fa-key text-green-600 text-xs"></i>
                                        </div>
                                        <span class="text-sm font-semibold text-gray-700">
                                            Mật khẩu mới <span class="text-red-500">*</span>
                                        </span>
                                    </label>
                                    <div class="relative">
                                        <input type="password" id="password" name="password" required minlength="8"
                                            class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent transition-all text-sm"
                                            placeholder="Nhập mật khẩu mới (tối thiểu 8 ký tự)">
                                        <button type="button" onclick="togglePassword('password', this)"
                                            class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-gray-600 transition-colors">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    </div>
                                    @error('password')
                                        <p class="mt-1.5 text-xs text-red-500 flex items-center gap-1">
                                            <i class="fas fa-exclamation-circle"></i> {{ $message }}
                                        </p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="password_confirmation" class="flex items-center gap-2 mb-2">
                                        <div class="w-7 h-7 bg-blue-100 rounded-lg flex items-center justify-center">
                                            <i class="fas fa-shield-alt text-blue-600 text-xs"></i>
                                        </div>
                                        <span class="text-sm font-semibold text-gray-700">
                                            Xác nhận mật khẩu mới <span class="text-red-500">*</span>
                                        </span>
                                    </label>
                                    <div class="relative">
                                        <input type="password" id="password_confirmation" name="password_confirmation" required minlength="8"
                                            class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent transition-all text-sm"
                                            placeholder="Nhập lại mật khẩu mới">
                                        <button type="button" onclick="togglePassword('password_confirmation', this)"
                                            class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-gray-600 transition-colors">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    </div>
                                </div>

                                <div class="p-3 bg-blue-50 border border-blue-200 rounded-lg">
                                    <div class="flex items-start gap-2">
                                        <i class="fas fa-info-circle text-blue-500 mt-0.5"></i>
                                        <div class="text-xs text-blue-700">
                                            <p class="font-semibold mb-1">Lưu ý:</p>
                                            <ul class="list-disc list-inside space-y-0.5">
                                                <li>Mật khẩu phải có ít nhất 8 ký tự</li>
                                                <li>Nên sử dụng kết hợp chữ hoa, chữ thường, số và ký tự đặc biệt</li>
                                                <li>Không chia sẻ mật khẩu với người khác</li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>

                                <div class="flex flex-col sm:flex-row gap-3 pt-4 border-t border-gray-200">
                                    <button type="submit"
                                        class="flex-1 px-4 py-2.5 bg-gradient-to-r from-primary to-primary-6 hover:from-primary-6 hover:to-primary text-white font-semibold text-sm rounded-lg shadow-md hover:shadow-lg transition-all duration-300 transform hover:scale-[1.01] active:scale-[0.99]">
                                        <i class="fas fa-save mr-2"></i> Đổi mật khẩu
                                    </button>
                                    <a href="{{ route('profile.index') }}"
                                        class="flex-1 px-4 py-2.5 bg-gradient-to-r from-gray-100 to-gray-200 hover:from-gray-200 hover:to-gray-300 text-gray-700 font-semibold text-sm rounded-lg shadow-sm hover:shadow-md transition-all duration-300 transform hover:scale-[1.01] active:scale-[0.99] text-center">
                                        <i class="fas fa-times mr-2"></i> Hủy
                                    </a>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        function togglePassword(inputId, button) {
            const input = document.getElementById(inputId);
            const icon = button.querySelector('i');
            
            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                input.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        }

        document.getElementById('password')?.addEventListener('input', function() {
            const password = this.value;
            const confirmation = document.getElementById('password_confirmation');
            
            if (confirmation && confirmation.value) {
                if (password !== confirmation.value) {
                    confirmation.setCustomValidity('Mật khẩu xác nhận không khớp');
                } else {
                    confirmation.setCustomValidity('');
                }
            }
        });

        document.getElementById('password_confirmation')?.addEventListener('input', function() {
            const password = document.getElementById('password').value;
            const confirmation = this.value;
            
            if (password !== confirmation) {
                this.setCustomValidity('Mật khẩu xác nhận không khớp');
            } else {
                this.setCustomValidity('');
            }
        });
    </script>
@endpush

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

        .animate-fadeIn {
            animation: fadeIn 0.6s ease-out forwards;
            opacity: 0;
        }
    </style>
@endpush
