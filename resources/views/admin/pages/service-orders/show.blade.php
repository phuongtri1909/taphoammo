@extends('admin.layouts.sidebar')

@section('title', 'Chi tiết đơn hàng dịch vụ - ' . $serviceOrder->slug)

@section('main-content')
    <div class="category-container">
        <div class="mb-4">
            <a href="{{ route('admin.service-orders.index') }}" class="btn back-button">
                <i class="fas fa-arrow-left"></i> Quay lại danh sách
            </a>
        </div>

        <div class="row">
            <div class="col-lg-8">
                <div class="product-info-card mb-3">
                    <div class="card-header py-2">
                        <h3 class="card-title mb-0" style="font-size: 0.95rem; font-weight: 600;">
                            <i class="fas fa-concierge-bell"></i>
                            Thông tin đơn hàng dịch vụ
                        </h3>
                    </div>
                    <div class="card-body py-2">
                        <div class="row g-2 mb-2">
                            <div class="col-6">
                                <small class="text-muted">Mã đơn hàng:</small>
                                <p class="mb-0"><strong>#{{ $serviceOrder->slug }}</strong></p>
                            </div>
                            <div class="col-6">
                                <small class="text-muted">Trạng thái:</small>
                                <p class="mb-0">
                                    <span class="status-badge bg-{{ $serviceOrder->status->badgeColor() }} text-white">
                                        {{ $serviceOrder->status->label() }}
                                    </span>
                                </p>
                            </div>
                        </div>
                        <div class="row g-2 mb-2">
                            <div class="col-6">
                                <small class="text-muted">Người mua:</small>
                                <p class="mb-0"><strong>{{ $serviceOrder->buyer->full_name }}</strong></p>
                                <small class="text-muted">{{ $serviceOrder->buyer->email }}</small>
                            </div>
                            <div class="col-6">
                                <small class="text-muted">Người bán:</small>
                                <p class="mb-0"><strong>{{ $serviceOrder->seller->full_name }}</strong></p>
                                <small class="text-muted">{{ $serviceOrder->seller->email }}</small>
                            </div>
                        </div>
                        <div class="row g-2 mb-2">
                            <div class="col-6">
                                <small class="text-muted">Tổng tiền:</small>
                                <p class="mb-0">
                                    <strong class="text-primary" style="font-size: 1.1rem;">
                                        {{ number_format($serviceOrder->total_amount, 0, ',', '.') }}₫
                                    </strong>
                                </p>
                            </div>
                            <div class="col-6">
                                <small class="text-muted">Ngày tạo:</small>
                                <p class="mb-0">{{ $serviceOrder->created_at->format('d/m/Y H:i:s') }}</p>
                            </div>
                        </div>
                        @if($serviceOrder->seller_confirmed_at)
                            <div class="row g-2 mb-2">
                                <div class="col-6">
                                    <small class="text-muted">Seller xác nhận hoàn thành:</small>
                                    <p class="mb-0">{{ $serviceOrder->seller_confirmed_at->format('d/m/Y H:i:s') }}</p>
                                </div>
                                @if($serviceOrder->seller_reconfirmed_at)
                                    <div class="col-6">
                                        <small class="text-muted">Seller báo lại lần cuối:</small>
                                        <p class="mb-0">{{ $serviceOrder->seller_reconfirmed_at->format('d/m/Y H:i:s') }}</p>
                                    </div>
                                @endif
                            </div>
                        @endif
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
                        @if($serviceOrder->serviceVariant && $serviceOrder->serviceVariant->service)
                            <div class="p-2 bg-gray-50 rounded mb-2">
                                <div class="d-flex justify-content-between align-items-start mb-1">
                                    <div class="flex-1">
                                        <strong class="color-primary">{{ $serviceOrder->serviceVariant->service->name }}</strong>
                                        <p class="mb-1 text-muted" style="font-size: 0.85rem;">Biến thể: {{ $serviceOrder->serviceVariant->name }}</p>
                                        <p class="mb-0 text-muted" style="font-size: 0.85rem;">
                                            Giá: <strong class="text-primary">{{ number_format($serviceOrder->serviceVariant->price, 0, ',', '.') }}₫</strong>
                                        </p>
                                    </div>
                                </div>
                            </div>
                        @else
                            <p class="text-muted mb-0">Dịch vụ không tồn tại</p>
                        @endif
                    </div>
                </div>

                @if($serviceOrder->disputes->count() > 0)
                <div class="product-info-card mb-3">
                    <div class="card-header py-2">
                        <h3 class="card-title mb-0" style="font-size: 0.95rem; font-weight: 600;">
                            <i class="fas fa-exclamation-triangle"></i>
                            Lịch sử tranh chấp ({{ $serviceOrder->disputes->count() }})
                        </h3>
                    </div>
                    <div class="card-body py-2">
                        <div class="space-y-2">
                            @foreach($serviceOrder->disputes->sortByDesc('created_at') as $dispute)
                                <div class="p-2 bg-gray-50 rounded mb-2 border-start border-3 border-{{ $dispute->status->badgeColor() }}">
                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                        <span class="status-badge bg-{{ $dispute->status->badgeColor() }} text-white">
                                            {{ $dispute->status->label() }}
                                        </span>
                                        <small class="text-muted">{{ $dispute->created_at->format('d/m/Y H:i') }}</small>
                                    </div>
                                    <p class="mb-1"><strong>Lý do khiếu nại (Buyer):</strong> {{ $dispute->reason }}</p>
                                    
                                    @if($dispute->evidence && is_array($dispute->evidence) && count($dispute->evidence) > 0)
                                        <div class="mb-2">
                                            <p class="mb-1" style="font-size: 0.85rem;"><strong><i class="fas fa-link"></i> Bằng chứng URL:</strong></p>
                                            <div class="ps-2">
                                                @foreach($dispute->evidence as $url)
                                                    <a href="{{ $url }}" target="_blank" class="d-block text-primary text-truncate" style="font-size: 0.8rem;">
                                                        {{ $url }}
                                                    </a>
                                                @endforeach
                                            </div>
                                        </div>
                                    @endif
                                    
                                    @if($dispute->evidence_files && is_array($dispute->evidence_files) && count($dispute->evidence_files) > 0)
                                        <div class="mb-2">
                                            <p class="mb-1" style="font-size: 0.85rem;"><strong><i class="fas fa-paperclip"></i> Tệp đính kèm:</strong></p>
                                            <div class="d-flex flex-wrap gap-2">
                                                @foreach($dispute->evidence_files as $file)
                                                    @if(isset($file['path']))
                                                        @php
                                                            $isImage = in_array(strtolower(pathinfo($file['path'], PATHINFO_EXTENSION)), ['jpg', 'jpeg', 'png', 'webp']);
                                                        @endphp
                                                        @if($isImage)
                                                            <a href="{{ asset('storage/' . $file['path']) }}" target="_blank">
                                                                <img src="{{ asset('storage/' . $file['path']) }}" alt="{{ $file['name'] ?? 'Evidence' }}" 
                                                                    class="rounded border" style="width: 60px; height: 60px; object-fit: cover;">
                                                            </a>
                                                        @else
                                                            <a href="{{ asset('storage/' . $file['path']) }}" target="_blank" 
                                                                class="btn btn-sm btn-outline-secondary">
                                                                <i class="fas fa-file me-1"></i>
                                                                <span class="text-truncate" style="max-width: 100px; display: inline-block;">{{ $file['name'] ?? 'File' }}</span>
                                                            </a>
                                                        @endif
                                                    @endif
                                                @endforeach
                                            </div>
                                        </div>
                                    @endif
                                    
                                    @if($dispute->seller_note)
                                        <p class="mb-1 text-warning" style="font-size: 0.85rem;"><strong>Phản hồi Seller:</strong> {{ $dispute->seller_note }}</p>
                                    @endif
                                    @if($dispute->admin_note)
                                        <p class="mb-1 text-info" style="font-size: 0.85rem;"><strong>Ghi chú Admin:</strong> {{ $dispute->admin_note }}</p>
                                    @endif
                                    @if($dispute->resolved_at)
                                        <small class="text-muted">Xử lý: {{ $dispute->resolved_at->format('d/m/Y H:i') }}</small>
                                        @if($dispute->resolvedBy)
                                            <small class="text-muted">bởi {{ $dispute->resolvedBy->full_name }}</small>
                                        @endif
                                    @endif
                                    
                                    @if($dispute->status === \App\Enums\ServiceDisputeStatus::REVIEWING)
                                        <div class="mt-3 pt-2 border-top">
                                            <div class="alert alert-info py-2 mb-2" style="font-size: 0.85rem;">
                                                <i class="fas fa-info-circle"></i> Khiếu nại này cần Admin xem xét và ra quyết định
                                            </div>
                                            <form id="adminReviewForm_{{ $dispute->id }}" class="mb-2">
                                                <div class="form-group mb-2">
                                                    <label class="form-label-custom">Ghi chú của Admin</label>
                                                    <textarea name="admin_note" class="custom-input" rows="2" placeholder="Ghi chú quyết định (tùy chọn)..." maxlength="500"></textarea>
                                                </div>
                                                <div class="d-flex gap-2">
                                                    <button type="button" class="btn btn-sm btn-success flex-fill" onclick="adminAcceptDispute('{{ $dispute->slug }}', '{{ $dispute->id }}')">
                                                        <i class="fas fa-check"></i> Chấp nhận (Hoàn tiền Buyer)
                                                    </button>
                                                    <button type="button" class="btn btn-sm btn-danger flex-fill" onclick="adminRejectDispute('{{ $dispute->slug }}', '{{ $dispute->id }}')">
                                                        <i class="fas fa-times"></i> Từ chối (Hoàn thành đơn)
                                                    </button>
                                                </div>
                                            </form>
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
                @endif

                @if($serviceOrder->refunds->count() > 0)
                <div class="product-info-card mb-3">
                    <div class="card-header py-2">
                        <h3 class="card-title mb-0" style="font-size: 0.95rem; font-weight: 600;">
                            <i class="fas fa-undo"></i>
                            Hoàn tiền ({{ $serviceOrder->refunds->count() }})
                        </h3>
                    </div>
                    <div class="card-body py-2">
                        <div class="space-y-2">
                            @foreach($serviceOrder->refunds as $refund)
                                <div class="p-2 bg-gray-50 rounded mb-2">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div class="flex-1">
                                            <span class="status-badge bg-{{ $refund->status->badgeColor() }} text-white mb-1">
                                                {{ $refund->status->label() }}
                                            </span>
                                            <p class="mb-1 mt-1">
                                                <strong>Số tiền:</strong> 
                                                <span class="text-primary">{{ number_format($refund->total_amount, 0, ',', '.') }}₫</span>
                                            </p>
                                            @if($refund->reason)
                                                <p class="mb-1"><strong>Lý do:</strong> {{ $refund->reason }}</p>
                                            @endif
                                            <small class="text-muted">Ngày tạo: {{ $refund->created_at->format('d/m/Y H:i') }}</small>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
                @endif
            </div>

            <div class="col-lg-4">
                <div class="service-order-sidebar-sticky">
                    <div class="summary-card mb-3">
                        <div class="summary-header">
                            <h3>
                                <i class="fas fa-info-circle"></i>
                                Tóm tắt tài chính
                            </h3>
                        </div>
                        <div class="summary-body">
                            <div class="summary-item">
                                <span class="summary-label">Tổng tiền:</span>
                                <span class="summary-value text-primary">
                                    {{ number_format($serviceOrder->total_amount, 0, ',', '.') }}₫
                                </span>
                            </div>
                            <div class="summary-item">
                                <span class="summary-label">Trạng thái:</span>
                                <span class="summary-value">
                                    <span class="status-badge bg-{{ $serviceOrder->status->badgeColor() }} text-white">
                                        {{ $serviceOrder->status->label() }}
                                    </span>
                                </span>
                            </div>
                            
                            <div style="border-top: 1px solid #e5e7eb; padding-top: 0.75rem; margin-top: 0.75rem;">
                                @if($serviceOrder->status === \App\Enums\ServiceOrderStatus::DISPUTED)
                                    @if($expectedRefundAmount > 0)
                                        <div class="summary-item">
                                            <span class="summary-label">Dự kiến hoàn buyer:</span>
                                            <span class="summary-value text-warning">
                                                {{ number_format($expectedRefundAmount, 0, ',', '.') }}₫
                                            </span>
                                        </div>
                                    @endif
                                    <div class="summary-item">
                                        <span class="summary-label">Phí sàn (dự kiến):</span>
                                        <span class="summary-value text-warning">
                                            {{ number_format($expectedCommission, 0, ',', '.') }}₫
                                        </span>
                                    </div>
                                    <div class="summary-item">
                                        <span class="summary-label">Seller sẽ nhận (dự kiến):</span>
                                        <span class="summary-value text-info">
                                            {{ number_format($expectedSellerAmount, 0, ',', '.') }}₫
                                        </span>
                                    </div>
                                @elseif(in_array($serviceOrder->status, [\App\Enums\ServiceOrderStatus::PARTIAL_REFUNDED, \App\Enums\ServiceOrderStatus::REFUNDED]))
                                    <div class="summary-item">
                                        <span class="summary-label">Đã hoàn buyer:</span>
                                        <span class="summary-value text-danger">
                                            {{ number_format($totalRefunded, 0, ',', '.') }}₫
                                        </span>
                                    </div>
                                    <div class="summary-item">
                                        <span class="summary-label">Seller đã nhận:</span>
                                        <span class="summary-value text-success">
                                            {{ number_format($sellerEarnings, 0, ',', '.') }}₫
                                        </span>
                                    </div>
                                @elseif($serviceOrder->status === \App\Enums\ServiceOrderStatus::COMPLETED)
                                    <div class="summary-item">
                                        <span class="summary-label">Phí sàn:</span>
                                        <span class="summary-value text-warning">
                                            {{ number_format($expectedCommission, 0, ',', '.') }}₫
                                            <small class="text-muted">({{ number_format($commissionRate, 1) }}%)</small>
                                        </span>
                                    </div>
                                    <div class="summary-item">
                                        <span class="summary-label">Seller đã nhận:</span>
                                        <span class="summary-value text-success">
                                            {{ number_format($sellerEarnings, 0, ',', '.') }}₫
                                        </span>
                                    </div>
                                @else
                                    <div class="summary-item">
                                        <span class="summary-label">Phí sàn (dự kiến):</span>
                                        <span class="summary-value text-warning">
                                            {{ number_format($expectedCommission, 0, ',', '.') }}₫
                                            <small class="text-muted">({{ number_format($commissionRate, 1) }}%)</small>
                                        </span>
                                    </div>
                                    <div class="summary-item">
                                        <span class="summary-label">Seller sẽ nhận (dự kiến):</span>
                                        <span class="summary-value text-info">
                                            {{ number_format($expectedSellerAmount, 0, ',', '.') }}₫
                                        </span>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    @php
                        $reviewingDispute = $serviceOrder->disputes()->where('status', \App\Enums\ServiceDisputeStatus::REVIEWING)->first();
                    @endphp
                    @if($reviewingDispute)
                    <div class="summary-card">
                        <div class="summary-header bg-warning-subtle">
                            <h3>
                                <i class="fas fa-gavel"></i>
                                Cần xử lý
                            </h3>
                        </div>
                        <div class="summary-body">
                            <p style="font-size: 0.85rem;" class="mb-2">
                                Khiếu nại <strong>#{{ $reviewingDispute->slug }}</strong> đang chờ Admin xem xét.
                            </p>
                            <p style="font-size: 0.75rem;" class="text-muted mb-0">
                                Seller đã từ chối giải quyết. Vui lòng xem xét và ra quyết định cuối cùng.
                            </p>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    @vite('resources/assets/admin/css/product-common.css')
    @vite('resources/assets/admin/css/product-show.css')
    <style>
        /* Avoid sticky cards overlapping each other */
        .service-order-sidebar-sticky {
            position: sticky !important;
            top: 20px !important;
            align-self: flex-start;
            max-height: calc(100vh - 40px);
            overflow-y: auto;
            overflow-x: visible;
            padding-right: 8px; /* room for scrollbar */
            margin-right: -8px; /* compensates for padding to keep alignment */
        }

        .service-order-sidebar-sticky .summary-card {
            position: static !important; /* Ensure inner cards are not sticky */
            margin-bottom: 1rem;
        }

        .service-order-sidebar-sticky .summary-card:last-child {
            margin-bottom: 0;
        }

        @media (max-width: 991.98px) {
            .service-order-sidebar-sticky {
                position: static !important;
                max-height: none !important;
                overflow: visible !important;
                padding-right: 0 !important;
                margin-right: 0 !important;
            }
        }
    </style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
function adminAcceptDispute(disputeSlug, disputeId) {
    const adminNote = document.querySelector(`#adminReviewForm_${disputeId} textarea[name="admin_note"]`).value.trim();
    
    Swal.fire({
        title: 'Chấp nhận khiếu nại',
        html: `
            <div style="text-align: left; padding: 1rem 0;">
                <p style="font-size: 14px; color: #374151;">Khi bạn chấp nhận khiếu nại:</p>
                <ul style="font-size: 13px; color: #6b7280; padding-left: 1.5rem;">
                    <li>Buyer sẽ được hoàn tiền toàn bộ</li>
                    <li>Đơn hàng sẽ chuyển trạng thái REFUNDED</li>
                    <li>Seller sẽ không nhận được tiền</li>
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
            submitAdminAction(`{{ url('admin/service-disputes') }}/${disputeSlug}/accept`, adminNote);
        }
    });
}

function adminRejectDispute(disputeSlug, disputeId) {
    const adminNote = document.querySelector(`#adminReviewForm_${disputeId} textarea[name="admin_note"]`).value.trim();
    
    Swal.fire({
        title: 'Từ chối khiếu nại',
        html: `
            <div style="text-align: left; padding: 1rem 0;">
                <p style="font-size: 14px; color: #374151;">Khi bạn từ chối khiếu nại:</p>
                <ul style="font-size: 13px; color: #6b7280; padding-left: 1.5rem;">
                    <li>Đơn hàng sẽ được hoàn thành</li>
                    <li>Seller sẽ được nhận tiền (sau phí sàn)</li>
                    <li>Buyer sẽ không được hoàn tiền</li>
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
            submitAdminAction(`{{ url('admin/service-disputes') }}/${disputeSlug}/reject`, adminNote);
        }
    });
}

function submitAdminAction(url, adminNote) {
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
