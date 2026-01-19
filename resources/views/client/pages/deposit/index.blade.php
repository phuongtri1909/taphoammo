@extends('client.layouts.app')

@section('title', 'Nạp tiền - ' . config('app.name'))

@section('content')
    <div class="w-full bg-gradient-to-br from-gray-50 via-white to-gray-50 min-h-screen py-4 md:py-6">
        <div class="w-full max-w-4xl mx-auto px-3 sm:px-4 md:px-6 lg:px-8">
            <div class="space-y-4 lg:space-y-5">
                <div class="bg-white rounded-lg shadow-md border border-gray-100 overflow-hidden transform transition-all duration-300 hover:shadow-lg animate-fadeIn">
                    <div class="p-4">
                        <div class="flex items-center justify-between mb-3">
                            <h2 class="text-lg font-bold text-gray-900 flex items-center gap-2">
                                <i class="fas fa-coins text-yellow-500 text-base"></i>
                                Nạp tiền vào ví
                            </h2>
                            <a href="{{ route('profile.transactions') }}" class="text-xs text-primary hover:underline">
                                <i class="fas fa-history"></i> Lịch sử giao dịch
                            </a>
                        </div>

                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Chọn số tiền nạp</label>
                            <div class="grid grid-cols-3 md:grid-cols-6 gap-2 mb-3">
                                @foreach([10000, 20000, 30000, 50000, 100000, 200000] as $amount)
                                    <button type="button" 
                                        class="amount-btn px-3 py-2 text-sm font-medium border-2 rounded-lg transition-all duration-200 hover:border-primary hover:bg-primary/5"
                                        data-amount="{{ $amount }}">
                                        {{ number_format($amount / 1000) }}K
                                    </button>
                                @endforeach
                            </div>
                            <div class="flex items-center gap-2">
                                <input type="number" id="customAmount" 
                                    class="flex-1 px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary"
                                    placeholder="Số tiền khác (bội số của 10,000₫)" 
                                    min="10000" step="10000">
                                <span class="text-gray-500 text-sm">₫</span>
                            </div>
                            <p class="text-xs text-gray-500 mt-1">Tối thiểu: 10,000₫ | Phải là bội số của 10,000₫</p>
                        </div>

                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Chọn ngân hàng</label>
                            <div class="grid grid-cols-2 md:grid-cols-3 gap-2">
                                @foreach($banks as $bank)
                                    <button type="button" 
                                        class="bank-btn flex flex-col items-center p-3 border-2 rounded-lg transition-all duration-200 hover:border-primary hover:bg-primary/5"
                                        data-bank-id="{{ $bank->id }}"
                                        data-bank-name="{{ $bank->name }}"
                                        data-bank-code="{{ $bank->code }}">
                                        <span class="text-sm font-bold text-gray-900">{{ $bank->code }}</span>
                                        <span class="text-xs text-gray-500 truncate w-full text-center">{{ $bank->name }}</span>
                                    </button>
                                @endforeach
                            </div>
                            @if($banks->isEmpty())
                                <p class="text-center text-gray-500 py-4">Chưa có ngân hàng nào được cấu hình</p>
                            @endif
                        </div>

                        <button type="button" id="submitDeposit" 
                            class="w-full py-3 bg-gradient-to-r from-primary to-primary-6 text-white font-bold rounded-lg shadow-lg hover:shadow-xl transition-all duration-300 transform hover:-translate-y-0.5 disabled:opacity-50 disabled:cursor-not-allowed disabled:transform-none"
                            disabled>
                            <i class="fas fa-credit-card mr-2"></i>
                            Thanh toán ngay
                        </button>
                    </div>
                </div>

                @if($deposits->count() > 0)
                    <div class="bg-white rounded-lg shadow-md border border-gray-100 overflow-hidden animate-fadeIn" style="animation-delay: 0.1s">
                        <div class="p-3 border-b border-gray-100">
                            <h3 class="text-sm font-bold text-gray-900">Giao dịch nạp tiền gần đây</h3>
                        </div>
                        <div class="divide-y divide-gray-100">
                            @foreach($deposits as $deposit)
                                <div class="p-3">
                                    <div class="flex items-center justify-between mb-1.5">
                                        <div class="flex items-center gap-3">
                                            <div class="w-8 h-8 {{ $deposit->status->badgeColor() === 'success' ? 'bg-green-100' : ($deposit->status->badgeColor() === 'warning' ? 'bg-yellow-100' : 'bg-red-100') }} rounded-full flex items-center justify-center flex-shrink-0">
                                                <i class="fas {{ $deposit->isSuccessful() ? 'fa-check' : ($deposit->isPending() ? 'fa-clock' : 'fa-times') }} text-xs {{ $deposit->status->badgeColor() === 'success' ? 'text-green-600' : ($deposit->status->badgeColor() === 'warning' ? 'text-yellow-600' : 'text-red-600') }}"></i>
                                            </div>
                                            <div class="flex-1 min-w-0">
                                                <p class="text-sm font-medium text-gray-900">{{ $deposit->amount_formatted }}</p>
                                                <p class="text-xs text-gray-500">{{ $deposit->created_at->format('d/m/Y H:i') }}</p>
                                            </div>
                                        </div>
                                        <span class="px-2 py-1 text-xs font-medium rounded flex-shrink-0 {{ $deposit->status->badgeColor() === 'success' ? 'bg-green-100 text-green-700' : ($deposit->status->badgeColor() === 'warning' ? 'bg-yellow-100 text-yellow-700' : 'bg-red-100 text-red-700') }}">
                                            {{ $deposit->status->label() }}
                                        </span>
                                    </div>
                                    <div class="flex flex-wrap items-center gap-3 text-xs text-gray-500 pl-11">
                                        <span class="flex items-center gap-1">
                                            <i class="fas fa-hashtag"></i>
                                            <span class="font-mono">{{ $deposit->transaction_code }}</span>
                                        </span>
                                        @if($deposit->bank_code)
                                            <span class="flex items-center gap-1">
                                                <i class="fas fa-university"></i>
                                                <span>{{ $deposit->bank_code }}</span>
                                            </span>
                                        @endif
                                        @if($deposit->bank_account_number)
                                            <span class="flex items-center gap-1">
                                                <i class="fas fa-credit-card"></i>
                                                <span class="font-mono">{{ $deposit->bank_account_number }}</span>
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <div id="paymentModal" class="fixed inset-0 z-50 hidden overflow-y-auto" style="display: none;">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 transition-opacity backdrop-blur" style="background-color: rgba(0, 0, 0, 0.4);" onclick="closePaymentModal()"></div>
            <div class="relative inline-block w-full max-w-md p-6 my-8 overflow-hidden text-left align-middle transition-all transform bg-white shadow-xl rounded-2xl z-10">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-bold text-gray-900">
                        <i class="fas fa-qrcode text-primary mr-2"></i>
                        Thanh toán chuyển khoản
                    </h3>
                    <button onclick="closePaymentModal()" class="text-gray-400 hover:text-gray-600 transition-colors">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>

                <div id="paymentContent">
                </div>

                <div class="mt-4 pt-4 border-t border-gray-100">
                    <div class="flex items-center gap-2 text-xs text-gray-500">
                        <i class="fas fa-info-circle"></i>
                        <span>Giao dịch sẽ được xác nhận tự động sau khi chuyển khoản thành công</span>
                    </div>
                </div>
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

    .amount-btn.selected, .bank-btn.selected {
        border-color: var(--color-primary) !important;
        background-color: rgba(var(--color-primary-rgb), 0.1) !important;
    }

    .amount-btn.selected {
        color: var(--color-primary);
        font-weight: bold;
    }

    #paymentModal {
        z-index: 9999;
    }

    #paymentModal > div > div:first-child {
        backdrop-filter: blur(4px);
        -webkit-backdrop-filter: blur(4px);
        background-color: rgba(0, 0, 0, 0.4) !important;
    }

    #paymentModal.hidden {
        display: none !important;
    }

    #paymentModal:not(.hidden) {
        display: flex !important;
        align-items: center;
        justify-content: center;
    }
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    let selectedAmount = 0;
    let selectedBankId = null;

    const firstAmountBtn = document.querySelector('.amount-btn[data-amount="10000"]');
    if (firstAmountBtn) {
        firstAmountBtn.classList.add('selected');
        selectedAmount = 10000;
    }

    const firstBankBtn = document.querySelector('.bank-btn');
    if (firstBankBtn) {
        firstBankBtn.classList.add('selected');
        selectedBankId = parseInt(firstBankBtn.dataset.bankId);
    }

    updateSubmitButton();

    document.querySelectorAll('.amount-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            document.querySelectorAll('.amount-btn').forEach(b => b.classList.remove('selected'));
            this.classList.add('selected');
            selectedAmount = parseInt(this.dataset.amount);
            document.getElementById('customAmount').value = '';
            updateSubmitButton();
        });
    });

    document.getElementById('customAmount').addEventListener('input', function() {
        document.querySelectorAll('.amount-btn').forEach(b => b.classList.remove('selected'));
        const val = parseInt(this.value) || 0;
        if (val >= 10000 && val % 10000 === 0) {
            selectedAmount = val;
        } else {
            selectedAmount = 0;
        }
        updateSubmitButton();
    });

    document.querySelectorAll('.bank-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            document.querySelectorAll('.bank-btn').forEach(b => b.classList.remove('selected'));
            this.classList.add('selected');
            selectedBankId = parseInt(this.dataset.bankId);
            updateSubmitButton();
        });
    });

    function updateSubmitButton() {
        const btn = document.getElementById('submitDeposit');
        btn.disabled = !(selectedAmount >= 10000 && selectedBankId);
    }

    document.getElementById('submitDeposit').addEventListener('click', function() {
        if (!selectedAmount || !selectedBankId) return;

        const customAmount = document.getElementById('customAmount').value;
        if (customAmount && customAmount % 10000 !== 0) {
            Swal.fire({
                icon: 'warning',
                title: 'Số tiền không hợp lệ',
                text: 'Số tiền phải là bội số của 10,000₫',
                confirmButtonColor: '#3b82f6'
            });
            return;
        }

        this.disabled = true;
        this.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Đang xử lý...';

        fetch('{{ route("deposit.store") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                amount: selectedAmount,
                bank_id: selectedBankId
            })
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                showPaymentModal(data);
                startSSE(data.deposit.transaction_code);
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Lỗi',
                    text: data.message || 'Có lỗi xảy ra khi tạo giao dịch',
                    confirmButtonColor: '#3b82f6'
                });
            }
        })
        .catch(err => {
            console.error(err);
            Swal.fire({
                icon: 'error',
                title: 'Lỗi',
                text: 'Có lỗi xảy ra, vui lòng thử lại',
                confirmButtonColor: '#3b82f6'
            });
        })
        .finally(() => {
            this.disabled = false;
            this.innerHTML = '<i class="fas fa-credit-card mr-2"></i>Thanh toán ngay';
            updateSubmitButton();
        });
    });

    function showPaymentModal(data) {
        if (!data || !data.bank_info || !data.deposit) {
            console.error('Invalid data for payment modal:', data);
            Swal.fire({
                icon: 'error',
                title: 'Lỗi',
                text: 'Dữ liệu không hợp lệ',
                confirmButtonColor: '#3b82f6'
            });
            return;
        }

        const bankInfo = data.bank_info;
        const deposit = data.deposit;

        let html = `
            <div class="text-center mb-4">
                <p class="text-sm text-gray-600 mb-2">Số tiền cần chuyển</p>
                <p class="text-2xl font-bold text-primary">${deposit.amount_formatted}</p>
            </div>

            ${bankInfo.qr_code ? `
                <div class="flex justify-center mb-4">
                    <img src="${bankInfo.qr_code}" alt="QR Code" class="w-60 h-60 rounded-lg shadow-md">
                </div>
            ` : ''}

            <div class="bg-gray-50 rounded-lg p-4 space-y-3">
                <div class="flex justify-between items-center">
                    <span class="text-sm text-gray-600">Ngân hàng</span>
                    <div class="flex items-center gap-2">
                        <span class="text-sm font-bold text-gray-900">${bankInfo.name}</span>
                        <button type="button" class="copy-btn text-primary hover:text-primary-6 transition-colors" data-text="${bankInfo.name.replace(/'/g, "\\'")}">
                            <i class="fas fa-copy text-sm"></i>
                        </button>
                    </div>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-sm text-gray-600">Số tài khoản</span>
                    <div class="flex items-center gap-2">
                        <span class="text-sm font-mono font-bold text-gray-900">${bankInfo.account_number}</span>
                        <button type="button" class="copy-btn text-primary hover:text-primary-6 transition-colors" data-text="${bankInfo.account_number.replace(/'/g, "\\'")}">
                            <i class="fas fa-copy text-sm"></i>
                        </button>
                    </div>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-sm text-gray-600">Chủ tài khoản</span>
                    <div class="flex items-center gap-2">
                        <span class="text-sm font-bold text-gray-900">${bankInfo.account_name}</span>
                        <button type="button" class="copy-btn text-primary hover:text-primary-6 transition-colors" data-text="${bankInfo.account_name.replace(/'/g, "\\'")}">
                            <i class="fas fa-copy text-sm"></i>
                        </button>
                    </div>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-sm text-gray-600">Nội dung CK</span>
                    <div class="flex items-center gap-2">
                        <span class="text-sm font-mono font-bold text-primary">${deposit.transaction_code}</span>
                        <button type="button" class="copy-btn text-primary hover:text-primary-6 transition-colors" data-text="${deposit.transaction_code.replace(/'/g, "\\'")}">
                            <i class="fas fa-copy text-sm"></i>
                        </button>
                    </div>
                </div>
            </div>

            <div class="mt-4 p-3 bg-yellow-50 border border-yellow-200 rounded-lg">
                <div class="flex items-start gap-2">
                    <i class="fas fa-exclamation-triangle text-yellow-500 mt-0.5"></i>
                    <div class="text-xs text-yellow-700">
                        <p class="font-bold mb-1">Lưu ý quan trọng:</p>
                        <ul class="list-disc list-inside space-y-0.5">
                            <li>Nhập <strong>đúng nội dung chuyển khoản</strong></li>
                            <li>Chuyển khoản <strong>đúng số tiền</strong></li>
                            <li>Tiền sẽ được cộng tự động sau 1-3 phút</li>
                        </ul>
                    </div>
                </div>
            </div>

            <div id="sseStatus" class="mt-4 text-center">
                <div class="flex items-center justify-center gap-2 text-sm text-gray-500">
                    <i class="fas fa-spinner fa-spin"></i>
                    <span>Đang chờ thanh toán...</span>
                </div>
            </div>
        `;

        document.getElementById('paymentContent').innerHTML = html;
        const modal = document.getElementById('paymentModal');
        modal.classList.remove('hidden');
        modal.style.display = 'flex';

        document.querySelectorAll('#paymentModal .copy-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                const textToCopy = this.getAttribute('data-text');
                copyToClipboard(textToCopy);
            });
        });
    }

    window.closePaymentModal = function() {
        const modal = document.getElementById('paymentModal');
        modal.classList.add('hidden');
        modal.style.display = 'none';
        if (window.sseSource) {
            window.sseSource.close();
        }
    };

    function copyToClipboard(text) {
        if (navigator.clipboard && navigator.clipboard.writeText) {
            navigator.clipboard.writeText(text).then(() => {
                showCopySuccess();
            }).catch(() => {
                fallbackCopyToClipboard(text);
            });
        } else {
            fallbackCopyToClipboard(text);
        }
    }

    function fallbackCopyToClipboard(text) {
        const textArea = document.createElement('textarea');
        textArea.value = text;
        textArea.style.position = 'fixed';
        textArea.style.left = '-999999px';
        textArea.style.top = '-999999px';
        document.body.appendChild(textArea);
        textArea.focus();
        textArea.select();
        
        try {
            const successful = document.execCommand('copy');
            if (successful) {
                showCopySuccess();
            } else {
                showCopyError();
            }
        } catch (err) {
            console.error('Fallback copy failed:', err);
            showCopyError();
        }
        
        document.body.removeChild(textArea);
    }

    function showCopySuccess() {
        Swal.fire({
            toast: true,
            position: 'top-end',
            icon: 'success',
            title: 'Đã sao chép!',
            showConfirmButton: false,
            timer: 1500,
            timerProgressBar: true
        });
    }

    function showCopyError() {
        Swal.fire({
            toast: true,
            position: 'top-end',
            icon: 'error',
            title: 'Không thể sao chép',
            showConfirmButton: false,
            timer: 2000
        });
    }

    function startSSE(transactionCode) {
        if (window.sseSource) {
            window.sseSource.close();
        }

        window.sseSource = new EventSource(`{{ route('deposit.sse') }}?transaction_code=${transactionCode}`);

        window.sseSource.onmessage = function(event) {
            const data = JSON.parse(event.data);
            
            if (data.type === 'close' || data.type === 'timeout') {
                window.sseSource.close();
                return;
            }

            if (data.status === 'success') {
                document.getElementById('sseStatus').innerHTML = `
                    <div class="flex items-center justify-center gap-2 text-sm text-green-600">
                        <i class="fas fa-check-circle"></i>
                        <span>Nạp tiền thành công!</span>
                    </div>
                `;

                Swal.fire({
                    icon: 'success',
                    title: 'Nạp tiền thành công!',
                    text: 'Số tiền đã được cộng vào ví của bạn',
                    confirmButtonColor: '#10b981'
                }).then(() => {
                    window.location.reload();
                });

                window.sseSource.close();
            }
        };

        window.sseSource.onerror = function() {
            console.log('SSE connection error, falling back to polling');
            window.sseSource.close();
            startPolling(transactionCode);
        };
    }

    function startPolling(transactionCode) {
        const poll = setInterval(() => {
            fetch(`{{ route('deposit.check-status') }}?transaction_code=${transactionCode}`)
                .then(res => res.json())
                .then(data => {
                    if (data.is_successful) {
                        clearInterval(poll);
                        Swal.fire({
                            icon: 'success',
                            title: 'Nạp tiền thành công!',
                            text: 'Số tiền đã được cộng vào ví của bạn',
                            confirmButtonColor: '#10b981'
                        }).then(() => {
                            window.location.reload();
                        });
                    }
                });
        }, 5000);

        setTimeout(() => clearInterval(poll), 300000);
    }
});
</script>
@endpush
