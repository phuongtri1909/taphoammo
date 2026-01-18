@extends('client.layouts.app')

@section('title', 'Đăng ký bán hàng - ' . config('app.name'))

@section('content')
<div class="w-full bg-gradient-to-br from-gray-50 via-white to-gray-50 min-h-screen py-4 md:py-6">
    <div class="w-full max-w-2xl mx-auto px-3 sm:px-4 md:px-6">
        <!-- Header -->
        <div class="mb-6">
            <a href="{{ route('home') }}" class="inline-flex items-center text-sm text-gray-600 hover:text-primary transition-colors">
                <i class="fas fa-arrow-left mr-2"></i>
                Quay lại trang chủ
            </a>
            <h1 class="text-xl font-bold text-gray-900 mt-3">Đăng ký bán hàng</h1>
            <p class="text-sm text-gray-600 mt-1">Trở thành người bán trên {{ config('app.name') }}</p>
        </div>

        @if($rejectedRegistration)
            <div class="mb-4 p-4 bg-red-50 border border-red-200 rounded-lg">
                <p class="text-sm text-red-800 font-medium mb-1">
                    <i class="fas fa-times-circle mr-2"></i>
                    Đăng ký trước của bạn đã bị từ chối
                </p>
                @if($rejectedRegistration->admin_note)
                    <p class="text-sm text-red-700">Lý do: {{ $rejectedRegistration->admin_note }}</p>
                @endif
                <p class="text-xs text-red-600 mt-2">Vui lòng điền lại thông tin chính xác.</p>
            </div>
        @endif

        <!-- Main Card -->
        <div class="bg-white rounded-xl shadow-lg border border-gray-100 overflow-hidden">
            <div class="p-5 md:p-6">
                <!-- Benefits -->
                <div class="mb-6 p-4 bg-gradient-to-r from-primary/5 to-primary-6/5 rounded-lg border border-primary/20">
                    <h3 class="font-semibold text-gray-900 mb-3">Lợi ích khi trở thành người bán:</h3>
                    <ul class="space-y-2">
                        <li class="flex items-center gap-2 text-sm text-gray-700">
                            <i class="fas fa-check-circle text-green-500"></i>
                            Tiếp cận hàng ngàn khách hàng tiềm năng
                        </li>
                        <li class="flex items-center gap-2 text-sm text-gray-700">
                            <i class="fas fa-check-circle text-green-500"></i>
                            Thanh toán tự động, nhanh chóng
                        </li>
                        <li class="flex items-center gap-2 text-sm text-gray-700">
                            <i class="fas fa-check-circle text-green-500"></i>
                            Hỗ trợ 24/7 từ đội ngũ chăm sóc
                        </li>
                        <li class="flex items-center gap-2 text-sm text-gray-700">
                            <i class="fas fa-check-circle text-green-500"></i>
                            Miễn phí đăng ký và quản lý gian hàng
                        </li>
                    </ul>
                </div>

                <form id="sellerForm" onsubmit="submitForm(event)">
                    @csrf
                    
                    <h3 class="font-semibold text-gray-900 mb-4">Thông tin liên hệ</h3>
                    
                    <!-- Phone -->
                    <div class="mb-4">
                        <label for="phone" class="block text-sm font-medium text-gray-700 mb-1">
                            Số điện thoại <span class="text-red-500">*</span>
                        </label>
                        <input type="tel" id="phone" name="phone" 
                            class="w-full py-3 px-4 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all"
                            placeholder="Nhập số điện thoại" required>
                        <p class="text-xs text-gray-500 mt-1">Để liên hệ khi cần thiết</p>
                    </div>

                    <h3 class="font-semibold text-gray-900 mb-4 mt-6">Thông tin ngân hàng</h3>
                    
                    <!-- Bank Name -->
                    <div class="mb-4">
                        <label for="bank_name" class="block text-sm font-medium text-gray-700 mb-1">
                            Ngân hàng <span class="text-red-500">*</span>
                        </label>
                        <select id="bank_name" name="bank_name" 
                            class="w-full py-3 px-4 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all"
                            required>
                            <option value="">Chọn ngân hàng</option>
                            <option value="Vietcombank">Vietcombank - Ngân hàng TMCP Ngoại thương VN</option>
                            <option value="Techcombank">Techcombank - Ngân hàng TMCP Kỹ thương VN</option>
                            <option value="BIDV">BIDV - Ngân hàng TMCP Đầu tư và Phát triển VN</option>
                            <option value="VietinBank">VietinBank - Ngân hàng TMCP Công thương VN</option>
                            <option value="Agribank">Agribank - Ngân hàng NN&PTNT VN</option>
                            <option value="MBBank">MBBank - Ngân hàng TMCP Quân đội</option>
                            <option value="ACB">ACB - Ngân hàng TMCP Á Châu</option>
                            <option value="VPBank">VPBank - Ngân hàng TMCP Việt Nam Thịnh Vượng</option>
                            <option value="TPBank">TPBank - Ngân hàng TMCP Tiên Phong</option>
                            <option value="Sacombank">Sacombank - Ngân hàng TMCP Sài Gòn Thương Tín</option>
                            <option value="HDBank">HDBank - Ngân hàng TMCP Phát triển TP.HCM</option>
                            <option value="VIB">VIB - Ngân hàng TMCP Quốc tế Việt Nam</option>
                            <option value="SHB">SHB - Ngân hàng TMCP Sài Gòn - Hà Nội</option>
                            <option value="Eximbank">Eximbank - Ngân hàng TMCP Xuất Nhập Khẩu VN</option>
                            <option value="MSB">MSB - Ngân hàng TMCP Hàng Hải VN</option>
                            <option value="LienVietPostBank">LienVietPostBank - Ngân hàng TMCP Bưu điện Liên Việt</option>
                            <option value="OCB">OCB - Ngân hàng TMCP Phương Đông</option>
                            <option value="SeABank">SeABank - Ngân hàng TMCP Đông Nam Á</option>
                            <option value="BacABank">BacABank - Ngân hàng TMCP Bắc Á</option>
                            <option value="VietABank">VietABank - Ngân hàng TMCP Việt Á</option>
                            <option value="PVcomBank">PVcomBank - Ngân hàng TMCP Đại Chúng VN</option>
                            <option value="Khác">Khác</option>
                        </select>
                    </div>

                    <!-- Custom Bank Name (hidden by default) -->
                    <div id="customBankNameWrapper" class="mb-4 hidden">
                        <label for="custom_bank_name" class="block text-sm font-medium text-gray-700 mb-1">
                            Tên ngân hàng <span class="text-red-500">*</span>
                        </label>
                        <input type="text" id="custom_bank_name" name="custom_bank_name" 
                            class="w-full py-3 px-4 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all"
                            placeholder="Nhập tên ngân hàng">
                        <p class="text-xs text-gray-500 mt-1">Vui lòng nhập tên ngân hàng của bạn</p>
                    </div>

                    <!-- Bank Account Number -->
                    <div class="mb-4">
                        <label for="bank_account_number" class="block text-sm font-medium text-gray-700 mb-1">
                            Số tài khoản <span class="text-red-500">*</span>
                        </label>
                        <input type="text" id="bank_account_number" name="bank_account_number" 
                            class="w-full py-3 px-4 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all"
                            placeholder="Nhập số tài khoản" required>
                    </div>

                    <!-- Bank Account Name -->
                    <div class="mb-4">
                        <label for="bank_account_name" class="block text-sm font-medium text-gray-700 mb-1">
                            Tên chủ tài khoản <span class="text-red-500">*</span>
                        </label>
                        <input type="text" id="bank_account_name" name="bank_account_name" 
                            class="w-full py-3 px-4 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all uppercase"
                            placeholder="Nhập tên chủ tài khoản (viết in hoa)" required>
                        <p class="text-xs text-gray-500 mt-1">Vui lòng nhập chính xác như trên thẻ ngân hàng</p>
                    </div>

                    <h3 class="font-semibold text-gray-900 mb-4 mt-6">Mạng xã hội (không bắt buộc)</h3>

                    <!-- Facebook -->
                    <div class="mb-4">
                        <label for="facebook_url" class="block text-sm font-medium text-gray-700 mb-1">
                            Facebook
                        </label>
                        <input type="url" id="facebook_url" name="facebook_url" 
                            class="w-full py-3 px-4 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all"
                            placeholder="https://facebook.com/yourpage">
                    </div>

                    <!-- Telegram -->
                    <div class="mb-6">
                        <label for="telegram_username" class="block text-sm font-medium text-gray-700 mb-1">
                            Telegram Username
                        </label>
                        <input type="text" id="telegram_username" name="telegram_username" 
                            class="w-full py-3 px-4 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all"
                            placeholder="@username">
                    </div>

                    <!-- Terms -->
                    <div class="mb-6">
                        <label class="flex items-start gap-2 cursor-pointer">
                            <input type="checkbox" id="terms" name="terms" value="1" class="w-5 h-5 mt-0.5 rounded border-gray-300 text-primary focus:ring-primary" required>
                            <span class="text-sm text-gray-600">
                                Tôi đồng ý với 
                                <a href="#" class="text-primary hover:underline">Điều khoản dịch vụ</a> 
                                và 
                                <a href="#" class="text-primary hover:underline">Chính sách bán hàng</a>
                                của {{ config('app.name') }}
                            </span>
                        </label>
                    </div>

                    <button type="submit" id="submitBtn" class="w-full py-3 px-4 bg-gradient-to-r from-primary to-primary-6 text-white font-semibold rounded-lg hover:shadow-lg transition-all">
                        <i class="fas fa-paper-plane mr-2"></i>
                        Gửi đăng ký
                    </button>
                </form>
            </div>
        </div>

        <!-- Note -->
        <div class="mt-4 p-4 bg-blue-50 border border-blue-200 rounded-lg">
            <p class="text-sm text-blue-800">
                <i class="fas fa-info-circle mr-2"></i>
                Đơn đăng ký của bạn sẽ được xem xét trong vòng 24-48 giờ. Chúng tôi sẽ liên hệ qua email hoặc số điện thoại nếu cần thêm thông tin.
            </p>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Helper function for styled Swal alerts
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

    // Show/hide custom bank name input
    const bankNameSelect = document.getElementById('bank_name');
    const customBankNameWrapper = document.getElementById('customBankNameWrapper');
    const customBankNameInput = document.getElementById('custom_bank_name');

    bankNameSelect.addEventListener('change', function() {
        if (this.value === 'Khác') {
            customBankNameWrapper.classList.remove('hidden');
            customBankNameInput.setAttribute('required', 'required');
        } else {
            customBankNameWrapper.classList.add('hidden');
            customBankNameInput.removeAttribute('required');
            customBankNameInput.value = '';
        }
    });

    // Auto uppercase bank account name
    document.getElementById('bank_account_name').addEventListener('input', function() {
        this.value = this.value.toUpperCase();
    });

    // Only allow numbers in bank account number
    document.getElementById('bank_account_number').addEventListener('input', function() {
        this.value = this.value.replace(/[^0-9]/g, '');
    });

    function submitForm(e) {
        e.preventDefault();

        const form = document.getElementById('sellerForm');
        const btn = document.getElementById('submitBtn');
        
        if (!document.getElementById('terms').checked) {
            showStyledAlert('warning', 'Thông báo', 'Vui lòng đồng ý với điều khoản dịch vụ.');
            return;
        }

        btn.disabled = true;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Đang gửi...';

        const formData = new FormData(form);
        const data = Object.fromEntries(formData.entries());

        fetch('{{ route("seller.register.store") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify(data)
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showStyledAlert('success', 'Thành công!', data.message, {
                    confirmButtonText: '<i class="fas fa-check-circle mr-2"></i>Đồng ý'
                }).then(() => {
                    if (data.redirect) {
                        window.location.href = data.redirect;
                    }
                });
            } else {
                showStyledAlert('error', 'Lỗi', data.message);
                if (data.redirect) {
                    setTimeout(() => {
                        window.location.href = data.redirect;
                    }, 2000);
                }
            }
        })
        .catch(error => {
            showStyledAlert('error', 'Lỗi', 'Có lỗi xảy ra. Vui lòng thử lại.');
        })
        .finally(() => {
            btn.disabled = false;
            btn.innerHTML = '<i class="fas fa-paper-plane mr-2"></i>Gửi đăng ký';
        });
    }
</script>
@endpush

