@extends('seller.layouts.sidebar')

@section('title', 'Chi tiết hoàn tiền - ' . $refund->slug)

@section('main-content')
    <div class="category-container">
        <div class="mb-4">
            <a href="{{ route('seller.refunds.index', ['tab' => 'refunds']) }}" class="btn back-button">
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
                                    <strong>#{{ $refund->order->slug }}</strong>
                                    <a href="{{ route('seller.orders.show', $refund->order->slug) }}" 
                                        class="btn btn-sm btn-link p-0 ms-2" title="Xem đơn hàng">
                                        <i class="fas fa-external-link-alt"></i>
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
                                <small class="text-muted">Số tiền hoàn:</small>
                                <p class="mb-0">
                                    <strong class="text-danger" style="font-size: 1.1rem;">
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
                    </div>
                </div>
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
                    <button type="button" class="btn back-button" data-bs-dismiss="modal">Đóng</button>
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
<script>
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

