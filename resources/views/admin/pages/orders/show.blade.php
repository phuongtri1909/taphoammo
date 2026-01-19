@extends('admin.layouts.sidebar')

@section('title', 'Chi tiết đơn hàng - ' . $order->slug)

@section('main-content')
    <div class="category-container">
        <div class="mb-4">
            <a href="{{ route('admin.orders.index') }}" class="btn back-button">
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
                            <div class="col-6">
                                <small class="text-muted">Người mua:</small>
                                <p class="mb-0"><strong>{{ $order->buyer->full_name }}</strong></p>
                                <small class="text-muted">{{ $order->buyer->email }}</small>
                            </div>
                            <div class="col-6">
                                <small class="text-muted">Người bán:</small>
                                <p class="mb-0"><strong>{{ $order->seller->full_name }}</strong></p>
                                <small class="text-muted">{{ $order->seller->email }}</small>
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
                    <div class="card-header py-2">
                        <h3 class="card-title mb-0" style="font-size: 0.95rem; font-weight: 600;">
                            <i class="fas fa-box"></i>
                            Sản phẩm ({{ $order->items->count() }})
                        </h3>
                    </div>
                    <div class="card-body py-2">
                        <div class="space-y-2">
                            @foreach($order->items as $item)
                                <div class="p-2 bg-gray-50 rounded mb-2">
                                    <div class="d-flex justify-content-between align-items-start mb-1">
                                        <div class="flex-1">
                                            <a class="mb-1 color-primary" href="{{ route('admin.products.show', $item->productVariant->product->slug) }}"><strong>{{ $item->productVariant->product->name }}</strong></a>
                                            <p class="mb-1 text-muted" style="font-size: 0.85rem;">Biến thể: {{ $item->productVariant->name }}</p>
                                            <p class="mb-0 text-muted" style="font-size: 0.85rem;">
                                                Số lượng: {{ $item->quantity }} × {{ number_format($item->price, 0, ',', '.') }}₫ = 
                                                <strong class="text-primary">{{ number_format($item->price * $item->quantity, 0, ',', '.') }}₫</strong>
                                            </p>
                                        </div>
                                    </div>
                                    @if($item->productValues->count() > 0)
                                        <div class="mt-2">
                                            <small class="text-muted d-block mb-1">Giá trị sản phẩm:</small>
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
                                        <a href="{{ route('admin.disputes.show', $dispute->slug) }}" class="btn btn-sm btn-primary">
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
                                        <a href="{{ route('admin.refunds.show', $refund->slug) }}" class="btn btn-sm btn-primary">
                                            <i class="fas fa-eye"></i>
                                        </a>
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
                                    <span class="summary-label">Hoa hồng sàn (dự kiến):</span>
                                    <span class="summary-value text-warning">
                                        {{ number_format($expectedCommission, 0, ',', '.') }}₫
                                        @if($expectedCommission > 0)
                                            <small class="text-muted">({{ number_format(($expectedCommission / $order->total_amount) * 100, 1) }}%)</small>
                                        @endif
                                    </span>
                                </div>
                                <div class="summary-item">
                                    <span class="summary-label">Seller sẽ nhận (dự kiến):</span>
                                    <span class="summary-value text-info">
                                        {{ number_format($expectedSellerAmount, 0, ',', '.') }}₫
                                    </span>
                                </div>
                                @if($sellerEarnings > 0)
                                    <div class="summary-item">
                                        <span class="summary-label">Seller đã nhận:</span>
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
                                    <span class="summary-label">Seller đã nhận:</span>
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
                                    <span class="summary-label">Hoa hồng sàn:</span>
                                    <span class="summary-value text-warning">
                                        {{ number_format($expectedCommission, 0, ',', '.') }}₫
                                        <small class="text-muted">({{ number_format($commissionRate, 1) }}%)</small>
                                    </span>
                                </div>
                                <div class="summary-item">
                                    <span class="summary-label">Seller đã nhận:</span>
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
                                    <span class="summary-label">Hoa hồng sàn:</span>
                                    <span class="summary-value text-warning">
                                        {{ number_format($expectedCommission, 0, ',', '.') }}₫
                                        <small class="text-muted">({{ number_format($commissionRate, 1) }}%)</small>
                                    </span>
                                </div>
                                <div class="summary-item">
                                    <span class="summary-label">Seller sẽ nhận:</span>
                                    <span class="summary-value text-info">
                                        {{ number_format($expectedSellerAmount, 0, ',', '.') }}₫
                                    </span>
                                </div>
                                @if($sellerEarnings > 0)
                                    <div class="summary-item">
                                        <span class="summary-label">Seller đã nhận:</span>
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
@endsection

@push('styles')
    @vite('resources/assets/admin/css/product-common.css')
    @vite('resources/assets/admin/css/product-show.css')
@endpush


