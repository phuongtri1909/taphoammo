@extends('admin.layouts.sidebar')

@section('title', 'Chi tiết tranh chấp dịch vụ - ' . $dispute->slug)

@section('main-content')
    <div class="category-container">
        <div class="mb-4">
            <a href="{{ route('admin.service-disputes.index') }}" class="btn back-button">
                <i class="fas fa-arrow-left"></i> Quay lại danh sách
            </a>
        </div>

        <div class="row">
            <div class="col-lg-8">
                <div class="product-info-card mb-3">
                    <div class="card-header py-2">
                        <h3 class="card-title mb-0" style="font-size: 0.95rem; font-weight: 600;">
                            <i class="fas fa-exclamation-triangle"></i>
                            Thông tin tranh chấp
                        </h3>
                    </div>
                    <div class="card-body py-2">
                        <div class="row g-2 mb-2">
                            <div class="col-6">
                                <small class="text-muted">Mã tranh chấp:</small>
                                <p class="mb-0"><strong>#{{ $dispute->slug }}</strong></p>
                            </div>
                            <div class="col-6">
                                <small class="text-muted">Trạng thái:</small>
                                <p class="mb-0">
                                    <span class="status-badge bg-{{ $dispute->status->badgeColor() }} text-white">
                                        {{ $dispute->status->label() }}
                                    </span>
                                </p>
                            </div>
                        </div>
                        <div class="row g-2 mb-2">
                            <div class="col-12">
                                <small class="text-muted">Đơn hàng dịch vụ:</small>
                                <p class="mb-0">
                                    <a class="color-primary" href="{{ route('admin.service-orders.show', $dispute->serviceOrder->slug) }}">
                                        <strong>#{{ $dispute->serviceOrder->slug }}</strong>
                                    </a>
                                    - {{ number_format($dispute->serviceOrder->total_amount, 0, ',', '.') }}₫
                                </p>
                            </div>
                        </div>
                        <div class="row g-2 mb-2">
                            <div class="col-6">
                                <small class="text-muted">Người mua:</small>
                                <p class="mb-0"><strong>{{ $dispute->serviceOrder->buyer->full_name }}</strong></p>
                                <small class="text-muted">{{ $dispute->serviceOrder->buyer->email }}</small>
                            </div>
                            <div class="col-6">
                                <small class="text-muted">Người bán:</small>
                                <p class="mb-0"><strong>{{ $dispute->serviceOrder->seller->full_name }}</strong></p>
                                <small class="text-muted">{{ $dispute->serviceOrder->seller->email }}</small>
                            </div>
                        </div>
                        <div class="row g-2">
                            <div class="col-12">
                                <small class="text-muted">Ngày tạo:</small>
                                <p class="mb-0">{{ $dispute->created_at->format('d/m/Y H:i:s') }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="product-info-card mb-3">
                    <div class="card-header py-2">
                        <h3 class="card-title mb-0" style="font-size: 0.95rem; font-weight: 600;">
                            <i class="fas fa-concierge-bell"></i>
                            Dịch vụ
                        </h3>
                    </div>
                    <div class="card-body py-2">
                        @if($dispute->serviceOrder->serviceVariant && $dispute->serviceOrder->serviceVariant->service)
                            <div class="p-2 bg-gray-50 rounded">
                                <strong class="color-primary">{{ $dispute->serviceOrder->serviceVariant->service->name }}</strong>
                                <p class="mb-1 text-muted" style="font-size: 0.85rem;">Biến thể: {{ $dispute->serviceOrder->serviceVariant->name }}</p>
                                <p class="mb-0 text-muted" style="font-size: 0.85rem;">
                                    Giá: <strong class="text-primary">{{ number_format($dispute->serviceOrder->serviceVariant->price, 0, ',', '.') }}₫</strong>
                                </p>
                            </div>
                        @else
                            <p class="text-muted mb-0">Dịch vụ không tồn tại</p>
                        @endif
                    </div>
                </div>

                <div class="product-info-card mb-3">
                    <div class="card-header py-2">
                        <h3 class="card-title mb-0" style="font-size: 0.95rem; font-weight: 600;">
                            <i class="fas fa-comment-alt"></i>
                            Lý do khiếu nại (từ người mua)
                        </h3>
                    </div>
                    <div class="card-body py-2">
                        <p class="mb-0" style="font-size: 0.9rem;">{{ $dispute->reason }}</p>
                    </div>
                </div>

                @if($dispute->seller_note)
                    <div class="product-info-card mb-3">
                        <div class="card-header py-2 bg-warning-subtle">
                            <h3 class="card-title mb-0" style="font-size: 0.95rem; font-weight: 600;">
                                <i class="fas fa-reply"></i>
                                Phản hồi của Seller
                            </h3>
                        </div>
                        <div class="card-body py-2">
                            <p class="mb-0" style="font-size: 0.9rem;">{{ $dispute->seller_note }}</p>
                        </div>
                    </div>
                @endif

                @if($dispute->admin_note)
                    <div class="product-info-card mb-3">
                        <div class="card-header py-2 bg-info-subtle">
                            <h3 class="card-title mb-0" style="font-size: 0.95rem; font-weight: 600;">
                                <i class="fas fa-user-shield"></i>
                                Ghi chú từ Admin
                            </h3>
                        </div>
                        <div class="card-body py-2">
                            <p class="mb-0" style="font-size: 0.9rem;">{{ $dispute->admin_note }}</p>
                        </div>
                    </div>
                @endif

                @if($dispute->resolved_at)
                    <div class="product-info-card mb-3">
                        <div class="card-header py-2 bg-success-subtle">
                            <h3 class="card-title mb-0" style="font-size: 0.95rem; font-weight: 600;">
                                <i class="fas fa-check-circle"></i>
                                Thông tin xử lý
                            </h3>
                        </div>
                        <div class="card-body py-2">
                            <p class="mb-1" style="font-size: 0.9rem;">
                                <strong>Ngày xử lý:</strong> {{ $dispute->resolved_at->format('d/m/Y H:i:s') }}
                            </p>
                            @if($dispute->resolvedBy)
                                <p class="mb-0" style="font-size: 0.9rem;">
                                    <strong>Xử lý bởi:</strong> {{ $dispute->resolvedBy->full_name }}
                                </p>
                            @endif
                        </div>
                    </div>
                @endif
            </div>

            <div class="col-lg-4">
                <div class="summary-card" style="position: sticky; top: 20px;">
                    <div class="summary-header">
                        <h3>
                            <i class="fas fa-cog"></i>
                            Xử lý tranh chấp
                        </h3>
                    </div>
                    <div class="summary-body">
                        @if($dispute->status === \App\Enums\ServiceDisputeStatus::REVIEWING)
                            @php
                                $commissionRate = (float) \App\Models\Config::getConfig('commission_rate', 10);
                                $refundAmount = $dispute->serviceOrder->total_amount;
                                $sellerAmount = $dispute->serviceOrder->total_amount * (1 - $commissionRate / 100);
                            @endphp
                            
                            <div class="alert alert-warning mb-3" style="font-size: 0.85rem;">
                                <strong><i class="fas fa-gavel"></i> Cần Admin quyết định:</strong>
                                <p class="mb-0 mt-1">Seller đã từ chối khiếu nại. Vui lòng xem xét và ra quyết định cuối cùng.</p>
                            </div>
                            
                            <div class="alert alert-info mb-3" style="font-size: 0.85rem;">
                                <strong>Nếu chấp nhận (hoàn tiền):</strong>
                                <ul class="mb-0 mt-1 ps-3">
                                    <li>Buyer được hoàn: <strong class="text-success">{{ number_format($refundAmount, 0, ',', '.') }}₫</strong></li>
                                    <li>Seller không nhận được tiền</li>
                                </ul>
                            </div>
                            
                            <div class="alert alert-secondary mb-3" style="font-size: 0.85rem;">
                                <strong>Nếu từ chối (hoàn thành đơn):</strong>
                                <ul class="mb-0 mt-1 ps-3">
                                    <li>Buyer không được hoàn tiền</li>
                                    <li>Seller nhận: <strong class="text-success">{{ number_format($sellerAmount, 0, ',', '.') }}₫</strong></li>
                                </ul>
                            </div>

                            <form id="adminReviewForm">
                                <div class="form-group mb-3">
                                    <label class="form-label-custom">Ghi chú của Admin (tùy chọn)</label>
                                    <textarea name="admin_note" class="custom-input" rows="3" placeholder="Ghi chú quyết định..." maxlength="500"></textarea>
                                </div>
                                <button type="button" id="acceptBtn" class="btn-modern success w-100 mb-2">
                                    <i class="fas fa-check"></i> Chấp nhận (Hoàn tiền Buyer)
                                </button>
                                <button type="button" id="rejectBtn" class="btn-modern danger w-100">
                                    <i class="fas fa-times"></i> Từ chối (Hoàn thành đơn)
                                </button>
                            </form>
                        @elseif($dispute->status === \App\Enums\ServiceDisputeStatus::OPEN)
                            <div class="text-center py-3">
                                <div class="mb-3">
                                    <span class="badge bg-warning" style="font-size: 1rem; padding: 0.5rem 1rem;">
                                        <i class="fas fa-hourglass-half"></i> Đang chờ Seller phản hồi
                                    </span>
                                </div>
                                <p class="text-muted mb-0" style="font-size: 0.85rem;">
                                    Khiếu nại đang chờ seller xem xét. Admin chỉ can thiệp khi seller từ chối.
                                </p>
                            </div>
                        @else
                            <div class="text-center py-3">
                                <p class="text-muted mb-2">Khiếu nại đã được xử lý</p>
                                <span class="badge bg-{{ $dispute->status->badgeColor() }}" style="font-size: 0.9rem; padding: 0.5rem 1rem;">
                                    {{ $dispute->status->label() }}
                                </span>
                                @if($dispute->resolvedBy)
                                    <div class="mt-3">
                                        <small class="text-muted">
                                            Xử lý bởi: {{ $dispute->resolvedBy->full_name }}<br>
                                            {{ $dispute->resolved_at ? $dispute->resolved_at->format('d/m/Y H:i') : '' }}
                                        </small>
                                    </div>
                                @endif
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
document.addEventListener('DOMContentLoaded', function() {
    const acceptBtn = document.getElementById('acceptBtn');
    const rejectBtn = document.getElementById('rejectBtn');

    if (acceptBtn) {
        acceptBtn.addEventListener('click', function() {
            const adminNote = document.querySelector('#adminReviewForm textarea[name="admin_note"]').value.trim();
            
            Swal.fire({
                title: 'Chấp nhận khiếu nại',
                html: `
                    <div style="text-align: left; padding: 1rem 0;">
                        <p style="font-size: 14px; color: #374151;">Khi bạn chấp nhận:</p>
                        <ul style="font-size: 13px; color: #6b7280; padding-left: 1.5rem;">
                            <li>Người mua sẽ được hoàn tiền toàn bộ</li>
                            <li>Người bán không nhận được tiền</li>
                            <li>Đơn hàng chuyển trạng thái REFUNDED</li>
                        </ul>
                    </div>
                `,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#10b981',
                cancelButtonColor: '#6b7280',
                confirmButtonText: '<i class="fas fa-check"></i> Xác nhận chấp nhận',
                cancelButtonText: 'Hủy'
            }).then((result) => {
                if (result.isConfirmed) {
                    submitAction('{{ route("admin.service-disputes.accept", $dispute->slug) }}', adminNote);
                }
            });
        });
    }

    if (rejectBtn) {
        rejectBtn.addEventListener('click', function() {
            const adminNote = document.querySelector('#adminReviewForm textarea[name="admin_note"]').value.trim();
            
            Swal.fire({
                title: 'Từ chối khiếu nại',
                html: `
                    <div style="text-align: left; padding: 1rem 0;">
                        <p style="font-size: 14px; color: #374151;">Khi bạn từ chối:</p>
                        <ul style="font-size: 13px; color: #6b7280; padding-left: 1.5rem;">
                            <li>Đơn hàng sẽ được hoàn thành</li>
                            <li>Người bán sẽ được nhận tiền (sau phí sàn)</li>
                            <li>Người mua không được hoàn tiền</li>
                        </ul>
                    </div>
                `,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#6b7280',
                confirmButtonText: '<i class="fas fa-times"></i> Xác nhận từ chối',
                cancelButtonText: 'Hủy'
            }).then((result) => {
                if (result.isConfirmed) {
                    submitAction('{{ route("admin.service-disputes.reject", $dispute->slug) }}', adminNote);
                }
            });
        });
    }
});

function submitAction(url, adminNote) {
    Swal.fire({
        title: 'Đang xử lý...',
        allowOutsideClick: false,
        allowEscapeKey: false,
        didOpen: () => { Swal.showLoading(); }
    });

    fetch(url, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
        },
        body: JSON.stringify({ admin_note: adminNote })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            Swal.fire({
                title: 'Thành công!',
                text: data.message,
                icon: 'success',
                confirmButtonColor: '#10b981'
            }).then(() => window.location.reload());
        } else {
            Swal.fire({
                title: 'Lỗi',
                text: data.message || 'Có lỗi xảy ra.',
                icon: 'error',
                confirmButtonColor: '#ef4444'
            });
        }
    })
    .catch(error => {
        console.error('Error:', error);
        Swal.fire({
            title: 'Lỗi',
            text: 'Có lỗi xảy ra.',
            icon: 'error',
            confirmButtonColor: '#ef4444'
        });
    });
}
</script>
@endpush
