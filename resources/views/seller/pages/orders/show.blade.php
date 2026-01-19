@extends('seller.layouts.sidebar')

@section('title', 'Chi tiết đơn hàng - ' . $order->slug)

@section('main-content')
    <div class="category-container">
        <div class="mb-4">
            <a href="{{ route('seller.orders.index') }}" class="btn back-button">
                <i class="fas fa-arrow-left"></i> Quay lại danh sách
            </a>
        </div>

        <div class="row">
            <div class="col-lg-8">
                <div class="product-info-card mb-3">
                    <div class="card-header py-2">
                        <h3 class="card-title mb-0" style="font-size: 0.95rem; font-weight: 600;">
                            <i class="fas fa-shopping-cart"></i>
                            Thông tin đơn hàng
                        </h3>
                    </div>
                    <div class="card-body py-2">
                        <div class="row g-2 mb-2">
                            <div class="col-6">
                                <small class="text-muted">Mã đơn hàng:</small>
                                <p class="mb-0"><strong>#{{ $order->slug }}</strong></p>
                            </div>
                            <div class="col-6">
                                <small class="text-muted">Trạng thái:</small>
                                <p class="mb-0">
                                    <span class="status-badge bg-{{ $order->status->badgeColor() }} text-white">
                                        {{ $order->status->label() }}
                                    </span>
                                </p>
                            </div>
                        </div>
                        <div class="row g-2 mb-2">
                            <div class="col-12">
                                <small class="text-muted">Người mua:</small>
                                <p class="mb-0"><strong>{{ $order->buyer->full_name }}</strong></p>
                                <small class="text-muted">{{ $order->buyer->email }}</small>
                            </div>
                        </div>
                        <div class="row g-2 mb-2">
                            <div class="col-6">
                                <small class="text-muted">Tổng tiền:</small>
                                <p class="mb-0">
                                    <strong class="text-primary" style="font-size: 1.1rem;">
                                        {{ number_format($order->total_amount, 0, ',', '.') }}₫
                                    </strong>
                                </p>
                            </div>
                            <div class="col-6">
                                <small class="text-muted">Ngày tạo:</small>
                                <p class="mb-0">{{ $order->created_at->format('d/m/Y H:i:s') }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="product-info-card mb-3">
                    <div class="card-header py-2 d-block">
                        <div class="d-flex justify-content-between align-items-center">
                            <h3 class="card-title mb-0" style="font-size: 0.95rem; font-weight: 600;">
                                <i class="fas fa-box"></i>
                                Sản phẩm ({{ $order->items->count() }})
                            </h3>
                            @php
                                $soldValuesCount = $order->items->sum(function($item) {
                                    return $item->productValues->where('status', \App\Enums\ProductValueStatus::SOLD)->count();
                                });
                            @endphp
                            @if($soldValuesCount > 0)
                                <button type="button" class="btn btn-sm bg-primary-1 text-white rounded-4" id="viewAllSoldValuesBtn">
                                    <i class="fas fa-eye"></i> Xem giá trị đã bán ({{ $soldValuesCount }})
                                </button>
                            @endif
                        </div>
                    </div>
                    <div class="card-body py-2">
                        <div class="space-y-2">
                            @foreach($order->items as $item)
                                @php
                                    $soldValues = $item->productValues->where('status', \App\Enums\ProductValueStatus::SOLD);
                                @endphp
                                <div class="p-2 bg-gray-50 rounded mb-2">
                                    <div class="d-flex justify-content-between align-items-start mb-1">
                                        <div class="flex-1">
                                            <a class="color-primary" href="{{ route('products.show', $item->productVariant->product->slug) }}" class="mb-1"><strong>{{ $item->productVariant->product->name }}</strong></a>
                                            <p class="mb-1 text-muted" style="font-size: 0.85rem;">Biến thể: {{ $item->productVariant->name }}</p>
                                            <p class="mb-0 text-muted" style="font-size: 0.85rem;">
                                                Số lượng: {{ $item->quantity }} × {{ number_format($item->price, 0, ',', '.') }}₫ = 
                                                <strong class="text-primary">{{ number_format($item->price * $item->quantity, 0, ',', '.') }}₫</strong>
                                            </p>
                                        </div>
                                    </div>
                                    @if($item->productValues->count() > 0)
                                        <div class="mt-2">
                                            <small class="text-muted d-block mb-1">Giá trị sản phẩm ({{ $item->productValues->count() }}):</small>
                                            <div class="space-y-1">
                                                @foreach($item->productValues as $value)
                                                    <div class="d-flex justify-content-between align-items-center p-1 bg-white rounded text-xs">
                                                        <span class="font-mono">#{{ $value->slug }}</span>
                                                        <span class="status-badge bg-{{ $value->status->badgeColor() }} text-white">
                                                            {{ $value->status->label() }}
                                                        </span>
                                                    </div>
                                                @endforeach
                                            </div>
                                            @if($soldValues->count() > 0)
                                                <div class="mt-1.5 pt-1.5 border-top">
                                                    <small class="text-muted">Đã bán: {{ $soldValues->count() }} giá trị</small>
                                                </div>
                                            @endif
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                @if($order->disputes->count() > 0)
                    <div class="product-info-card mb-3">
                        <div class="card-header py-2">
                            <h3 class="card-title mb-0" style="font-size: 0.95rem; font-weight: 600;">
                                <i class="fas fa-exclamation-triangle"></i>
                                Tranh chấp ({{ $order->disputes->count() }})
                            </h3>
                        </div>
                        <div class="card-body py-2">
                            @foreach($order->disputes as $dispute)
                                <div class="p-2 bg-{{ $dispute->status->badgeColor() }}-50 rounded mb-2">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div class="flex-1">
                                            <p class="mb-1">
                                                <strong>{{ $dispute->orderItem->productVariant->product->name }}</strong>
                                                <span class="status-badge bg-{{ $dispute->status->badgeColor() }} ms-2 text-white">
                                                    {{ $dispute->status->label() }}
                                                </span>
                                            </p>
                                            <p class="mb-0 text-muted" style="font-size: 0.85rem;">{{ $dispute->reason }}</p>
                                        </div>
                                        <a href="{{ route('seller.refunds.dispute.show', $dispute->slug) }}" class="btn btn-sm btn-primary">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                @if($order->refunds->count() > 0)
                    <div class="product-info-card mb-3">
                        <div class="card-header py-2">
                            <h3 class="card-title mb-0" style="font-size: 0.95rem; font-weight: 600;">
                                <i class="fas fa-undo"></i>
                                Hoàn tiền ({{ $order->refunds->count() }})
                            </h3>
                        </div>
                        <div class="card-body py-2">
                            @foreach($order->refunds as $refund)
                                <div class="p-2 bg-{{ $refund->status->badgeColor() }}-50 rounded mb-2">
                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                        <div class="flex-1">
                                            <p class="mb-1">
                                                <strong>{{ number_format($refund->total_amount, 0, ',', '.') }}₫</strong>
                                                <span class="status-badge bg-{{ $refund->status->badgeColor() }} ms-2 text-white">
                                                    {{ $refund->status->label() }}
                                                </span>
                                            </p>
                                            <p class="mb-0 text-muted" style="font-size: 0.85rem;">
                                                {{ $refund->created_at->format('d/m/Y H:i') }}
                                            </p>
                                        </div>
                                    </div>
                                    @if($refund->items->count() > 0)
                                        <div class="mt-2 pt-2 border-top">
                                            <small class="text-muted d-block mb-1">Giá trị đã hoàn ({{ $refund->items->count() }}):</small>
                                            <div class="d-flex flex-wrap gap-1">
                                                @foreach($refund->items as $refundItem)
                                                    <span class="badge bg-danger text-white" style="font-size: 0.7rem;">
                                                        #{{ $refundItem->productValue->slug }} 
                                                        <small>({{ number_format($refundItem->amount, 0, ',', '.') }}₫)</small>
                                                    </span>
                                                @endforeach
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
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
                            <span class="summary-label">Tổng sản phẩm:</span>
                            <span class="summary-value">{{ $order->items->sum('quantity') }}</span>
                        </div>
                        <div class="summary-item">
                            <span class="summary-label">Số loại:</span>
                            <span class="summary-value">{{ $order->items->count() }}</span>
                        </div>
                        <div class="summary-item">
                            <span class="summary-label">Tổng tiền:</span>
                            <span class="summary-value text-primary">
                                {{ number_format($order->total_amount, 0, ',', '.') }}₫
                            </span>
                        </div>
                        <div class="summary-item">
                            <span class="summary-label">Trạng thái:</span>
                            <span class="summary-value">
                                <span class="status-badge bg-{{ $order->status->badgeColor() }} text-white">
                                    {{ $order->status->label() }}
                                </span>
                            </span>
                        </div>
                        
                        <div style="border-top: 1px solid #e5e7eb; padding-top: 0.75rem; margin-top: 0.75rem;">
                            @if($order->status === \App\Enums\OrderStatus::DISPUTED)
                                @if($expectedRefundAmount > 0)
                                    <div class="summary-item">
                                        <span class="summary-label">Dự kiến hoàn buyer:</span>
                                        <span class="summary-value text-warning">
                                            {{ number_format($expectedRefundAmount, 0, ',', '.') }}₫
                                            <small class="text-muted d-block" style="font-size: 0.7rem;">
                                                ({{ $disputedValuesCount }} giá trị)
                                            </small>
                                        </span>
                                    </div>
                                @endif
                                <div class="summary-item">
                                    <span class="summary-label">Phí sàn (dự kiến):</span>
                                    <span class="summary-value text-warning">
                                        {{ number_format($expectedCommission, 0, ',', '.') }}₫
                                        @if($expectedCommission > 0)
                                            <small class="text-muted">({{ number_format(($expectedCommission / $order->total_amount) * 100, 1) }}%)</small>
                                        @endif
                                    </span>
                                </div>
                                <div class="summary-item">
                                    <span class="summary-label">Bạn sẽ nhận (dự kiến):</span>
                                    <span class="summary-value text-info">
                                        {{ number_format($expectedSellerAmount, 0, ',', '.') }}₫
                                    </span>
                                </div>
                                @if($sellerEarnings > 0)
                                    <div class="summary-item">
                                        <span class="summary-label">Bạn đã nhận:</span>
                                        <span class="summary-value text-success">
                                            {{ number_format($sellerEarnings, 0, ',', '.') }}₫
                                            @if($sellerSaleTransaction)
                                                <small class="text-muted d-block" style="font-size: 0.7rem;">
                                                    {{ $sellerSaleTransaction->created_at->format('d/m/Y H:i') }}
                                                </small>
                                            @endif
                                        </span>
                                    </div>
                                @endif
                            @elseif(in_array($order->status, [\App\Enums\OrderStatus::PARTIAL_REFUNDED, \App\Enums\OrderStatus::REFUNDED]))
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
                                        @if($sellerSaleTransaction)
                                            <small class="text-muted d-block" style="font-size: 0.7rem;">
                                                {{ $sellerSaleTransaction->created_at->format('d/m/Y H:i') }}
                                            </small>
                                        @endif
                                    </span>
                                </div>
                            @elseif($order->status === \App\Enums\OrderStatus::COMPLETED)
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
                                        @if($sellerSaleTransaction)
                                            <small class="text-muted d-block" style="font-size: 0.7rem;">
                                                {{ $sellerSaleTransaction->created_at->format('d/m/Y H:i') }}
                                            </small>
                                        @endif
                                    </span>
                                </div>
                            @else
                                <div class="summary-item">
                                    <span class="summary-label">Phí sàn:</span>
                                    <span class="summary-value text-warning">
                                        {{ number_format($expectedCommission, 0, ',', '.') }}₫
                                        <small class="text-muted">({{ number_format($commissionRate, 1) }}%)</small>
                                    </span>
                                </div>
                                <div class="summary-item">
                                    <span class="summary-label">Bạn sẽ nhận:</span>
                                    <span class="summary-value text-info">
                                        {{ number_format($expectedSellerAmount, 0, ',', '.') }}₫
                                    </span>
                                </div>
                                @if($sellerEarnings > 0)
                                    <div class="summary-item">
                                        <span class="summary-label">Bạn đã nhận:</span>
                                        <span class="summary-value text-success">
                                            {{ number_format($sellerEarnings, 0, ',', '.') }}₫
                                            @if($sellerSaleTransaction)
                                                <small class="text-muted d-block" style="font-size: 0.7rem;">
                                                    {{ $sellerSaleTransaction->created_at->format('d/m/Y H:i') }}
                                                </small>
                                            @endif
                                        </span>
                                    </div>
                                @endif
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="soldValuesModal" tabindex="-1" aria-labelledby="soldValuesModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="soldValuesModalLabel">
                        <i class="fas fa-eye"></i> Giá trị sản phẩm đã bán
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="soldValuesModalContent">
                    <div class="text-center py-4">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <p class="mt-2 text-muted small">Đang tải dữ liệu...</p>
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
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const viewAllSoldValuesBtn = document.getElementById('viewAllSoldValuesBtn');
        if (viewAllSoldValuesBtn) {
            viewAllSoldValuesBtn.addEventListener('click', function() {
                const modal = new bootstrap.Modal(document.getElementById('soldValuesModal'));
                const content = document.getElementById('soldValuesModalContent');
                
                content.innerHTML = `
                    <div class="text-center py-4">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <p class="mt-2 text-muted small">Đang tải dữ liệu...</p>
                    </div>
                `;
                
                modal.show();
                
                const slugs = [
                    @php
                        $soldValueSlugs = [];
                        foreach($order->items as $item) {
                            foreach($item->productValues->where('status', \App\Enums\ProductValueStatus::SOLD) as $value) {
                                $soldValueSlugs[] = $value->slug;
                            }
                        }
                    @endphp
                    @foreach($soldValueSlugs as $slug)
                        '{{ $slug }}'{{ !$loop->last ? ',' : '' }}
                    @endforeach
                ];
                
                if (slugs.length === 0) {
                    content.innerHTML = `
                        <div class="text-center py-4">
                            <p class="text-muted mb-0">Không có giá trị đã bán nào.</p>
                        </div>
                    `;
                    return;
                }
                
                const promises = slugs.map(slug => 
                    fetch(`/product-values/${slug}/data`, {
                        method: 'GET',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json'
                        }
                    }).then(response => response.json())
                );
                
                Promise.all(promises)
                    .then(results => {
                        let html = `<div class="list-group list-group-flush">`;
                        let hasError = false;
                        let errorMessage = '';
                        
                        results.forEach((data, index) => {
                            if (data.success) {
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
                                        <div class="d-flex align-items-center gap-2">
                                            <span class="badge bg-secondary" style="font-size: 0.7rem; min-width: 80px;">#${data.value.slug}</span>
                                            <span class="badge ${
                                                data.value.status_color === 'success' ? 'bg-success' :
                                                (data.value.status_color === 'warning' ? 'bg-warning text-dark' :
                                                (data.value.status_color === 'danger' ? 'bg-danger' : 'bg-primary'))
                                            }" style="font-size: 0.7rem;">
                                                ${data.value.status}
                                            </span>
                                            <code class="flex-grow-1 text-break" style="font-size: 0.75rem; background: transparent; padding: 0;">${displayText}</code>
                                        </div>
                                    </div>
                                `;
                            } else {
                                hasError = true;
                                errorMessage = data.message || 'Có lỗi xảy ra khi tải dữ liệu.';
                            }
                        });
                        
                        html += `</div>`;
                        
                        if (hasError && results.filter(r => r.success).length === 0) {
                            content.innerHTML = `
                                <div class="text-center py-4">
                                    <p class="text-danger mb-0">${errorMessage}</p>
                                </div>
                            `;
                        } else {
                            content.innerHTML = html;
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        content.innerHTML = `
                            <div class="text-center py-4">
                                <p class="text-danger mb-0">Có lỗi xảy ra khi tải dữ liệu.</p>
                            </div>
                        `;
                    });
            });
        }
    });
</script>
@endpush


