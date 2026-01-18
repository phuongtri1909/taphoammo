@extends('client.layouts.app')

@section('title', 'Bảo mật 2 lớp - ' . config('app.name'))

@section('content')
<div class="w-full bg-gradient-to-br from-gray-50 via-white to-gray-50 min-h-screen py-4 md:py-6">
    <div class="w-full max-w-3xl mx-auto px-3 sm:px-4 md:px-6">
        <div class="mb-6">
            <a href="{{ route('profile.index') }}" class="inline-flex items-center text-sm text-gray-600 hover:text-primary transition-colors">
                <i class="fas fa-arrow-left mr-2"></i>
                Quay lại trang cá nhân
            </a>
            <h1 class="text-xl font-bold text-gray-900 mt-3">Bảo mật 2 lớp (2FA)</h1>
            <p class="text-sm text-gray-600 mt-1">Tăng cường bảo mật cho tài khoản của bạn</p>
        </div>

        @if(session('warning'))
            <div class="mb-4 p-4 bg-yellow-50 border border-yellow-200 rounded-lg">
                <p class="text-sm text-yellow-800">
                    <i class="fas fa-exclamation-triangle mr-2"></i>
                    {{ session('warning') }}
                </p>
            </div>
        @endif

        <div class="bg-white rounded-xl shadow-lg border border-gray-100 overflow-hidden">
            <div class="p-5 md:p-6">
                <div class="flex items-center justify-between p-4 rounded-lg mb-6 {{ $user->hasTwoFactorEnabled() ? 'bg-green-50 border border-green-200' : 'bg-orange-50 border border-orange-200' }}">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-full flex items-center justify-center {{ $user->hasTwoFactorEnabled() ? 'bg-green-500' : 'bg-orange-500' }}">
                            <i class="fas {{ $user->hasTwoFactorEnabled() ? 'fa-check' : 'fa-exclamation-triangle' }} text-white"></i>
                        </div>
                        <div>
                            <p class="font-semibold text-gray-900">
                                {{ $user->hasTwoFactorEnabled() ? 'Bảo mật 2 lớp đang BẬT' : 'Bảo mật 2 lớp đang TẮT' }}
                            </p>
                            <p class="text-xs text-gray-600">
                                {{ $user->hasTwoFactorEnabled() ? 'Tài khoản của bạn được bảo vệ' : 'Hãy bật để bảo vệ tài khoản' }}
                            </p>
                        </div>
                    </div>
                </div>

                @if(!$user->hasTwoFactorEnabled())
                    <div id="enableSection">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Bật bảo mật 2 lớp</h3>
                        
                        <div class="space-y-4 mb-6">
                            <div class="flex gap-3">
                                <div class="w-8 h-8 bg-primary/10 rounded-full flex items-center justify-center flex-shrink-0">
                                    <span class="text-sm font-bold text-primary">1</span>
                                </div>
                                <div>
                                    <p class="font-medium text-gray-900">Tải ứng dụng xác thực</p>
                                    <p class="text-sm text-gray-600">Tải Google Authenticator hoặc Authy trên điện thoại của bạn.</p>
                                </div>
                            </div>
                            <div class="flex gap-3">
                                <div class="w-8 h-8 bg-primary/10 rounded-full flex items-center justify-center flex-shrink-0">
                                    <span class="text-sm font-bold text-primary">2</span>
                                </div>
                                <div>
                                    <p class="font-medium text-gray-900">Quét mã QR</p>
                                    <p class="text-sm text-gray-600">Mở ứng dụng và quét mã QR bên dưới.</p>
                                </div>
                            </div>
                            <div class="flex gap-3">
                                <div class="w-8 h-8 bg-primary/10 rounded-full flex items-center justify-center flex-shrink-0">
                                    <span class="text-sm font-bold text-primary">3</span>
                                </div>
                                <div>
                                    <p class="font-medium text-gray-900">Nhập mã xác thực</p>
                                    <p class="text-sm text-gray-600">Nhập mã 6 số từ ứng dụng để hoàn tất.</p>
                                </div>
                            </div>
                        </div>

                        <button id="btnStartSetup" onclick="startSetup()" class="w-full py-3 px-4 bg-gradient-to-r from-primary to-primary-6 text-white font-semibold rounded-lg hover:shadow-lg transition-all">
                            <i class="fas fa-shield-alt mr-2"></i>
                            Bắt đầu thiết lập
                        </button>
                    </div>

                    <div id="qrSection" class="hidden">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Quét mã QR</h3>
                        
                        <div class="flex flex-col items-center mb-6">
                            <div id="qrCodeContainer" class="p-4 bg-white border-2 border-gray-200 rounded-lg mb-4">
                            </div>
                            
                            <p class="text-sm text-gray-600 mb-2">Hoặc nhập mã thủ công:</p>
                            <div class="flex items-center gap-2 p-3 bg-gray-100 rounded-lg">
                                <code id="secretKey" class="text-sm font-mono text-gray-800"></code>
                                <button onclick="copySecret()" class="text-primary hover:text-primary-6 transition-colors">
                                    <i class="fas fa-copy"></i>
                                </button>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label for="verifyCode" class="block text-sm font-medium text-gray-700 mb-2">
                                Nhập mã xác thực từ ứng dụng
                            </label>
                            <input type="text" id="verifyCode" 
                                class="w-full py-3 px-4 border border-gray-300 rounded-lg text-center tracking-[0.5em] font-mono text-lg"
                                placeholder="000000" maxlength="6" pattern="[0-9]*">
                        </div>

                        <div class="flex gap-3">
                            <button onclick="cancelSetup()" class="flex-1 py-3 px-4 border border-gray-300 text-gray-700 font-semibold rounded-lg hover:bg-gray-50 transition-all">
                                Hủy
                            </button>
                            <button id="btnConfirm" onclick="confirmSetup()" class="flex-1 py-3 px-4 bg-gradient-to-r from-primary to-primary-6 text-white font-semibold rounded-lg hover:shadow-lg transition-all">
                                Xác nhận
                            </button>
                        </div>
                    </div>

                    <div id="recoverySection" class="hidden">
                        <div class="text-center mb-6">
                            <div class="w-16 h-16 mx-auto bg-green-100 rounded-full flex items-center justify-center mb-4">
                                <i class="fas fa-check text-2xl text-green-600"></i>
                            </div>
                            <h3 class="text-lg font-semibold text-gray-900">Bảo mật 2 lớp đã được bật!</h3>
                            <p class="text-sm text-gray-600 mt-1">Lưu các mã khôi phục dưới đây để đề phòng mất điện thoại.</p>
                        </div>

                        <div class="bg-orange-50 border border-orange-200 rounded-lg p-4 mb-4">
                            <p class="text-sm text-orange-800">
                                <i class="fas fa-exclamation-triangle mr-2"></i>
                                <strong>Quan trọng:</strong> Lưu các mã này ở nơi an toàn. Mỗi mã chỉ dùng được một lần.
                            </p>
                        </div>

                        <div id="recoveryCodesGrid" class="grid grid-cols-2 gap-2 mb-6">
                        </div>

                        <div class="flex gap-3">
                            <button onclick="downloadRecoveryCodes()" class="flex-1 py-3 px-4 border border-gray-300 text-gray-700 font-semibold rounded-lg hover:bg-gray-50 transition-all">
                                <i class="fas fa-download mr-2"></i>
                                Tải xuống
                            </button>
                            <button onclick="finishSetup()" class="flex-1 py-3 px-4 bg-gradient-to-r from-primary to-primary-6 text-white font-semibold rounded-lg hover:shadow-lg transition-all">
                                Hoàn tất
                            </button>
                        </div>
                    </div>
                @else
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Quản lý bảo mật 2 lớp</h3>

                        <div class="space-y-4">
                            <button onclick="showRecoveryCodes()" class="w-full flex items-center justify-between p-4 border border-gray-200 rounded-lg hover:bg-gray-50 transition-all">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                                        <i class="fas fa-key text-blue-600"></i>
                                    </div>
                                    <div class="text-left">
                                        <p class="font-medium text-gray-900">Xem mã khôi phục</p>
                                        <p class="text-xs text-gray-600">Xem lại các mã khôi phục của bạn</p>
                                    </div>
                                </div>
                                <i class="fas fa-chevron-right text-gray-400"></i>
                            </button>

                            <button onclick="regenerateRecoveryCodes()" class="w-full flex items-center justify-between p-4 border border-gray-200 rounded-lg hover:bg-gray-50 transition-all">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center">
                                        <i class="fas fa-sync text-purple-600"></i>
                                    </div>
                                    <div class="text-left">
                                        <p class="font-medium text-gray-900">Tạo mã khôi phục mới</p>
                                        <p class="text-xs text-gray-600">Vô hiệu hóa mã cũ và tạo mã mới</p>
                                    </div>
                                </div>
                                <i class="fas fa-chevron-right text-gray-400"></i>
                            </button>

                            <button onclick="disableTwoFactor()" class="w-full flex items-center justify-between p-4 border border-red-200 rounded-lg hover:bg-red-50 transition-all">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 bg-red-100 rounded-lg flex items-center justify-center">
                                        <i class="fas fa-times text-red-600"></i>
                                    </div>
                                    <div class="text-left">
                                        <p class="font-medium text-red-600">Tắt bảo mật 2 lớp</p>
                                        <p class="text-xs text-gray-600">Không khuyến khích - tài khoản sẽ kém an toàn hơn</p>
                                    </div>
                                </div>
                                <i class="fas fa-chevron-right text-gray-400"></i>
                            </button>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<div id="passwordModal" class="hidden fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-xl max-w-md w-full p-6">
        <h3 id="modalTitle" class="text-lg font-semibold text-gray-900 mb-4">Xác nhận mật khẩu</h3>
        <p id="modalDescription" class="text-sm text-gray-600 mb-4">Nhập mật khẩu để tiếp tục.</p>
        
        <input type="password" id="modalPassword" 
            class="w-full py-3 px-4 border border-gray-300 rounded-lg mb-4"
            placeholder="Nhập mật khẩu">
        
        <div class="flex gap-3">
            <button onclick="closePasswordModal()" class="flex-1 py-3 px-4 border border-gray-300 text-gray-700 font-semibold rounded-lg hover:bg-gray-50 transition-all">
                Hủy
            </button>
            <button id="modalConfirmBtn" class="flex-1 py-3 px-4 bg-gradient-to-r from-primary to-primary-6 text-white font-semibold rounded-lg hover:shadow-lg transition-all">
                Xác nhận
            </button>
        </div>
    </div>
</div>

<div id="recoveryModal" class="hidden fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-xl max-w-md w-full p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Mã khôi phục</h3>
        
        <div class="bg-orange-50 border border-orange-200 rounded-lg p-3 mb-4">
            <p class="text-xs text-orange-800">
                <i class="fas fa-exclamation-triangle mr-1"></i>
                Mỗi mã chỉ dùng được một lần. Hãy lưu ở nơi an toàn.
            </p>
        </div>

        <div id="modalRecoveryCodesGrid" class="grid grid-cols-2 gap-2 mb-4">
        </div>
        
        <button onclick="closeRecoveryModal()" class="w-full py-3 px-4 bg-gradient-to-r from-primary to-primary-6 text-white font-semibold rounded-lg hover:shadow-lg transition-all">
            Đóng
        </button>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function showStyledAlert(type, title, message, options = {}) {
        const configs = {
            error: {
                icon: 'error',
                iconColor: '#ef4444',
                confirmButtonColor: '#ef4444',
                html: `
                    <div style="text-align: center; padding: 1rem 0;">
                        <div style="width: 80px; height: 80px; margin: 0 auto 1.5rem; background: linear-gradient(135deg, #ef4444, #dc2626); border-radius: 50%; display: flex; align-items: center; justify-content: center; box-shadow: 0 10px 25px rgba(239, 68, 68, 0.3);">
                            <i class="fas fa-times-circle" style="font-size: 40px; color: white;"></i>
                        </div>
                        <p style="font-size: 16px; color: #374151; margin: 0; font-weight: 600;">${message}</p>
                    </div>
                `
            },
            success: {
                icon: 'success',
                iconColor: '#10b981',
                confirmButtonColor: '#10b981',
                html: `
                    <div style="text-align: center; padding: 1rem 0;">
                        <div style="width: 80px; height: 80px; margin: 0 auto 1.5rem; background: linear-gradient(135deg, #10b981, #059669); border-radius: 50%; display: flex; align-items: center; justify-content: center; box-shadow: 0 10px 25px rgba(16, 185, 129, 0.3);">
                            <i class="fas fa-check-circle" style="font-size: 40px; color: white;"></i>
                        </div>
                        <p style="font-size: 16px; color: #374151; margin: 0; font-weight: 600;">${message}</p>
                    </div>
                `
            },
            warning: {
                icon: 'warning',
                iconColor: '#f59e0b',
                confirmButtonColor: '#f59e0b',
                html: `
                    <div style="text-align: center; padding: 1rem 0;">
                        <div style="width: 80px; height: 80px; margin: 0 auto 1.5rem; background: linear-gradient(135deg, #f59e0b, #d97706); border-radius: 50%; display: flex; align-items: center; justify-content: center; box-shadow: 0 10px 25px rgba(245, 158, 11, 0.3);">
                            <i class="fas fa-exclamation-triangle" style="font-size: 40px; color: white;"></i>
                        </div>
                        <p style="font-size: 16px; color: #374151; margin: 0; font-weight: 600;">${message}</p>
                    </div>
                `
            }
        };

        const config = configs[type] || configs.error;
        
        return Swal.fire({
            title: title ? `<div style="font-size: 24px; font-weight: 700; color: #1f2937; margin-bottom: 8px;">${title}</div>` : '',
            html: options.html || config.html,
            showCancelButton: options.showCancelButton || false,
            confirmButtonColor: options.confirmButtonColor || config.confirmButtonColor,
            cancelButtonColor: options.cancelButtonColor || '#6b7280',
            confirmButtonText: options.confirmButtonText || '<i class="fas fa-check-circle mr-2"></i>Đồng ý',
            cancelButtonText: options.cancelButtonText || '<i class="fas fa-times mr-2"></i>Hủy',
            width: options.width || '480px',
            padding: '2rem',
            background: '#ffffff',
            customClass: {
                popup: 'rounded-2xl shadow-2xl border border-gray-200',
                title: 'mb-0 pb-4',
                confirmButton: 'px-6 py-3 rounded-lg font-semibold shadow-lg hover:shadow-xl transition-all duration-200 transform hover:scale-105',
                cancelButton: 'px-6 py-3 rounded-lg font-semibold hover:bg-gray-100 transition-all duration-200',
                actions: 'gap-3 mt-4'
            },
            buttonsStyling: true,
            focusConfirm: false,
            allowOutsideClick: options.allowOutsideClick !== false,
            allowEscapeKey: options.allowEscapeKey !== false,
            ...options
        });
    }

    let currentRecoveryCodes = [];

    function startSetup() {
        const btn = document.getElementById('btnStartSetup');
        btn.disabled = true;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Đang khởi tạo...';

        fetch('{{ route("2fa.enable") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                document.getElementById('qrCodeContainer').innerHTML = data.qr_code;
                document.getElementById('secretKey').textContent = data.secret;
                document.getElementById('enableSection').classList.add('hidden');
                document.getElementById('qrSection').classList.remove('hidden');
            } else {
                showStyledAlert('error', 'Lỗi', data.message);
            }
        })
        .catch(error => {
            showStyledAlert('error', 'Lỗi', 'Có lỗi xảy ra. Vui lòng thử lại.');
        })
        .finally(() => {
            btn.disabled = false;
            btn.innerHTML = '<i class="fas fa-shield-alt mr-2"></i>Bắt đầu thiết lập';
        });
    }

    function cancelSetup() {
        document.getElementById('qrSection').classList.add('hidden');
        document.getElementById('enableSection').classList.remove('hidden');
        document.getElementById('verifyCode').value = '';
    }

    function confirmSetup() {
        const code = document.getElementById('verifyCode').value;
        if (code.length !== 6) {
            showStyledAlert('error', 'Lỗi', 'Vui lòng nhập mã 6 số');
            return;
        }

        const btn = document.getElementById('btnConfirm');
        btn.disabled = true;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Đang xác thực...';

        fetch('{{ route("2fa.confirm") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ code: code })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                currentRecoveryCodes = data.recovery_codes;
                const grid = document.getElementById('recoveryCodesGrid');
                grid.innerHTML = data.recovery_codes.map(code => 
                    `<div class="p-2 bg-gray-100 rounded text-center font-mono text-sm">${code}</div>`
                ).join('');
                
                document.getElementById('qrSection').classList.add('hidden');
                document.getElementById('recoverySection').classList.remove('hidden');
            } else {
                showStyledAlert('error', 'Lỗi', data.message);
            }
        })
        .catch(error => {
            showStyledAlert('error', 'Lỗi', 'Có lỗi xảy ra. Vui lòng thử lại.');
        })
        .finally(() => {
            btn.disabled = false;
            btn.innerHTML = 'Xác nhận';
        });
    }

    function downloadRecoveryCodes() {
        const codes = currentRecoveryCodes.join('\n');
        const blob = new Blob([codes], { type: 'text/plain' });
        const url = URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = url;
        a.download = '{{ config("app.name") }}_recovery_codes.txt';
        a.click();
        URL.revokeObjectURL(url);
    }

    function finishSetup() {
        window.location.reload();
    }

    function copySecret() {
        const secret = document.getElementById('secretKey').textContent;
        navigator.clipboard.writeText(secret).then(() => {
            Swal.fire({
                toast: true,
                position: 'top-end',
                icon: 'success',
                title: 'Đã sao chép!',
                showConfirmButton: false,
                timer: 1500
            });
        });
    }

    let modalCallback = null;

    function showPasswordModal(title, description, callback) {
        document.getElementById('modalTitle').textContent = title;
        document.getElementById('modalDescription').textContent = description;
        document.getElementById('modalPassword').value = '';
        document.getElementById('passwordModal').classList.remove('hidden');
        modalCallback = callback;
        
        document.getElementById('modalConfirmBtn').onclick = function() {
            const password = document.getElementById('modalPassword').value;
            if (!password) {
                showStyledAlert('error', 'Lỗi', 'Vui lòng nhập mật khẩu');
                return;
            }
            closePasswordModal();
            callback(password);
        };
    }

    function closePasswordModal() {
        document.getElementById('passwordModal').classList.add('hidden');
    }

    function closeRecoveryModal() {
        document.getElementById('recoveryModal').classList.add('hidden');
    }

    function showRecoveryCodes() {
        showPasswordModal('Xem mã khôi phục', 'Nhập mật khẩu để xem các mã khôi phục.', function(password) {
            fetch('{{ route("2fa.recovery-codes") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ password: password })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const grid = document.getElementById('modalRecoveryCodesGrid');
                    grid.innerHTML = data.recovery_codes.map(code => 
                        `<div class="p-2 bg-gray-100 rounded text-center font-mono text-sm">${code}</div>`
                    ).join('');
                    document.getElementById('recoveryModal').classList.remove('hidden');
                } else {
                    showStyledAlert('error', 'Lỗi', data.message);
                }
            })
            .catch(error => {
                showStyledAlert('error', 'Lỗi', 'Có lỗi xảy ra. Vui lòng thử lại.');
            });
        });
    }

    function regenerateRecoveryCodes() {
        showStyledAlert('warning', 'Xác nhận tạo mã mới', 'Các mã khôi phục cũ sẽ bị vô hiệu hóa. Bạn có chắc chắn muốn tiếp tục?', {
            showCancelButton: true,
            confirmButtonColor: '#3b82f6',
            cancelButtonColor: '#6b7280',
            confirmButtonText: '<i class="fas fa-check-circle mr-2"></i>Đồng ý',
            cancelButtonText: '<i class="fas fa-times mr-2"></i>Hủy',
            width: '500px'
        }).then((result) => {
            if (result.isConfirmed) {
                showPasswordModal('Tạo mã khôi phục mới', 'Nhập mật khẩu để tạo mã mới.', function(password) {
                    fetch('{{ route("2fa.regenerate-recovery-codes") }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({ password: password })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            currentRecoveryCodes = data.recovery_codes;
                            const grid = document.getElementById('modalRecoveryCodesGrid');
                            grid.innerHTML = data.recovery_codes.map(code => 
                                `<div class="p-2 bg-gray-100 rounded text-center font-mono text-sm">${code}</div>`
                            ).join('');
                            document.getElementById('recoveryModal').classList.remove('hidden');
                            showStyledAlert('success', 'Thành công', data.message, {
                                showConfirmButton: false,
                                timer: 2000,
                                width: '400px'
                            });
                        } else {
                            showStyledAlert('error', 'Lỗi', data.message);
                        }
                    })
                    .catch(error => {
                        showStyledAlert('error', 'Lỗi', 'Có lỗi xảy ra. Vui lòng thử lại.');
                    });
                });
            }
        });
    }

    function disableTwoFactor() {
        showStyledAlert('warning', 'Tắt bảo mật 2 lớp?', 'Tài khoản của bạn sẽ kém an toàn hơn. Bạn có chắc chắn muốn tắt bảo mật 2 lớp?', {
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            cancelButtonColor: '#6b7280',
            confirmButtonText: '<i class="fas fa-times-circle mr-2"></i>Tắt 2FA',
            cancelButtonText: '<i class="fas fa-times mr-2"></i>Hủy',
            width: '500px'
        }).then((result) => {
            if (result.isConfirmed) {
                showPasswordModal('Tắt bảo mật 2 lớp', 'Nhập mật khẩu để xác nhận.', function(password) {
                    fetch('{{ route("2fa.disable") }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({ password: password })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            showStyledAlert('success', 'Thành công', data.message).then(() => {
                                window.location.reload();
                            });
                        } else {
                            showStyledAlert('error', 'Lỗi', data.message);
                        }
                    })
                    .catch(error => {
                        showStyledAlert('error', 'Lỗi', 'Có lỗi xảy ra. Vui lòng thử lại.');
                    });
                });
            }
        });
    }

    document.getElementById('verifyCode')?.addEventListener('input', function(e) {
        this.value = this.value.replace(/[^0-9]/g, '');
    });
</script>
@endpush

