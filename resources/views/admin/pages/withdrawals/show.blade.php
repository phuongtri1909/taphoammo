@extends('admin.layouts.sidebar')

@section('title', 'Chi tiết rút tiền - ' . $withdrawal->slug)

@section('main-content')
    <div class="category-container">
        <div class="mb-4">
            <a href="{{ route('admin.withdrawals.index') }}" class="btn back-button">
                <i class="fas fa-arrow-left"></i> Quay lại danh sách
            </a>
        </div>

        <div class="row">
            <div class="col-lg-8">
                <div class="product-info-card mb-3">
                    <div class="card-header py-2">
                        <h3 class="card-title mb-0" style="font-size: 0.95rem; font-weight: 600;">
                            <i class="fas fa-money-bill-wave"></i>
                            Thông tin rút tiền
                        </h3>
                    </div>
                    <div class="card-body py-3">
                        <div class="row g-3 mb-3">
                            <div class="col-6">
                                <small class="text-muted">Mã yêu cầu:</small>
                                <p class="mb-0"><strong>#{{ $withdrawal->slug }}</strong></p>
                            </div>
                            <div class="col-6">
                                <small class="text-muted">Trạng thái:</small>
                                <p class="mb-0">
                                    <span class="badge bg-{{ $withdrawal->status->badgeColor() }} text-white">
                                        {{ $withdrawal->status->label() }}
                                    </span>
                                </p>
                            </div>
                        </div>
                        <div class="row g-3 mb-3">
                            <div class="col-6">
                                <small class="text-muted">Số tiền:</small>
                                <p class="mb-0">
                                    <strong class="text-danger" style="font-size: 1.2rem;">
                                        {{ $withdrawal->amount_formatted }}
                                    </strong>
                                </p>
                            </div>
                            <div class="col-6">
                                <small class="text-muted">Ngày tạo:</small>
                                <p class="mb-0">{{ $withdrawal->created_at->format('d/m/Y H:i:s') }}</p>
                            </div>
                        </div>
                        @if($withdrawal->processed_at)
                            <div class="row g-3">
                                <div class="col-6">
                                    <small class="text-muted">Ngày xử lý:</small>
                                    <p class="mb-0">{{ $withdrawal->processed_at->format('d/m/Y H:i:s') }}</p>
                                </div>
                                @if($withdrawal->processedBy)
                                    <div class="col-6">
                                        <small class="text-muted">Xử lý bởi:</small>
                                        <p class="mb-0">{{ $withdrawal->processedBy->full_name }}</p>
                                    </div>
                                @endif
                            </div>
                        @endif
                    </div>
                </div>

                <div class="product-info-card mb-3">
                    <div class="card-header py-2">
                        <h3 class="card-title mb-0" style="font-size: 0.95rem; font-weight: 600;">
                            <i class="fas fa-user"></i>
                            Thông tin người dùng
                        </h3>
                    </div>
                    <div class="card-body py-3">
                        <div class="row g-3">
                            <div class="col-6">
                                <small class="text-muted">Họ tên:</small>
                                <p class="mb-0"><strong>{{ $withdrawal->user->full_name }}</strong></p>
                            </div>
                            <div class="col-6">
                                <small class="text-muted">Email:</small>
                                <p class="mb-0">{{ $withdrawal->user->email }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="product-info-card mb-3">
                    <div class="card-header py-2">
                        <h3 class="card-title mb-0" style="font-size: 0.95rem; font-weight: 600;">
                            <i class="fas fa-university"></i>
                            Thông tin ngân hàng
                        </h3>
                    </div>
                    <div class="card-body py-3">
                        <div class="row g-3 mb-3">
                            <div class="col-6">
                                <small class="text-muted">Ngân hàng:</small>
                                <p class="mb-0"><strong>{{ $withdrawal->bank_name }}</strong></p>
                            </div>
                            <div class="col-6">
                                <small class="text-muted">Số tài khoản:</small>
                                <p class="mb-0"><strong class="font-monospace">{{ $withdrawal->bank_account_number }}</strong></p>
                            </div>
                        </div>
                        <div class="row g-3">
                            <div class="col-12">
                                <small class="text-muted">Chủ tài khoản:</small>
                                <p class="mb-0"><strong>{{ $withdrawal->bank_account_name }}</strong></p>
                            </div>
                        </div>
                    </div>
                </div>

                @if($withdrawal->note)
                    <div class="product-info-card mb-3">
                        <div class="card-header py-2">
                            <h3 class="card-title mb-0" style="font-size: 0.95rem; font-weight: 600;">
                                <i class="fas fa-sticky-note"></i>
                                Ghi chú từ người dùng
                            </h3>
                        </div>
                        <div class="card-body py-3">
                            <p class="mb-0">{{ $withdrawal->note }}</p>
                        </div>
                    </div>
                @endif

                @if($withdrawal->admin_note)
                    <div class="product-info-card mb-3">
                        <div class="card-header py-2">
                            <h3 class="card-title mb-0" style="font-size: 0.95rem; font-weight: 600;">
                                <i class="fas fa-comment-alt"></i>
                                Ghi chú Admin
                            </h3>
                        </div>
                        <div class="card-body py-3">
                            <p class="mb-0">{{ $withdrawal->admin_note }}</p>
                        </div>
                    </div>
                @endif
            </div>

            <div class="col-lg-4">
                <div class="summary-card" style="position: sticky; top: 20px;">
                    <div class="summary-header">
                        <h3>
                            <i class="fas fa-cog"></i>
                            Xử lý yêu cầu
                        </h3>
                    </div>
                    <div class="summary-body">
                        @if($withdrawal->status === \App\Enums\WithdrawalStatus::PENDING)
                            <button type="button" class="btn-modern primary w-100 mb-3" onclick="markProcessing()">
                                <i class="fas fa-spinner"></i> Đánh dấu đang xử lý
                            </button>
                        @endif

                        @if(in_array($withdrawal->status->value, ['pending', 'processing']))
                            <button type="button" class="btn-modern success w-100 mb-3" onclick="completeWithdrawal()">
                                <i class="fas fa-check"></i> Hoàn thành (Đã chuyển tiền)
                            </button>

                            <hr class="my-4">

                            <form id="rejectForm">
                                <div class="form-group mb-3">
                                    <label class="form-label-custom">Lý do từ chối <span class="required-mark">*</span></label>
                                    <textarea name="admin_note" class="custom-input" rows="3" required 
                                        placeholder="Nhập lý do từ chối..."></textarea>
                                </div>
                                <button type="submit" class="btn-modern danger w-100">
                                    <i class="fas fa-times"></i> Từ chối (Hoàn tiền về ví)
                                </button>
                            </form>
                        @else
                            <div class="text-center py-3">
                                <p class="text-muted mb-2">Yêu cầu đã được xử lý</p>
                                <span class="badge bg-{{ $withdrawal->status->badgeColor() }} text-white">
                                    {{ $withdrawal->status->label() }}
                                </span>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    @vite('resources/assets/admin/css/product-common.css')
    @vite('resources/assets/admin/css/product-show.css')
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    function markProcessing() {
        Swal.fire({
            title: 'Đánh dấu đang xử lý?',
            text: 'Yêu cầu sẽ chuyển sang trạng thái đang xử lý',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#3b82f6',
            cancelButtonColor: '#6b7280',
            confirmButtonText: 'Xác nhận',
            cancelButtonText: 'Hủy'
        }).then((result) => {
            if (result.isConfirmed) {
                fetch('{{ route("admin.withdrawals.process", $withdrawal->slug) }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    }
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        Swal.fire('Thành công!', data.message, 'success')
                            .then(() => window.location.reload());
                    } else {
                        Swal.fire('Lỗi', data.message, 'error');
                    }
                });
            }
        });
    }

    function completeWithdrawal() {
        Swal.fire({
            title: 'Hoàn thành rút tiền?',
            html: `
                <p>Xác nhận đã chuyển tiền cho người dùng?</p>
                <input id="swal-admin-note" class="swal2-input" placeholder="Ghi chú (tùy chọn)">
            `,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#10b981',
            cancelButtonColor: '#6b7280',
            confirmButtonText: 'Hoàn thành',
            cancelButtonText: 'Hủy'
        }).then((result) => {
            if (result.isConfirmed) {
                const adminNote = document.getElementById('swal-admin-note').value;
                
                fetch('{{ route("admin.withdrawals.complete", $withdrawal->slug) }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ admin_note: adminNote })
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        Swal.fire('Thành công!', data.message, 'success')
                            .then(() => window.location.reload());
                    } else {
                        Swal.fire('Lỗi', data.message, 'error');
                    }
                });
            }
        });
    }

    document.getElementById('rejectForm')?.addEventListener('submit', function(e) {
        e.preventDefault();
        
        const adminNote = this.querySelector('textarea[name="admin_note"]').value.trim();
        if (!adminNote) {
            Swal.fire('Lỗi', 'Vui lòng nhập lý do từ chối', 'warning');
            return;
        }

        Swal.fire({
            title: 'Từ chối yêu cầu?',
            html: `<p>Tiền sẽ được hoàn lại vào ví người dùng</p>
                   <div class="text-start p-3 bg-light rounded mt-2">
                       <strong>Lý do:</strong> ${adminNote}
                   </div>`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            cancelButtonColor: '#6b7280',
            confirmButtonText: 'Từ chối',
            cancelButtonText: 'Hủy'
        }).then((result) => {
            if (result.isConfirmed) {
                fetch('{{ route("admin.withdrawals.reject", $withdrawal->slug) }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ admin_note: adminNote })
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        Swal.fire('Thành công!', data.message, 'success')
                            .then(() => window.location.reload());
                    } else {
                        Swal.fire('Lỗi', data.message, 'error');
                    }
                });
            }
        });
    });
</script>
@endpush
