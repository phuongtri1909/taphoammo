@extends('admin.layouts.sidebar')

@section('title', 'Chi tiết hoàn tiền - ' . $refund->slug)

@section('main-content')
    <div class="category-container">
        <div class="mb-4">
            <a href="{{ route('admin.refunds.index') }}" class="btn back-button">
                <i class="fas fa-arrow-left"></i> Quay lại danh sách
            </a>
        </div>

        <div class="row">
            <div class="col-lg-8">
                <div class="product-info-card mb-3">
                    <div class="card-header py-2">
                        <h3 class="card-title mb-0" style="font-size: 0.95rem; font-weight: 600;">
                            <i class="fas fa-undo"></i>
                            Thông tin hoàn tiền
                        </h3>
                    </div>
                    <div class="card-body py-2">
                        <div class="row g-2 mb-2">
                            <div class="col-6">
                                <small class="text-muted">Đơn hàng:</small>
                                <p class="mb-0">
                                    <a class="color-primary" href="{{ route('admin.orders.show', $refund->order->slug) }}">
                                        <strong>#{{ $refund->order->slug }}</strong>
                                    </a>
                                </p>
                            </div>
                            <div class="col-6">
                                <small class="text-muted">Trạng thái:</small>
                                <p class="mb-0">
                                    <span class="status-badge bg-{{ $refund->status->badgeColor() }} text-white">
                                        {{ $refund->status->label() }}
                                    </span>
                                </p>
                            </div>
                        </div>
                        <div class="row g-2 mb-2">
                            <div class="col-6">
                                <small class="text-muted">Người mua:</small>
                                <p class="mb-0"><strong>{{ $refund->buyer->full_name }}</strong></p>
                                <small class="text-muted">{{ $refund->buyer->email }}</small>
                            </div>
                            <div class="col-6">
                                <small class="text-muted">Số tiền:</small>
                                <p class="mb-0">
                                    <strong class="text-primary" style="font-size: 1.1rem;">
                                        {{ number_format($refund->total_amount, 0, ',', '.') }}₫
                                    </strong>
                                </p>
                            </div>
                        </div>
                        <div class="row g-2">
                            <div class="col-6">
                                <small class="text-muted">Ngày tạo:</small>
                                <p class="mb-0">{{ $refund->created_at->format('d/m/Y H:i:s') }}</p>
                            </div>
                            @if($refund->status !== \App\Enums\RefundStatus::PENDING && $refund->updated_at != $refund->created_at)
                                <div class="col-6">
                                    <small class="text-muted">Ngày xử lý:</small>
                                    <p class="mb-0">{{ $refund->updated_at->format('d/m/Y H:i:s') }}</p>
                                </div>
                            @endif
                        </div>
                        @if($refund->processedBy)
                            <div class="row g-2 mt-2">
                                <div class="col-12">
                                    <small class="text-muted">Xử lý bởi:</small>
                                    <p class="mb-0">{{ $refund->processedBy->full_name }}</p>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

                <div class="product-info-card mb-3">
                    <div class="card-header py-2 d-flex justify-content-between align-items-center">
                        <h3 class="card-title mb-0" style="font-size: 0.95rem; font-weight: 600;">
                            <i class="fas fa-list"></i>
                            Chi tiết hoàn tiền ({{ $refund->items->count() }} giá trị)
                        </h3>
                        @if($refund->items->count() > 0)
                            <button type="button" class="btn btn-sm action-button" onclick="showAllValuesModal()">
                                <i class="fas fa-eye"></i> Xem tất cả giá trị
                            </button>
                        @endif
                    </div>
                    <div class="card-body py-2">
                        <div class="space-y-1">
                            @foreach($refund->items as $item)
                                <div class="d-flex justify-content-between align-items-center p-1.5 bg-gray-50 rounded mb-1" style="font-size: 0.85rem;">
                                    <span class="text-gray-700 font-mono">
                                        #{{ $item->productValue->slug }}
                                        <span class="badge bg-secondary ms-1" style="font-size: 0.7rem;">
                                            {{ $item->productValue->status->label() }}
                                        </span>
                                    </span>
                                    <span class="font-semibold text-danger">
                                        {{ number_format($item->amount, 0, ',', '.') }}₫
                                    </span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <div class="product-info-card mb-3">
                    <div class="card-header py-2">
                        <h3 class="card-title mb-0" style="font-size: 0.95rem; font-weight: 600;">
                            <i class="fas fa-shopping-cart"></i>
                            Thông tin đơn hàng liên quan
                        </h3>
                    </div>
                    <div class="card-body py-2">
                        <div class="row g-2">
                            <div class="col-6">
                                <small class="text-muted">Tổng tiền đơn hàng:</small>
                                <p class="mb-0">
                                    <strong>{{ number_format($refund->order->total_amount, 0, ',', '.') }}₫</strong>
                                </p>
                            </div>
                            <div class="col-6">
                                <small class="text-muted">Trạng thái đơn hàng:</small>
                                <p class="mb-0">
                                    <span class="status-badge bg-{{ $refund->order->status->badgeColor() }} text-white">
                                        {{ $refund->order->status->label() }}
                                    </span>
                                </p>
                            </div>
                        </div>
                        @if($refund->order->seller)
                            <div class="row g-2 mt-2">
                                <div class="col-12">
                                    <small class="text-muted">Người bán:</small>
                                    <p class="mb-0"><strong>{{ $refund->order->seller->full_name }}</strong></p>
                                    <small class="text-muted">{{ $refund->order->seller->email }}</small>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="summary-card" style="position: sticky; top: 20px;">
                    <div class="summary-header">
                        <h3>
                            <i class="fas fa-info-circle"></i>
                            Tóm tắt
                        </h3>
                    </div>
                    <div class="summary-body">
                        <div class="summary-item">
                            <span class="summary-label">Số tiền đã hoàn:</span>
                            <span class="summary-value text-danger">
                                {{ number_format($refund->total_amount, 0, ',', '.') }}₫
                            </span>
                        </div>
                        <div class="summary-item">
                            <span class="summary-label">Số giá trị đã hoàn:</span>
                            <span class="summary-value">
                                {{ $refund->items->count() }} giá trị
                            </span>
                        </div>
                        <hr class="my-3">
                        <div class="summary-item">
                            <span class="summary-label">Trạng thái:</span>
                            <span class="summary-value">
                                <span class="status-badge bg-{{ $refund->status->badgeColor() }} text-white">
                                    {{ $refund->status->label() }}
                                </span>
                            </span>
                        </div>
                        @if(isset($dispute) && $dispute->admin_note)
                            <hr class="my-3">
                            <div class="summary-item">
                                <span class="summary-label">Ghi chú từ Admin:</span>
                                <span class="summary-value text-muted" style="font-size: 0.85rem;">
                                    {{ $dispute->admin_note }}
                                </span>
                            </div>
                        @endif
                        @if($refund->processedBy)
                            <hr class="my-3">
                            <div class="summary-item">
                                <span class="summary-label">Xử lý bởi:</span>
                                <span class="summary-value">
                                    {{ $refund->processedBy->full_name }}
                                </span>
                            </div>
                        @endif
                    </div>
                </div>

                @if($refund->status === \App\Enums\RefundStatus::PENDING)
                    <div class="summary-card mt-3" style="position: sticky; top: 400px;">
                        <div class="summary-header">
                            <h3>
                                <i class="fas fa-cog"></i>
                                Xử lý hoàn tiền
                            </h3>
                        </div>
                        <div class="summary-body">
                            <form id="approveRefundForm" class="mb-4">
                                @csrf
                                <button type="submit" class="btn-modern success w-100">
                                    <i class="fas fa-check"></i> Phê duyệt & Hoàn tiền
                                </button>
                            </form>

                            <hr class="my-4">

                            <form id="rejectRefundForm">
                                @csrf
                                <div class="form-group mb-3">
                                    <label class="form-label-custom">Lý do từ chối <span class="required-mark">*</span></label>
                                    <textarea name="admin_note" class="custom-input" rows="3" required placeholder="Nhập lý do từ chối..."></textarea>
                                </div>
                                <button type="submit" class="btn-modern danger w-100">
                                    <i class="fas fa-times"></i> Từ chối hoàn tiền
                                </button>
                            </form>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <div class="modal fade" id="allValuesModal" tabindex="-1" aria-labelledby="allValuesModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="allValuesModalLabel">
                        <i class="fas fa-list"></i> Tất cả giá trị đã hoàn tiền
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div id="allValuesContent">
                        <div class="text-center py-4">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                            <p class="mt-2 text-muted">Đang tải dữ liệu...</p>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
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
        const approveForm = document.getElementById('approveRefundForm');
        const rejectForm = document.getElementById('rejectRefundForm');

        if (approveForm) {
            approveForm.addEventListener('submit', function(e) {
                e.preventDefault();

                Swal.fire({
                    title: 'Xác nhận phê duyệt hoàn tiền',
                    html: `
                        <div style="text-align: center; padding: 1rem 0;">
                            <div style="width: 80px; height: 80px; margin: 0 auto 1.5rem; background: linear-gradient(135deg, #10b981, #059669); border-radius: 50%; display: flex; align-items: center; justify-content: center; box-shadow: 0 10px 25px rgba(16, 185, 129, 0.3);">
                                <i class="fas fa-check-circle" style="font-size: 40px; color: white;"></i>
                            </div>
                            <p style="font-size: 16px; color: #374151; margin: 0; font-weight: 600;">Bạn có chắc chắn muốn phê duyệt và hoàn tiền <strong>{{ number_format($refund->total_amount, 0, ',', '.') }}₫</strong> cho người mua?</p>
                        </div>
                    `,
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#10b981',
                    cancelButtonColor: '#6b7280',
                    confirmButtonText: '<i class="fas fa-check-circle mr-2"></i>Xác nhận',
                    cancelButtonText: '<i class="fas fa-times mr-2"></i>Hủy',
                    width: '480px'
                }).then((result) => {
                    if (result.isConfirmed) {
                        Swal.fire({
                            title: 'Đang xử lý...',
                            allowOutsideClick: false,
                            allowEscapeKey: false,
                            didOpen: () => {
                                Swal.showLoading();
                            }
                        });

                        fetch('{{ route("admin.refunds.approve", $refund->slug) }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Accept': 'application/json'
                            }
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                Swal.fire({
                                    title: 'Thành công!',
                                    html: `
                                        <div style="text-align: center; padding: 1rem 0;">
                                            <div style="width: 80px; height: 80px; margin: 0 auto 1.5rem; background: linear-gradient(135deg, #10b981, #059669); border-radius: 50%; display: flex; align-items: center; justify-content: center; box-shadow: 0 10px 25px rgba(16, 185, 129, 0.3);">
                                                <i class="fas fa-check-circle" style="font-size: 40px; color: white;"></i>
                                            </div>
                                            <p style="font-size: 16px; color: #374151; margin: 0; font-weight: 600;">${data.message}</p>
                                        </div>
                                    `,
                                    icon: 'success',
                                    confirmButtonColor: '#10b981',
                                    confirmButtonText: '<i class="fas fa-check mr-2"></i>Đồng ý',
                                    width: '480px'
                                }).then(() => {
                                    window.location.reload();
                                });
                            } else {
                                Swal.fire({
                                    title: 'Lỗi',
                                    html: `
                                        <div style="text-align: center; padding: 1rem 0;">
                                            <div style="width: 80px; height: 80px; margin: 0 auto 1.5rem; background: linear-gradient(135deg, #ef4444, #dc2626); border-radius: 50%; display: flex; align-items: center; justify-content: center; box-shadow: 0 10px 25px rgba(239, 68, 68, 0.3);">
                                                <i class="fas fa-times-circle" style="font-size: 40px; color: white;"></i>
                                            </div>
                                            <p style="font-size: 16px; color: #374151; margin: 0; font-weight: 600;">${data.message || 'Có lỗi xảy ra.'}</p>
                                        </div>
                                    `,
                                    icon: 'error',
                                    confirmButtonColor: '#ef4444',
                                    confirmButtonText: '<i class="fas fa-check mr-2"></i>Đồng ý',
                                    width: '480px'
                                });
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            Swal.fire({
                                title: 'Lỗi',
                                html: `
                                    <div style="text-align: center; padding: 1rem 0;">
                                        <div style="width: 80px; height: 80px; margin: 0 auto 1.5rem; background: linear-gradient(135deg, #ef4444, #dc2626); border-radius: 50%; display: flex; align-items: center; justify-content: center; box-shadow: 0 10px 25px rgba(239, 68, 68, 0.3);">
                                            <i class="fas fa-times-circle" style="font-size: 40px; color: white;"></i>
                                        </div>
                                        <p style="font-size: 16px; color: #374151; margin: 0; font-weight: 600;">Có lỗi xảy ra khi xử lý hoàn tiền.</p>
                                    </div>
                                `,
                                icon: 'error',
                                confirmButtonColor: '#ef4444',
                                confirmButtonText: '<i class="fas fa-check mr-2"></i>Đồng ý',
                                width: '480px'
                            });
                        });
                    }
                });
            });
        }

        if (rejectForm) {
            rejectForm.addEventListener('submit', function(e) {
                e.preventDefault();
                
                const note = document.querySelector('#rejectRefundForm textarea[name="admin_note"]').value.trim();
                if (!note) {
                    Swal.fire({
                        title: 'Lỗi',
                        html: `
                            <div style="text-align: center; padding: 1rem 0;">
                                <div style="width: 80px; height: 80px; margin: 0 auto 1.5rem; background: linear-gradient(135deg, #f59e0b, #d97706); border-radius: 50%; display: flex; align-items: center; justify-content: center; box-shadow: 0 10px 25px rgba(245, 158, 11, 0.3);">
                                    <i class="fas fa-exclamation-triangle" style="font-size: 40px; color: white;"></i>
                                </div>
                                <p style="font-size: 16px; color: #374151; margin: 0; font-weight: 600;">Vui lòng nhập lý do từ chối.</p>
                            </div>
                        `,
                        icon: 'warning',
                        confirmButtonColor: '#f59e0b',
                        confirmButtonText: '<i class="fas fa-check mr-2"></i>Đồng ý',
                        width: '480px'
                    });
                    return;
                }

                Swal.fire({
                    title: 'Xác nhận từ chối hoàn tiền',
                    html: `
                        <div style="text-align: center; padding: 1rem 0;">
                            <div style="width: 80px; height: 80px; margin: 0 auto 1.5rem; background: linear-gradient(135deg, #ef4444, #dc2626); border-radius: 50%; display: flex; align-items: center; justify-content: center; box-shadow: 0 10px 25px rgba(239, 68, 68, 0.3);">
                                <i class="fas fa-times-circle" style="font-size: 40px; color: white;"></i>
                            </div>
                            <p style="font-size: 16px; color: #374151; margin: 0 0 1rem 0; font-weight: 600;">Bạn có chắc chắn muốn từ chối hoàn tiền này?</p>
                            <div style="background: #fef2f2; border-left: 4px solid #ef4444; padding: 0.75rem; border-radius: 0.5rem; text-align: left;">
                                <strong style="color: #991b1b; display: block; margin-bottom: 0.5rem;">Lý do từ chối:</strong>
                                <p style="color: #7f1d1d; margin: 0; font-size: 0.9rem;">${note}</p>
                            </div>
                        </div>
                    `,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#ef4444',
                    cancelButtonColor: '#6b7280',
                    confirmButtonText: '<i class="fas fa-times-circle mr-2"></i>Xác nhận từ chối',
                    cancelButtonText: '<i class="fas fa-arrow-left mr-2"></i>Hủy',
                    width: '520px'
                }).then((result) => {
                    if (result.isConfirmed) {
                        Swal.fire({
                            title: 'Đang xử lý...',
                            allowOutsideClick: false,
                            allowEscapeKey: false,
                            didOpen: () => {
                                Swal.showLoading();
                            }
                        });

                        fetch('{{ route("admin.refunds.reject", $refund->slug) }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify({ admin_note: note })
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                Swal.fire({
                                    title: 'Thành công!',
                                    html: `
                                        <div style="text-align: center; padding: 1rem 0;">
                                            <div style="width: 80px; height: 80px; margin: 0 auto 1.5rem; background: linear-gradient(135deg, #10b981, #059669); border-radius: 50%; display: flex; align-items: center; justify-content: center; box-shadow: 0 10px 25px rgba(16, 185, 129, 0.3);">
                                                <i class="fas fa-check-circle" style="font-size: 40px; color: white;"></i>
                                            </div>
                                            <p style="font-size: 16px; color: #374151; margin: 0; font-weight: 600;">${data.message}</p>
                                        </div>
                                    `,
                                    icon: 'success',
                                    confirmButtonColor: '#10b981',
                                    confirmButtonText: '<i class="fas fa-check mr-2"></i>Đồng ý',
                                    width: '480px'
                                }).then(() => {
                                    window.location.reload();
                                });
                            } else {
                                Swal.fire({
                                    title: 'Lỗi',
                                    html: `
                                        <div style="text-align: center; padding: 1rem 0;">
                                            <div style="width: 80px; height: 80px; margin: 0 auto 1.5rem; background: linear-gradient(135deg, #ef4444, #dc2626); border-radius: 50%; display: flex; align-items: center; justify-content: center; box-shadow: 0 10px 25px rgba(239, 68, 68, 0.3);">
                                                <i class="fas fa-times-circle" style="font-size: 40px; color: white;"></i>
                                            </div>
                                            <p style="font-size: 16px; color: #374151; margin: 0; font-weight: 600;">${data.message || 'Có lỗi xảy ra.'}</p>
                                        </div>
                                    `,
                                    icon: 'error',
                                    confirmButtonColor: '#ef4444',
                                    confirmButtonText: '<i class="fas fa-check mr-2"></i>Đồng ý',
                                    width: '480px'
                                });
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            Swal.fire({
                                title: 'Lỗi',
                                html: `
                                    <div style="text-align: center; padding: 1rem 0;">
                                        <div style="width: 80px; height: 80px; margin: 0 auto 1.5rem; background: linear-gradient(135deg, #ef4444, #dc2626); border-radius: 50%; display: flex; align-items: center; justify-content: center; box-shadow: 0 10px 25px rgba(239, 68, 68, 0.3);">
                                            <i class="fas fa-times-circle" style="font-size: 40px; color: white;"></i>
                                        </div>
                                        <p style="font-size: 16px; color: #374151; margin: 0; font-weight: 600;">Có lỗi xảy ra khi từ chối hoàn tiền.</p>
                                    </div>
                                `,
                                icon: 'error',
                                confirmButtonColor: '#ef4444',
                                confirmButtonText: '<i class="fas fa-check mr-2"></i>Đồng ý',
                                width: '480px'
                            });
                        });
                    }
                });
            });
        }
    });

    function showAllValuesModal() {
        const modal = new bootstrap.Modal(document.getElementById('allValuesModal'));
        modal.show();

        const slugs = @json($refund->items->pluck('productValue.slug')->toArray());
        const amounts = @json($refund->items->mapWithKeys(function($item) { return [$item->productValue->slug => $item->amount]; })->toArray());
        const content = document.getElementById('allValuesContent');

        Promise.all(slugs.map(slug => 
            fetch(`{{ url('/product-values') }}/${slug}/data`)
                .then(res => res.json())
                .then(data => ({ slug, data }))
                .catch(err => ({ slug, error: err.message }))
        )).then(results => {
            let html = '<div class="list-group list-group-flush">';
            
            results.forEach(({ slug, data, error }) => {
                if (error) {
                    html += `
                        <div class="list-group-item">
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="badge bg-secondary">#${slug}</span>
                                <span class="text-danger">Lỗi: ${error}</span>
                            </div>
                        </div>
                    `;
                } else {
                    const amount = amounts[slug] || 0;
                    const valueText = data.data?.value || '';
                    const passwordText = data.data?.password || '';
                    const otherFields = [];
                    
                    if (data.data) {
                        Object.keys(data.data).forEach(key => {
                            if (!['value', 'password'].includes(key)) {
                                const val = typeof data.data[key] === 'object' 
                                    ? JSON.stringify(data.data[key])
                                    : data.data[key];
                                otherFields.push(`${key}: ${val}`);
                            }
                        });
                    }
                    
                    const displayText = [
                        valueText,
                        passwordText ? `Pass: ${passwordText}` : '',
                        ...otherFields
                    ].filter(Boolean).join(' | ') || 'Không có dữ liệu';
                    
                    html += `
                        <div class="list-group-item px-2 py-1.5 border-bottom">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <div class="d-flex align-items-center gap-2">
                                    <span class="badge bg-secondary" style="font-size: 0.7rem; min-width: 80px;">#${data.value.slug}</span>
                                    <span class="badge bg-${data.value.status.badge_color} text-white" style="font-size: 0.7rem;">
                                        ${data.value.status.label}
                                    </span>
                                </div>
                                <span class="font-semibold text-danger">${parseInt(amount).toLocaleString('vi-VN')}₫</span>
                            </div>
                            <div class="mt-1">
                                <code class="text-break" style="font-size: 0.75rem; background: transparent; padding: 0; color: #6b7280;">${displayText}</code>
                            </div>
                        </div>
                    `;
                }
            });
            
            html += '</div>';
            content.innerHTML = html;
        }).catch(error => {
            console.error('Error:', error);
            content.innerHTML = `
                <div class="text-center py-4">
                    <p class="text-danger mb-0">Có lỗi xảy ra khi tải dữ liệu.</p>
                </div>
            `;
        });
    }
</script>
@endpush


