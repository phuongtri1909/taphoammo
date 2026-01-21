@extends('client.layouts.app')

@section('title', 'Rút tiền - ' . config('app.name'))

@section('content')
    <div class="w-full bg-gradient-to-br from-gray-50 via-white to-gray-50 min-h-screen py-4 md:py-6">
        <div class="w-full max-w-4xl mx-auto px-3 sm:px-4 md:px-6 lg:px-8">
            <div class="space-y-4 lg:space-y-5">
                <div class="bg-white rounded-lg shadow-md border border-gray-100 overflow-hidden transform transition-all duration-300 hover:shadow-lg animate-fadeIn">
                    <div class="p-3">
                        <div class="flex items-center justify-between mb-2.5">
                            <h2 class="text-lg font-bold text-gray-900 flex items-center gap-2">
                                <i class="fas fa-money-bill-wave text-green-500 text-base"></i>
                                Rút tiền về ngân hàng
                            </h2>
                            <div class="text-right">
                                <p class="text-xs text-gray-500">Số dư khả dụng</p>
                                <p class="text-base font-bold text-green-600">{{ number_format($balance, 0, ',', '.') }}₫</p>
                            </div>
                        </div>

                        <form id="withdrawalForm" class="space-y-3" enctype="multipart/form-data">
                            <!-- Amount -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Số tiền rút</label>
                                <div class="grid grid-cols-3 md:grid-cols-5 gap-2 mb-2">
                                    @foreach([50000, 100000, 200000, 500000, 1000000] as $amount)
                                        <button type="button" 
                                            class="amount-btn px-3 py-2 text-sm font-medium border-2 rounded-lg transition-all duration-200 hover:border-green-500 hover:bg-green-50"
                                            data-amount="{{ $amount }}">
                                            {{ number_format($amount / 1000) }}K
                                        </button>
                                    @endforeach
                                </div>
                                <div class="flex items-center gap-2 mb-1">
                                    <input type="number" name="amount" id="amountInput" 
                                        class="flex-1 px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500"
                                        placeholder="Số tiền khác" min="50000" step="10000" required>
                                    <span class="text-gray-500 text-sm">₫</span>
                                </div>
                                <p class="text-xs text-gray-500">Tối thiểu: 50,000₫ | Phải là bội số của 10,000₫</p>
                            </div>

                            <!-- Bank Info -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Tên ngân hàng</label>
                                    <input type="text" name="bank_name" id="bank_name" value="{{ Auth::user()->bank_name ?? '' }}"
                                        class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500"
                                        placeholder="VD: Vietcombank, MB Bank..." required>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Số tài khoản</label>
                                    <input type="text" name="bank_account_number" id="bank_account_number" value="{{ Auth::user()->bank_account_number ?? '' }}"
                                        class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500"
                                        placeholder="Nhập số tài khoản" required>
                                </div>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Tên chủ tài khoản</label>
                                <input type="text" name="bank_account_name" id="bank_account_name" value="{{ Auth::user()->bank_account_name ?? '' }}"
                                    class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500"
                                    placeholder="Nhập đúng tên chủ tài khoản (không dấu)" required>
                            </div>

                            <!-- Save Bank Info Checkbox -->
                            <div class="flex items-center gap-2">
                                <input type="checkbox" name="save_bank_info" id="save_bank_info" value="1" 
                                    class="w-4 h-4 text-green-600 border-gray-300 rounded focus:ring-green-500"
                                    {{ Auth::user()->bank_name ? 'checked' : '' }}>
                                <label for="save_bank_info" class="text-sm text-gray-700 cursor-pointer">
                                    Lưu thông tin ngân hàng để sử dụng lần sau
                                </label>
                            </div>

                            @if(Auth::user()->bank_name || Auth::user()->bank_account_number || Auth::user()->qr_code)
                                <div class="p-3 bg-blue-50 border border-blue-200 rounded-lg">
                                    <div class="flex items-center justify-between mb-2">
                                        <span class="text-sm font-medium text-blue-700">
                                            <i class="fas fa-info-circle"></i> Thông tin ngân hàng đã lưu
                                        </span>
                                        <button type="button" id="clearBankInfoBtn" 
                                            class="text-xs text-red-600 hover:text-red-800 font-medium">
                                            <i class="fas fa-trash-alt"></i> Xóa thông tin đã lưu
                                        </button>
                                    </div>
                                    <div class="text-xs text-blue-600 space-y-1">
                                        @if(Auth::user()->bank_name)
                                            <p><strong>Ngân hàng:</strong> {{ Auth::user()->bank_name }}</p>
                                        @endif
                                        @if(Auth::user()->bank_account_number)
                                            <p><strong>Số tài khoản:</strong> {{ Auth::user()->bank_account_number }}</p>
                                        @endif
                                        @if(Auth::user()->bank_account_name)
                                            <p><strong>Chủ tài khoản:</strong> {{ Auth::user()->bank_account_name }}</p>
                                        @endif
                                    </div>
                                </div>
                            @endif

                            <!-- QR Code Upload -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Mã QR ngân hàng</label>
                                <div class="space-y-2">
                                    @if(Auth::user()->qr_code)
                                        <div class="mb-2">
                                            <img src="{{ Storage::url(Auth::user()->qr_code) }}" alt="QR Code" 
                                                class="max-w-xs h-auto border border-gray-300 rounded-lg p-2 bg-white">
                                            <p class="text-xs text-gray-500 mt-1">QR Code hiện tại</p>
                                        </div>
                                    @endif
                                    <input type="file" name="qr_code" id="qr_code" accept="image/*"
                                        class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500">
                                    <p class="text-xs text-gray-500">Chỉ chấp nhận file ảnh (JPG, PNG, GIF, WEBP). Ảnh sẽ được tự động giảm dung lượng.</p>
                                </div>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Ghi chú (tùy chọn)</label>
                                <textarea name="note" rows="2" 
                                    class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500"
                                    placeholder="Ghi chú cho yêu cầu rút tiền"></textarea>
                            </div>

                            <button type="submit" id="submitWithdrawal"
                                class="w-full py-3 bg-gradient-to-r from-green-500 to-green-600 text-white font-bold rounded-lg shadow-lg hover:shadow-xl transition-all duration-300 transform hover:-translate-y-0.5">
                                <i class="fas fa-paper-plane mr-2"></i>
                                Gửi yêu cầu rút tiền
                            </button>
                        </form>

                        <div class="mt-3 p-2.5 bg-blue-50 border border-blue-200 rounded-lg">
                            <div class="flex items-start gap-2">
                                <i class="fas fa-info-circle text-blue-500 mt-0.5 text-xs"></i>
                                <div class="text-xs text-blue-700">
                                    <p class="font-bold mb-1">Lưu ý:</p>
                                    <ul class="list-disc list-inside space-y-0.5">
                                        <li>Mã OTP xác thực sẽ được gửi đến email của bạn</li>
                                        <li>Tiền sẽ được trừ ngay sau khi xác thực OTP</li>
                                        <li>Admin sẽ xử lý yêu cầu trong vòng 24h làm việc</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                @if($withdrawals->count() > 0)
                    <div class="bg-white rounded-lg shadow-md border border-gray-100 overflow-hidden animate-fadeIn" style="animation-delay: 0.1s">
                        <div class="p-3 border-b border-gray-100">
                            <h3 class="text-sm font-bold text-gray-900">Lịch sử rút tiền</h3>
                        </div>
                        <div class="divide-y divide-gray-100">
                            @foreach($withdrawals as $withdrawal)
                                <div class="p-3">
                                    <div class="flex items-center justify-between mb-1.5">
                                        <div class="flex items-center gap-3">
                                            <div class="w-8 h-8 {{ $withdrawal->status->badgeColor() === 'success' ? 'bg-green-100' : ($withdrawal->status->badgeColor() === 'warning' ? 'bg-yellow-100' : ($withdrawal->status->badgeColor() === 'info' ? 'bg-blue-100' : 'bg-red-100')) }} rounded-full flex items-center justify-center flex-shrink-0">
                                                <i class="fas {{ $withdrawal->isCompleted() ? 'fa-check' : ($withdrawal->isPending() || $withdrawal->isPendingOtp() || $withdrawal->isProcessing() ? 'fa-clock' : 'fa-times') }} text-xs {{ $withdrawal->status->badgeColor() === 'success' ? 'text-green-600' : ($withdrawal->status->badgeColor() === 'warning' ? 'text-yellow-600' : ($withdrawal->status->badgeColor() === 'info' ? 'text-blue-600' : 'text-red-600')) }}"></i>
                                            </div>
                                            <div class="flex-1 min-w-0">
                                                <p class="text-sm font-medium text-gray-900">{{ $withdrawal->amount_formatted }}</p>
                                                <p class="text-xs text-gray-500">{{ $withdrawal->created_at->format('d/m/Y H:i') }}</p>
                                            </div>
                                        </div>
                                        <span class="px-2 py-1 text-xs font-medium rounded flex-shrink-0 {{ $withdrawal->status->badgeColor() === 'success' ? 'bg-green-100 text-green-700' : ($withdrawal->status->badgeColor() === 'warning' ? 'bg-yellow-100 text-yellow-700' : ($withdrawal->status->badgeColor() === 'info' ? 'bg-blue-100 text-blue-700' : 'bg-red-100 text-red-700')) }}">
                                            {{ $withdrawal->status->label() }}
                                        </span>
                                    </div>
                                    <div class="flex flex-wrap items-center gap-3 text-xs text-gray-500 pl-11">
                                        <span class="flex items-center gap-1">
                                            <i class="fas fa-hashtag"></i>
                                            <span class="font-mono">{{ $withdrawal->slug }}</span>
                                        </span>
                                        <span class="flex items-center gap-1">
                                            <i class="fas fa-university"></i>
                                            <span>{{ $withdrawal->bank_name }}</span>
                                        </span>
                                        <span class="flex items-center gap-1">
                                            <i class="fas fa-credit-card"></i>
                                            <span class="font-mono">{{ $withdrawal->bank_account_number }}</span>
                                        </span>
                                    </div>

                                    @if($withdrawal->isPendingOtp())
                                        <div class="mt-2 p-2 bg-yellow-50 rounded-lg">
                                            <div class="flex items-center gap-2 mb-2">
                                                <i class="fas fa-key text-yellow-500 text-xs"></i>
                                                <span class="text-xs text-yellow-700">Nhập mã OTP đã gửi đến email của bạn</span>
                                            </div>
                                            <div class="flex gap-2">
                                                <input type="text" class="otp-input flex-1 px-2 py-1 text-xs border border-gray-300 rounded" 
                                                    data-withdrawal-slug="{{ $withdrawal->slug }}"
                                                    placeholder="Nhập mã OTP 6 số" maxlength="6">
                                                <button type="button" class="verify-otp-btn px-2.5 py-1 text-xs bg-green-500 text-white rounded hover:bg-green-600"
                                                    data-withdrawal-slug="{{ $withdrawal->slug }}">
                                                    Xác thực
                                                </button>
                                                <button type="button" class="resend-otp-btn px-2.5 py-1 text-xs bg-gray-500 text-white rounded hover:bg-gray-600"
                                                    data-withdrawal-slug="{{ $withdrawal->slug }}">
                                                    Gửi lại
                                                </button>
                                            </div>
                                        </div>
                                    @endif

                                    @if(in_array($withdrawal->status->value, ['pending_otp', 'pending']))
                                        <button type="button" class="cancel-withdrawal-btn mt-2 text-xs text-red-500 hover:text-red-700"
                                            data-withdrawal-slug="{{ $withdrawal->slug }}">
                                            <i class="fas fa-times"></i> Hủy yêu cầu
                                        </button>
                                    @endif

                                    @if($withdrawal->admin_note)
                                        <div class="mt-2 p-2 bg-gray-50 rounded text-xs text-gray-600">
                                            <strong>Ghi chú Admin:</strong> {{ $withdrawal->admin_note }}
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                        </div>

                        @if($withdrawals->hasPages())
                            <div class="p-3 border-t border-gray-100">
                                {{ $withdrawals->appends(request()->query())->links('components.paginate') }}
                            </div>
                        @endif
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection

@push('styles')
<style>
    .animate-fadeIn {
        animation: fadeIn 0.5s ease-in-out;
    }
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }
    .amount-btn.selected {
        border-color: #22c55e !important;
        background-color: #f0fdf4 !important;
        color: #16a34a;
        font-weight: bold;
    }
</style>
@endpush

@php
    use Illuminate\Support\Facades\Storage;
@endphp

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.amount-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            document.querySelectorAll('.amount-btn').forEach(b => b.classList.remove('selected'));
            this.classList.add('selected');
            document.getElementById('amountInput').value = this.dataset.amount;
        });
    });

    document.getElementById('withdrawalForm').addEventListener('submit', function(e) {
        e.preventDefault();

        const amount = parseInt(document.getElementById('amountInput').value);
        if (amount < 50000 || amount % 10000 !== 0) {
            Swal.fire({
                icon: 'warning',
                title: 'Số tiền không hợp lệ',
                text: 'Số tiền tối thiểu 50,000₫ và phải là bội số của 10,000₫',
                confirmButtonColor: '#22c55e'
            });
            return;
        }

        const formData = new FormData(this);
        const btn = document.getElementById('submitWithdrawal');
        btn.disabled = true;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Đang xử lý...';

        fetch('{{ route("withdrawal.store") }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: formData
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                Swal.fire({
                    icon: 'success',
                    title: 'Thành công!',
                    text: data.message,
                    confirmButtonColor: '#22c55e'
                }).then(() => window.location.reload());
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Lỗi',
                    text: data.message,
                    confirmButtonColor: '#22c55e'
                });
            }
        })
        .catch(err => {
            Swal.fire({
                icon: 'error',
                title: 'Lỗi',
                text: 'Có lỗi xảy ra, vui lòng thử lại',
                confirmButtonColor: '#22c55e'
            });
        })
        .finally(() => {
            btn.disabled = false;
            btn.innerHTML = '<i class="fas fa-paper-plane mr-2"></i>Gửi yêu cầu rút tiền';
        });
    });

    document.querySelectorAll('.verify-otp-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const slug = this.dataset.withdrawalSlug;
            const input = document.querySelector(`.otp-input[data-withdrawal-slug="${slug}"]`);
            const otp = input.value;

            if (!otp || otp.length !== 6) {
                Swal.fire({ icon: 'warning', title: 'Vui lòng nhập mã OTP 6 số' });
                return;
            }

            fetch(`/withdrawal/${slug}/verify-otp`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ otp })
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    Swal.fire({ icon: 'success', title: 'Thành công!', text: data.message })
                        .then(() => window.location.reload());
                } else {
                    Swal.fire({ icon: 'error', title: 'Lỗi', text: data.message });
                }
            });
        });
    });

    document.querySelectorAll('.resend-otp-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const slug = this.dataset.withdrawalSlug;

            fetch(`/withdrawal/${slug}/resend-otp`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                }
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    Swal.fire({ icon: 'success', title: 'Đã gửi lại OTP!', text: data.message });
                } else {
                    Swal.fire({ icon: 'error', title: 'Lỗi', text: data.message });
                }
            });
        });
    });

    document.querySelectorAll('.cancel-withdrawal-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const slug = this.dataset.withdrawalSlug;

            Swal.fire({
                title: 'Hủy yêu cầu rút tiền?',
                text: 'Tiền sẽ được hoàn lại vào ví của bạn',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'Hủy yêu cầu',
                cancelButtonText: 'Không'
            }).then((result) => {
                if (result.isConfirmed) {
                    fetch(`/withdrawal/${slug}/cancel`, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json'
                        }
                    })
                    .then(res => res.json())
                    .then(data => {
                        if (data.success) {
                            Swal.fire({ icon: 'success', title: 'Đã hủy!', text: data.message })
                                .then(() => window.location.reload());
                        } else {
                            Swal.fire({ icon: 'error', title: 'Lỗi', text: data.message });
                        }
                    });
                }
            });
        });
    });

    // Clear bank info button
    const clearBankInfoBtn = document.getElementById('clearBankInfoBtn');
    if (clearBankInfoBtn) {
        clearBankInfoBtn.addEventListener('click', function() {
            Swal.fire({
                title: 'Xóa thông tin ngân hàng?',
                text: 'Bạn có chắc chắn muốn xóa thông tin ngân hàng đã lưu?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'Xóa',
                cancelButtonText: 'Hủy'
            }).then((result) => {
                if (result.isConfirmed) {
                    fetch('{{ route("withdrawal.clear-bank-info") }}', {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json'
                        }
                    })
                    .then(res => res.json())
                    .then(data => {
                        if (data.success) {
                            Swal.fire({ 
                                icon: 'success', 
                                title: 'Thành công!', 
                                text: data.message 
                            }).then(() => window.location.reload());
                        } else {
                            Swal.fire({ icon: 'error', title: 'Lỗi', text: data.message });
                        }
                    })
                    .catch(error => {
                        Swal.fire({ 
                            icon: 'error', 
                            title: 'Lỗi', 
                            text: 'Có lỗi xảy ra, vui lòng thử lại' 
                        });
                    });
                }
            });
        });
    }
});
</script>
@endpush
