@extends('seller.layouts.sidebar')

@section('title', 'Chi tiết đơn hàng dịch vụ - ' . $serviceOrder->slug)

@section('main-content')
    <div class="category-container">
        <div class="mb-4">
            <a href="{{ route('seller.service-orders.index') }}" class="btn back-button">
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
                            <div class="col-12">
                                <small class="text-muted">Người mua:</small>
                                <p class="mb-0"><strong>{{ $serviceOrder->buyer->full_name }}</strong></p>
                                <small class="text-muted">{{ $serviceOrder->buyer->email }}</small>
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
                                    <small class="text-muted">Xác nhận hoàn thành:</small>
                                    <p class="mb-0">{{ $serviceOrder->seller_confirmed_at->format('d/m/Y H:i:s') }}</p>
                                </div>
                                @if($serviceOrder->seller_reconfirmed_at)
                                    <div class="col-6">
                                        <small class="text-muted">Báo lại lần cuối:</small>
                                        <p class="mb-0">{{ $serviceOrder->seller_reconfirmed_at->format('d/m/Y H:i:s') }}</p>
                                    </div>
                                @endif
                            </div>
                        @endif
                        @if($serviceOrder->note)
                            <div class="row g-2 mb-2">
                                <div class="col-12">
                                    <small class="text-muted">Ghi chú từ người mua:</small>
                                    <div class="p-2 bg-gray-50 rounded border border-gray-200 mt-1">
                                        <p class="mb-0 text-sm" style="white-space: pre-wrap;">{{ $serviceOrder->note }}</p>
                                    </div>
                                </div>
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
                                        <a class="text-primary" href="{{ route('seller.services.show', $serviceOrder->serviceVariant->service->slug) }}">
                                            <strong>{{ $serviceOrder->serviceVariant->service->name }}</strong>
                                        </a>
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
                                    <p class="mb-1"><strong>Lý do:</strong> {{ $dispute->reason }}</p>
                                    
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
                                                            <a href="javascript:void(0)"
                                                               onclick="openFileModal({{ json_encode(asset('storage/' . $file['path'])) }}, {{ json_encode($file['name'] ?? 'Image') }}, true)">
                                                                <img src="{{ asset('storage/' . $file['path']) }}" alt="{{ $file['name'] ?? 'Evidence' }}" 
                                                                    class="rounded border" style="width: 60px; height: 60px; object-fit: cover; cursor: pointer;">
                                                            </a>
                                                        @else
                                                            <a href="javascript:void(0)"
                                                               onclick="openFileModal({{ json_encode(asset('storage/' . $file['path'])) }}, {{ json_encode($file['name'] ?? 'File') }}, false)"
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
                                        <p class="mb-1 text-muted" style="font-size: 0.85rem;"><strong>Phản hồi của bạn:</strong> {{ $dispute->seller_note }}</p>
                                    @endif
                                    @if($dispute->admin_note)
                                        <p class="mb-1 text-info" style="font-size: 0.85rem;"><strong>Ghi chú Admin:</strong> {{ $dispute->admin_note }}</p>
                                    @endif
                                    @if($dispute->resolved_at)
                                        <small class="text-muted">Xử lý: {{ $dispute->resolved_at->format('d/m/Y H:i') }}</small>
                                    @endif
                                    
                                    @if($dispute->status === \App\Enums\ServiceDisputeStatus::OPEN)
                                        @php
                                            $disputeDecisionHours = (int) \App\Models\Config::getConfig('service_order_completion_hours', 96);
                                            $disputeDecisionDeadline = $dispute->created_at->copy()->addHours($disputeDecisionHours);
                                        @endphp
                                        <div class="mt-2">
                                            <small class="text-muted" style="font-size: 0.8rem;">
                                                <i class="fas fa-hourglass-half"></i>
                                                Còn lại để phản hồi khiếu nại:
                                                <strong id="disputeCountdown-{{ $dispute->slug }}"
                                                        data-dispute-deadline="{{ $disputeDecisionDeadline->toISOString() }}"></strong>
                                            </small>
                                        </div>
                                        <div class="mt-3 pt-2 border-top">
                                            <div class="d-flex gap-2">
                                                <button type="button" class="btn btn-sm btn-success" onclick="acceptDispute('{{ $dispute->slug }}')">
                                                    <i class="fas fa-check"></i> Chấp nhận giải quyết
                                                </button>
                                                <button type="button" class="btn btn-sm btn-danger" onclick="rejectDispute('{{ $dispute->slug }}')">
                                                    <i class="fas fa-times"></i> Từ chối (Chuyển Admin)
                                                </button>
                                            </div>
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
                            <i class="fas fa-cog"></i>
                            Thao tác
                        </h3>
                    </div>
                    <div class="summary-body">
                        @if($serviceOrder->status === \App\Enums\ServiceOrderStatus::PAID)
                            @if($serviceOrder->last_dispute_resolved_at)
                                <div class="alert alert-warning mb-3" style="font-size: 0.85rem;">
                                    <i class="fas fa-exclamation-circle"></i> <strong>Đơn cần xử lý lại!</strong><br>
                                    <span class="text-muted" style="font-size: 0.75rem;">
                                        Bạn đã chấp nhận giải quyết khiếu nại. Vui lòng hoàn thành lại dịch vụ theo yêu cầu của khách hàng và xác nhận khi đã xong.
                                    </span>
                                </div>
                            @endif
                            
                            @if($sellerConfirmDeadline)
                                <div class="alert alert-info mb-3" style="font-size: 0.85rem;">
                                    <i class="fas fa-clock"></i> <strong>Hạn xử lý đơn hàng:</strong><br>
                                    <span id="sellerDeadline" data-deadline="{{ $sellerConfirmDeadline->toISOString() }}">
                                        {{ $sellerConfirmDeadline->format('d/m/Y H:i') }}
                                    </span>
                                    <div class="mt-1 text-muted" style="font-size: 0.75rem;">
                                        Còn lại: <strong id="sellerCountdown"></strong>
                                    </div>
                                </div>
                            @endif
                            
                            <button type="button" id="confirmCompletionBtn" class="btn-modern success w-100 mb-2">
                                <i class="fas fa-check-circle"></i> 
                                @if($serviceOrder->last_dispute_resolved_at)
                                    Xác nhận đã hoàn thành lại
                                @else
                                    Xác nhận đã hoàn thành dịch vụ
                                @endif
                            </button>
                            <small class="text-muted d-block text-center mb-3">
                                @if($serviceOrder->last_dispute_resolved_at)
                                    Nhấn khi bạn đã hoàn thành điều chỉnh theo khiếu nại
                                @else
                                    Nhấn khi bạn đã hoàn thành dịch vụ cho khách hàng
                                @endif
                            </small>
                            
                            <hr class="my-3">
                            
                            <button type="button" id="rejectOrderBtn" class="btn-modern danger w-100 mb-2">
                                <i class="fas fa-times-circle"></i> Từ chối nhận đơn hàng
                            </button>
                            <small class="text-muted d-block text-center">Tiền sẽ được hoàn lại cho người mua</small>
                            
                        @elseif($serviceOrder->status === \App\Enums\ServiceOrderStatus::SELLER_CONFIRMED)
                            @if($buyerConfirmDeadline)
                                <div class="alert alert-success mb-3" style="font-size: 0.85rem;">
                                    <i class="fas fa-hourglass-half"></i> <strong>Đang chờ người mua xác nhận</strong><br>
                                    <span class="text-muted" style="font-size: 0.75rem;">
                                        Hạn: {{ $buyerConfirmDeadline->format('d/m/Y H:i') }}<br>
                                        Còn lại: <strong id="buyerCountdown" data-deadline="{{ $buyerConfirmDeadline->toISOString() }}"></strong>
                                    </span>
                                </div>
                            @endif
                            
                            <div class="text-center py-2">
                                <span class="badge bg-primary" style="font-size: 0.9rem; padding: 0.5rem 1rem;">
                                    <i class="fas fa-clock"></i> Đang chờ xác nhận từ người mua
                                </span>
                                @if($serviceOrder->last_dispute_resolved_at)
                                    <p class="text-muted mt-2" style="font-size: 0.75rem;">
                                        <i class="fas fa-info-circle"></i> Đơn đã được xác nhận lại sau khi giải quyết khiếu nại
                                    </p>
                                @endif
                            </div>
                            
                        @elseif($serviceOrder->status === \App\Enums\ServiceOrderStatus::DISPUTED)
                            <div class="alert alert-warning mb-3" style="font-size: 0.85rem;">
                                <i class="fas fa-exclamation-triangle"></i> <strong>Đơn hàng đang có khiếu nại</strong><br>
                                <span class="text-muted" style="font-size: 0.75rem;">
                                    Vui lòng xem xét và xử lý khiếu nại bên dưới
                                </span>
                            </div>

                            @if($sellerConfirmDeadline)
                                <div class="alert alert-warning mb-3" style="font-size: 0.85rem;">
                                    <i class="fas fa-clock"></i> <strong>Hạn phản hồi khiếu nại:</strong><br>
                                    <span id="sellerDeadline" data-deadline="{{ $sellerConfirmDeadline->toISOString() }}">
                                        {{ $sellerConfirmDeadline->format('d/m/Y H:i') }}
                                    </span>
                                    <div class="mt-1 text-muted" style="font-size: 0.75rem;">
                                        Còn lại: <strong id="sellerCountdown"></strong>
                                    </div>
                                </div>
                            @endif

                            <button type="button" id="rejectOrderBtn" class="btn-modern danger w-100 mb-2">
                                <i class="fas fa-times-circle"></i> Từ chối nhận đơn hàng
                            </button>
                            <small class="text-muted d-block text-center">Tiền sẽ được hoàn lại cho người mua</small>
                            
                        @elseif($serviceOrder->status === \App\Enums\ServiceOrderStatus::COMPLETED)
                            <div class="text-center py-3">
                                <span class="badge bg-success" style="font-size: 0.9rem; padding: 0.5rem 1rem;">
                                    <i class="fas fa-check-circle"></i> Đơn hàng đã hoàn thành
                                </span>
                            </div>
                            
                        @elseif($serviceOrder->status === \App\Enums\ServiceOrderStatus::REFUNDED)
                            <div class="text-center py-3">
                                <span class="badge bg-secondary" style="font-size: 0.9rem; padding: 0.5rem 1rem;">
                                    <i class="fas fa-undo"></i> Đơn hàng đã hoàn tiền
                                </span>
                            </div>
                        @endif
                    </div>
                </div>

                <div class="summary-card">
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
                                    <span class="summary-label">Bạn sẽ nhận (dự kiến):</span>
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
                                    <span class="summary-label">Bạn đã nhận:</span>
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
                                    <span class="summary-label">Bạn đã nhận:</span>
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
                                    <span class="summary-label">Bạn sẽ nhận (dự kiến):</span>
                                    <span class="summary-value text-info">
                                        {{ number_format($expectedSellerAmount, 0, ',', '.') }}₫
                                    </span>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
                </div>
            </div>
        </div>
    </div>

    <!-- File Preview Modal (Dispute evidence) -->
    <div id="fileModal" class="modal fade" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="fileModalTitle">
                        <i class="fas fa-file"></i> Xem file
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="fileModalBody" style="max-height: 70vh; overflow: auto;">
                </div>
                <div class="modal-footer">
                    <a id="fileModalDownload" href="#" download class="btn action-button">
                        <i class="fas fa-download"></i> Tải xuống
                    </a>
                    <button type="button" class="btn back-button" data-bs-dismiss="modal">Đóng</button>
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
            padding-right: 8px;
            margin-right: -8px;
        }

        .service-order-sidebar-sticky .summary-card {
            position: static !important;
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
document.addEventListener('DOMContentLoaded', function() {
    // Countdown functions
    function updateCountdown(elementId, deadline) {
        const element = document.getElementById(elementId);
        if (!element) return;
        
        const now = new Date().getTime();
        const deadlineTime = new Date(deadline).getTime();
        const distance = deadlineTime - now;
        
        if (distance < 0) {
            element.innerHTML = '<span class="text-danger">Đã hết hạn</span>';
            return;
        }
        
        const days = Math.floor(distance / (1000 * 60 * 60 * 24));
        const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
        const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
        const seconds = Math.floor((distance % (1000 * 60)) / 1000);
        
        let text = '';
        if (days > 0) text += days + ' ngày ';
        if (hours > 0) text += hours + ' giờ ';
        text += minutes + ' phút ' + seconds + ' giây';
        
        element.innerHTML = text;
    }
    
    // Seller deadline countdown
    const sellerDeadlineEl = document.getElementById('sellerDeadline');
    if (sellerDeadlineEl) {
        const deadline = sellerDeadlineEl.dataset.deadline;
        setInterval(() => updateCountdown('sellerCountdown', deadline), 1000);
        updateCountdown('sellerCountdown', deadline);
    }
    
    // Buyer deadline countdown
    const buyerCountdownEl = document.getElementById('buyerCountdown');
    if (buyerCountdownEl) {
        const deadline = buyerCountdownEl.dataset.deadline;
        setInterval(() => updateCountdown('buyerCountdown', deadline), 1000);
        updateCountdown('buyerCountdown', deadline);
    }

    // Dispute decision countdowns (seller accept/reject)
    const disputeCountdownEls = document.querySelectorAll('[data-dispute-deadline]');
    if (disputeCountdownEls && disputeCountdownEls.length > 0) {
        const tick = () => {
            disputeCountdownEls.forEach((el) => {
                const deadline = el.dataset.disputeDeadline;
                if (!deadline) return;
                updateCountdown(el.id, deadline);
            });
        };
        setInterval(tick, 1000);
        tick();
    }
    
    // Confirm completion button
    const confirmBtn = document.getElementById('confirmCompletionBtn');
    if (confirmBtn) {
        confirmBtn.addEventListener('click', function() {
            Swal.fire({
                title: 'Xác nhận hoàn thành dịch vụ',
                html: `
                    <div style="text-align: left; padding: 1rem 0;">
                        <p style="font-size: 14px; color: #374151;">Bạn xác nhận đã hoàn thành dịch vụ cho khách hàng?</p>
                        <ul style="font-size: 13px; color: #6b7280; padding-left: 1.5rem;">
                            <li>Người mua sẽ có thời gian để xác nhận hoặc khiếu nại</li>
                            <li>Nếu không có phản hồi, đơn sẽ tự động hoàn thành</li>
                        </ul>
                    </div>
                `,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#10b981',
                cancelButtonColor: '#6b7280',
                confirmButtonText: '<i class="fas fa-check"></i> Xác nhận hoàn thành',
                cancelButtonText: 'Hủy'
            }).then((result) => {
                if (result.isConfirmed) {
                    submitAction('{{ route("seller.service-orders.confirm-completion", $serviceOrder->slug) }}');
                }
            });
        });
    }
    
    
    // Reject order button
    const rejectOrderBtn = document.getElementById('rejectOrderBtn');
    if (rejectOrderBtn) {
        rejectOrderBtn.addEventListener('click', function() {
            Swal.fire({
                title: 'Từ chối nhận đơn hàng',
                html: `
                    <div style="text-align: left; padding: 1rem 0;">
                        <p style="font-size: 14px; color: #374151; margin-bottom: 0.5rem;"><strong>Bạn chắc chắn muốn từ chối đơn hàng này?</strong></p>
                        <div style="background: #fef3c7; padding: 0.75rem; border-radius: 8px; margin-bottom: 1rem;">
                            <p style="font-size: 13px; color: #92400e; margin: 0;">
                                <i class="fas fa-exclamation-triangle"></i>
                                Số tiền <strong>{{ number_format($serviceOrder->total_amount, 0, ',', '.') }}₫</strong> sẽ được hoàn lại cho người mua.
                            </p>
                        </div>
                        <p style="font-size: 13px; color: #6b7280;">Hành động này không thể hoàn tác.</p>
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
                    submitAction('{{ route("seller.service-orders.reject", $serviceOrder->slug) }}');
                }
            });
        });
    }
});

function submitAction(url, data = {}) {
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
        body: JSON.stringify(data)
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
            text: 'Có lỗi xảy ra khi xử lý.',
            icon: 'error',
            confirmButtonColor: '#ef4444'
        });
    });
}

function acceptDispute(disputeSlug) {
    Swal.fire({
        title: 'Chấp nhận giải quyết khiếu nại',
        html: `
            <div style="text-align: left; padding: 1rem 0;">
                <p style="font-size: 14px; color: #374151;">Khi bạn chấp nhận:</p>
                <ul style="font-size: 13px; color: #6b7280; padding-left: 1.5rem;">
                    <li>Bạn thừa nhận cần điều chỉnh theo yêu cầu</li>
                    <li>Thời gian sẽ được reset để bạn thực hiện</li>
                    <li>Sau khi hoàn thành, nhấn "Báo lại đã hoàn thành"</li>
                </ul>
            </div>
        `,
        input: 'textarea',
        inputLabel: 'Phản hồi của bạn (tùy chọn)',
        inputPlaceholder: 'VD: Mình sẽ kiểm tra và xử lý lại theo yêu cầu của bạn...',
        inputAttributes: {
            maxlength: 1000
        },
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#10b981',
        cancelButtonColor: '#6b7280',
        confirmButtonText: '<i class="fas fa-check"></i> Chấp nhận giải quyết',
        cancelButtonText: 'Hủy'
    }).then((result) => {
        if (result.isConfirmed) {
            const sellerNote = (result.value || '').trim();
            submitAction(
                `{{ url('seller/service-orders/' . $serviceOrder->slug . '/disputes') }}/${disputeSlug}/accept`,
                { seller_note: sellerNote || null }
            );
        }
    });
}

function rejectDispute(disputeSlug) {
    Swal.fire({
        title: 'Từ chối giải quyết khiếu nại',
        html: `
            <div style="text-align: left; padding: 1rem 0;">
                <p style="font-size: 14px; color: #374151;">Khi bạn từ chối:</p>
                <ul style="font-size: 13px; color: #6b7280; padding-left: 1.5rem;">
                    <li>Khiếu nại sẽ được chuyển cho Admin xem xét</li>
                    <li>Admin sẽ đưa ra quyết định cuối cùng</li>
                </ul>
            </div>
        `,
        input: 'textarea',
        inputLabel: 'Lý do từ chối (*)',
        inputPlaceholder: 'Nhập lý do từ chối (ít nhất 10 ký tự)...',
        inputAttributes: {
            maxlength: 1000
        },
        inputValidator: (value) => {
            const v = (value || '').trim();
            if (!v || v.length < 10) {
                return 'Vui lòng nhập lý do từ chối (ít nhất 10 ký tự).';
            }
            return null;
        },
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#ef4444',
        cancelButtonColor: '#6b7280',
        confirmButtonText: '<i class="fas fa-times"></i> Từ chối & Chuyển Admin',
        cancelButtonText: 'Hủy'
    }).then((result) => {
        if (result.isConfirmed) {
            const sellerNote = (result.value || '').trim();
            submitAction(
                `{{ url('seller/service-orders/' . $serviceOrder->slug . '/disputes') }}/${disputeSlug}/reject`,
                { seller_note: sellerNote }
            );
        }
    });
}

function openFileModal(url, name, isImage) {
    const modal = new bootstrap.Modal(document.getElementById('fileModal'));
    const title = document.getElementById('fileModalTitle');
    const body = document.getElementById('fileModalBody');
    const downloadLink = document.getElementById('fileModalDownload');

    title.innerHTML = `<i class="fas fa-file"></i> ${name}`;
    downloadLink.href = url;

    const ext = (url.split('.').pop() || '').toLowerCase().split('?')[0];

    if (isImage) {
        body.innerHTML = `
            <div class="text-center">
                <img src="${url}" alt="${name}" style="max-width: 100%; max-height: 70vh; object-fit: contain;">
            </div>
        `;
    } else if (ext === 'pdf') {
        body.innerHTML = `
            <iframe src="${url}" style="width: 100%; height: 70vh; border: none;"></iframe>
        `;
    } else {
        body.innerHTML = `
            <div class="text-center py-5">
                <i class="fas fa-file fa-4x text-muted mb-3"></i>
                <p class="text-muted mb-0">File: <strong>${name}</strong></p>
                <p class="text-muted">Vui lòng bấm \"Tải xuống\" để xem file.</p>
            </div>
        `;
    }

    modal.show();
}
</script>
@endpush
