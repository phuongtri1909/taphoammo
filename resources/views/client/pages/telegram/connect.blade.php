@extends('client.layouts.app')

@section('title', 'Kết nối Telegram - ' . config('app.name'))

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
                                    <i class="fab fa-telegram text-primary text-base"></i>
                                    Kết nối Telegram
                                </h2>
                                <p class="text-xs text-gray-500 mt-0.5">Nhận thông báo về đơn hàng và giao dịch qua Telegram</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow-md border border-gray-100 overflow-hidden transform transition-all duration-300 hover:shadow-lg animate-fadeIn"
                    style="animation-delay: 0.1s">
                    <div class="p-4 md:p-5">
                        <div class="space-y-4">
                            <!-- Hướng dẫn -->
                            <div class="bg-gradient-to-r from-blue-50 to-indigo-50 border border-blue-200 rounded-lg p-4">
                                <h3 class="text-sm font-bold text-gray-900 mb-3 flex items-center gap-2">
                                    <i class="fas fa-info-circle text-blue-600"></i>
                                    Hướng dẫn kết nối
                                </h3>
                                <ol class="space-y-2 text-xs text-gray-700">
                                    <li class="flex items-start gap-2">
                                        <span class="font-bold text-blue-600">1.</span>
                                        <span>Mở ứng dụng Telegram trên điện thoại hoặc máy tính của bạn</span>
                                    </li>
                                    <li class="flex items-start gap-2">
                                        <span class="font-bold text-blue-600">2.</span>
                                        <span>Tìm kiếm bot: <strong class="text-primary">@ {{ $botUsername }}</strong></span>
                                    </li>
                                    <li class="flex items-start gap-2">
                                        <span class="font-bold text-blue-600">3.</span>
                                        <span>Nhấn nút <strong>"Start"</strong> hoặc gửi lệnh <code class="bg-gray-200 px-1 rounded">/start</code></span>
                                    </li>
                                    <li class="flex items-start gap-2">
                                        <span class="font-bold text-blue-600">4.</span>
                                        <span>Sao chép mã xác nhận bên dưới và gửi cho bot</span>
                                    </li>
                                    <li class="flex items-start gap-2">
                                        <span class="font-bold text-blue-600">5.</span>
                                        <span>Chờ vài giây, sau đó quay lại trang này để kiểm tra trạng thái</span>
                                    </li>
                                </ol>
                            </div>

                            <!-- Mã xác nhận -->
                            <div class="bg-gradient-to-r from-green-50 to-emerald-50 border border-green-200 rounded-lg p-4">
                                <label class="block text-xs font-semibold text-gray-700 mb-2">
                                    <i class="fas fa-key text-green-600"></i>
                                    Mã xác nhận của bạn:
                                </label>
                                <div class="flex items-center gap-2">
                                    <input type="text" 
                                        id="verificationCode" 
                                        value="{{ $verificationCode }}" 
                                        readonly
                                        class="flex-1 px-4 py-3 bg-white border-2 border-green-300 rounded-lg text-lg font-bold text-center text-gray-900 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent">
                                    <button 
                                        onclick="copyCode()"
                                        class="px-4 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors font-semibold text-sm whitespace-nowrap">
                                        <i class="fas fa-copy mr-1"></i>
                                        Sao chép
                                    </button>
                                </div>
                                <p class="text-xs text-gray-600 mt-2">
                                    <i class="fas fa-clock text-orange-500"></i>
                                    Mã này có hiệu lực trong <strong>10 phút</strong>. Nếu hết hạn, vui lòng làm mới trang.
                                </p>
                            </div>

                            <!-- Nút mở Telegram -->
                            @if($botUsername && $botUsername !== 'YourBotName')
                            <div class="text-center">
                                <a href="https://t.me/{{ ltrim($botUsername, '@') }}" 
                                   target="_blank"
                                   class="inline-flex items-center gap-2 px-6 py-3 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition-all duration-200 font-semibold shadow-md hover:shadow-lg">
                                    <i class="fab fa-telegram text-xl"></i>
                                    Mở Telegram Bot
                                </a>
                            </div>
                            @endif

                            <!-- Kiểm tra trạng thái -->
                            <div class="border-t border-gray-200 pt-4">
                                <button 
                                    onclick="checkStatus()"
                                    class="w-full px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors font-semibold text-sm">
                                    <i class="fas fa-sync-alt mr-2"></i>
                                    Kiểm tra trạng thái kết nối
                                </button>
                            </div>

                            <!-- Thông báo -->
                            <div id="statusMessage" class="hidden"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        function copyCode() {
            const codeInput = document.getElementById('verificationCode');
            codeInput.select();
            codeInput.setSelectionRange(0, 99999); // For mobile devices
            
            try {
                document.execCommand('copy');
                
                Swal.fire({
                    icon: 'success',
                    title: 'Đã sao chép!',
                    text: 'Mã xác nhận đã được sao chép vào clipboard',
                    timer: 2000,
                    showConfirmButton: false,
                    toast: true,
                    position: 'top-end'
                });
            } catch (err) {
                // Fallback: Select text
                codeInput.focus();
                codeInput.select();
                
                Swal.fire({
                    icon: 'info',
                    title: 'Vui lòng sao chép thủ công',
                    text: 'Đã chọn mã xác nhận, vui lòng nhấn Ctrl+C (hoặc Cmd+C trên Mac)',
                    timer: 3000,
                    showConfirmButton: false
                });
            }
        }

        function checkStatus() {
            const statusMessage = document.getElementById('statusMessage');
            statusMessage.classList.add('hidden');
            
            Swal.fire({
                title: 'Đang kiểm tra...',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            fetch('{{ route("telegram.status") }}', {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                Swal.close();
                
                if (data.success && data.connected) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Kết nối thành công!',
                        text: 'Bạn đã kết nối Telegram thành công. Bạn sẽ nhận được thông báo qua Telegram.',
                        confirmButtonText: 'Quay lại trang cá nhân'
                    }).then(() => {
                        window.location.href = '{{ route("profile.index") }}';
                    });
                } else {
                    Swal.fire({
                        icon: 'info',
                        title: 'Chưa kết nối',
                        text: 'Vui lòng làm theo hướng dẫn bên trên để kết nối Telegram.',
                        confirmButtonText: 'Đã hiểu'
                    });
                }
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Lỗi!',
                    text: 'Có lỗi xảy ra khi kiểm tra trạng thái. Vui lòng thử lại sau.'
                });
            });
        }

        // Tự động kiểm tra trạng thái mỗi 5 giây
        let statusCheckInterval = setInterval(() => {
            fetch('{{ route("telegram.status") }}', {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success && data.connected) {
                    clearInterval(statusCheckInterval);
                    Swal.fire({
                        icon: 'success',
                        title: 'Kết nối thành công!',
                        text: 'Bạn đã kết nối Telegram thành công.',
                        confirmButtonText: 'Quay lại trang cá nhân'
                    }).then(() => {
                        window.location.href = '{{ route("profile.index") }}';
                    });
                }
            })
            .catch(error => {
                // Ignore errors in auto-check
            });
        }, 5000);

        // Clear interval khi rời khỏi trang
        window.addEventListener('beforeunload', () => {
            clearInterval(statusCheckInterval);
        });
    </script>
@endpush
