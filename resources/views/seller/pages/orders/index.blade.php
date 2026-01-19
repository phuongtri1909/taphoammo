@extends('seller.layouts.sidebar')

@section('title', 'Đơn hàng của tôi')

@section('main-content')
    <div class="category-container">
        <div class="content-card">
            <div class="card-top">
                <h2 class="page-title">Đơn hàng đã bán</h2>
            </div>
            <div class="card-content">
                <form action="{{ route('seller.orders.index') }}" method="GET" class="filter-form mb-4">
                    <div class="row g-3 mb-3">
                        <div class="col-md-3">
                            <label class="form-label-custom small">Tìm kiếm</label>
                            <input type="text" name="search" class="custom-input" placeholder="Order, buyer..."
                                value="{{ request('search') }}">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label-custom small">Trạng thái</label>
                            <select name="status" class="custom-select">
                                <option value="">Tất cả</option>
                                <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Chờ thanh toán</option>
                                <option value="paid" {{ request('status') === 'paid' ? 'selected' : '' }}>Đã thanh toán</option>
                                <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Hoàn thành</option>
                                <option value="disputed" {{ request('status') === 'disputed' ? 'selected' : '' }}>Đang tranh chấp</option>
                                <option value="refunded" {{ request('status') === 'refunded' ? 'selected' : '' }}>Đã hoàn tiền</option>
                                <option value="partial_refunded" {{ request('status') === 'partial_refunded' ? 'selected' : '' }}>Hoàn tiền một phần</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label-custom small">Từ ngày</label>
                            <input type="date" name="date_from" class="custom-input" value="{{ request('date_from') }}">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label-custom small">Đến ngày</label>
                            <input type="date" name="date_to" class="custom-input" value="{{ request('date_to') }}">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label-custom small">Khoảng tiền</label>
                            <div class="d-flex gap-2">
                                <input type="number" name="amount_min" class="custom-input" placeholder="Từ" 
                                    value="{{ request('amount_min') }}" min="0" step="1000">
                                <input type="number" name="amount_max" class="custom-input" placeholder="Đến" 
                                    value="{{ request('amount_max') }}" min="0" step="1000">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12">
                            <button type="submit" class="btn action-button">
                                <i class="fas fa-search"></i> Lọc
                            </button>
                            <a href="{{ route('seller.orders.index') }}" class="btn back-button">
                                <i class="fas fa-redo"></i> Reset
                            </a>
                            <span class="text-muted ms-2" style="font-size: 0.85rem;">
                                Tìm thấy: <strong>{{ $orders->total() }}</strong> đơn hàng
                            </span>
                        </div>
                    </div>
                </form>

                @if ($orders->isEmpty())
                    <div class="empty-state">
                        <div class="empty-state-icon">
                            <i class="fas fa-shopping-cart"></i>
                        </div>
                        <h4>Không có đơn hàng nào</h4>
                        <p>Thử thay đổi bộ lọc để tìm kiếm</p>
                    </div>
                @else
                    <div class="data-table-container">
                        <table class="data-table">
                            <thead>
                                <tr class="text-center">
                                    <th class="column-stt text-center">STT</th>
                                    <th class="column-small text-center">Thao tác</th>
                                    <th class="column-medium">Đơn hàng</th>
                                    <th class="column-medium">Sản phẩm</th>
                                    <th class="column-small">Người mua</th>
                                    <th class="column-small text-center">Số tiền</th>
                                    <th class="column-small text-center">Ngày tạo</th>
                                    <th class="column-small text-center">Trạng thái</th>
                                </tr>
                            </thead>
                            <tbody class="text-center">
                                @foreach ($orders as $key => $order)
                                    <tr>
                                        <td>{{ $orders->firstItem() + $key }}</td>
                                        <td>
                                            <div class="action-buttons-wrapper">
                                                <a href="{{ route('seller.orders.show', $order->slug) }}"
                                                    class="action-icon view-icon" title="Xem chi tiết">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                            </div>
                                        </td>
                                        <td class="text-start">
                                            <div class="product-info">
                                                <div class="product-details">
                                                    <span class="item-name">#{{ $order->slug }}</span>
                                                    <small class="text-muted d-block">
                                                        {{ $order->items->count() }} sản phẩm
                                                    </small>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="text-start">
                                            @foreach ($order->items as $item)
                                                <a class="text-primary" href="{{ route('seller.products.show', $item->productVariant->product->slug) }}">{{ $item->productVariant->product->name }}</a>
                                                <br>
                                                <small class="text-muted">{{ $item->productVariant->name }} × {{ $item->quantity }}</small>
                                            @endforeach
                                        </td>
                                        <td class="text-start">
                                            <small>{{ $order->buyer->full_name }}</small>
                                            <br>
                                            <small class="text-muted">{{ $order->buyer->email }}</small>
                                        </td>
                                        <td class="text-center">
                                            <span class="font-bold text-primary">
                                                {{ number_format($order->total_amount, 0, ',', '.') }}₫
                                            </span>
                                        </td>
                                        <td>
                                            {{ $order->created_at->format('d/m/Y H:i') }}
                                        </td>
                                        <td>
                                            <span class="status-badge bg-{{ $order->status->badgeColor() }} text-white">
                                                {{ $order->status->label() }}
                                            </span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4">
                        {{ $orders->appends(request()->query())->links('components.paginate') }}
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection

@push('styles')
    @vite('resources/assets/admin/css/product-common.css')
@endpush


