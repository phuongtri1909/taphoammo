@extends('seller.layouts.sidebar')

@section('title', 'Hoàn tiền & Khiếu nại')

@section('main-content')
    <div class="category-container">
        <div class="content-card">
            <div class="card-top">
                <h2 class="page-title">Hoàn tiền & Khiếu nại</h2>
                <div class="d-flex gap-2">
                    <a href="{{ route('seller.refunds.index', ['tab' => 'disputes']) }}" 
                        class="btn {{ request('tab') !== 'refunds' ? 'action-button' : 'back-button' }}">
                        <i class="fas fa-exclamation-triangle"></i> Khiếu nại ({{ $disputes->total() }})
                    </a>
                    <a href="{{ route('seller.refunds.index', ['tab' => 'refunds']) }}" 
                        class="btn {{ request('tab') === 'refunds' ? 'action-button' : 'back-button' }}">
                        <i class="fas fa-undo"></i> Hoàn tiền ({{ $refunds->total() }})
                    </a>
                </div>
            </div>
            <div class="card-content">
                @if(request('tab') === 'refunds')
                    <form action="{{ route('seller.refunds.index', ['tab' => 'refunds']) }}" method="GET" class="filter-form mb-4">
                        <input type="hidden" name="tab" value="refunds">
                        <div class="row g-3 mb-3">
                            <div class="col-md-3">
                                <label class="form-label-custom small">Trạng thái</label>
                                <select name="refund_status" class="custom-select">
                                    <option value="">Tất cả</option>
                                    <option value="pending" {{ request('refund_status') === 'pending' ? 'selected' : '' }}>Chờ xử lý</option>
                                    <option value="completed" {{ request('refund_status') === 'completed' ? 'selected' : '' }}>Hoàn thành</option>
                                    <option value="rejected" {{ request('refund_status') === 'rejected' ? 'selected' : '' }}>Từ chối</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label-custom small">Từ ngày</label>
                                <input type="date" name="refund_date_from" class="custom-input" value="{{ request('refund_date_from') }}">
                            </div>
                            <div class="col-md-2">
                                <label class="form-label-custom small">Đến ngày</label>
                                <input type="date" name="refund_date_to" class="custom-input" value="{{ request('refund_date_to') }}">
                            </div>
                            <div class="col-md-2">
                                <label class="form-label-custom small">Khoảng tiền</label>
                                <div class="d-flex gap-1">
                                    <input type="number" name="refund_amount_min" class="custom-input" placeholder="Từ (₫)" 
                                        value="{{ request('refund_amount_min') }}" min="0" step="1000" style="font-size: 0.8rem;">
                                    <input type="number" name="refund_amount_max" class="custom-input" placeholder="Đến (₫)" 
                                        value="{{ request('refund_amount_max') }}" min="0" step="1000" style="font-size: 0.8rem;">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label-custom small">&nbsp;</label>
                                <div>
                                    <button type="submit" class="btn action-button">
                                        <i class="fas fa-search"></i> Lọc
                                    </button>
                                    <a href="{{ route('seller.refunds.index', ['tab' => 'refunds']) }}" class="btn back-button">
                                        <i class="fas fa-redo"></i> Reset
                                    </a>
                                </div>
                            </div>
                        </div>
                    </form>

                    @if ($refunds->isEmpty())
                        <div class="empty-state">
                            <div class="empty-state-icon">
                                <i class="fas fa-check-circle"></i>
                            </div>
                            <h4>Không có yêu cầu hoàn tiền nào</h4>
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
                                        <th class="column-small">Người mua</th>
                                        <th class="column-small text-center">Số tiền</th>
                                        <th class="column-small text-center">Ngày tạo</th>
                                        <th class="column-small text-center">Trạng thái</th>
                                    </tr>
                                </thead>
                                <tbody class="text-center">
                                    @foreach ($refunds as $key => $refund)
                                        <tr>
                                            <td>{{ $refunds->firstItem() + $key }}</td>
                                            <td>
                                                <div class="action-buttons-wrapper">
                                                    <a href="{{ route('seller.refunds.show', $refund->slug) }}"
                                                        class="action-icon view-icon" title="Xem chi tiết">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                </div>
                                            </td>
                                            <td class="text-start">
                                                <div class="product-info">
                                                    <div class="product-details">
                                                        <a class="item-name color-primary" href="{{ route('seller.orders.show', $refund->order->slug) }}"><strong>#{{ $refund->order->slug }}</strong></a>
                                                        <small class="text-muted d-block">
                                                            {{ $refund->items->count() }} giá trị đã hoàn
                                                        </small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="text-start">
                                                <small>{{ $refund->buyer->full_name }}</small>
                                                <br>
                                                <small class="text-muted">{{ $refund->buyer->email }}</small>
                                            </td>
                                            <td class="text-center">
                                                <span class="font-bold text-danger">
                                                    {{ number_format($refund->total_amount, 0, ',', '.') }}₫
                                                </span>
                                            </td>
                                            <td>
                                                {{ $refund->created_at->format('d/m/Y H:i') }}
                                            </td>
                                            <td>
                                                <span class="status-badge bg-{{ $refund->status->badgeColor() }} text-white">
                                                    {{ $refund->status->label() }}
                                                </span>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="mt-4">
                            {{ $refunds->appends(request()->query())->links('components.paginate') }}
                        </div>
                    @endif
                @else
                    <form action="{{ route('seller.refunds.index', ['tab' => 'disputes']) }}" method="GET" class="filter-form mb-4">
                        <input type="hidden" name="tab" value="disputes">
                        <div class="row g-3 mb-3">
                            <div class="col-md-3">
                                <label class="form-label-custom small">Trạng thái</label>
                                <select name="dispute_status" class="custom-select">
                                    <option value="">Tất cả</option>
                                    <option value="open" {{ request('dispute_status') === 'open' ? 'selected' : '' }}>Đang mở</option>
                                    <option value="reviewing" {{ request('dispute_status') === 'reviewing' ? 'selected' : '' }}>Đang xem xét</option>
                                    <option value="approved" {{ request('dispute_status') === 'approved' ? 'selected' : '' }}>Đã chấp nhận</option>
                                    <option value="rejected" {{ request('dispute_status') === 'rejected' ? 'selected' : '' }}>Đã từ chối</option>
                                    <option value="withdrawn" {{ request('dispute_status') === 'withdrawn' ? 'selected' : '' }}>Đã rút</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label-custom small">Từ ngày</label>
                                <input type="date" name="dispute_date_from" class="custom-input" value="{{ request('dispute_date_from') }}">
                            </div>
                            <div class="col-md-2">
                                <label class="form-label-custom small">Đến ngày</label>
                                <input type="date" name="dispute_date_to" class="custom-input" value="{{ request('dispute_date_to') }}">
                            </div>
                            <div class="col-md-2">
                                <label class="form-label-custom small">Tìm kiếm</label>
                                <input type="text" name="dispute_search" class="custom-input" placeholder="Đơn hàng, người mua, sản phẩm..." 
                                    value="{{ request('dispute_search') }}">
                            </div>
                            <div class="col-md-2">
                                <label class="form-label-custom small">Khoảng tiền đơn hàng</label>
                                <div class="d-flex gap-1">
                                    <input type="number" name="dispute_amount_min" class="custom-input" placeholder="Từ (₫)" 
                                        value="{{ request('dispute_amount_min') }}" min="0" step="1000" style="font-size: 0.8rem;">
                                    <input type="number" name="dispute_amount_max" class="custom-input" placeholder="Đến (₫)" 
                                        value="{{ request('dispute_amount_max') }}" min="0" step="1000" style="font-size: 0.8rem;">
                                </div>
                            </div>
                            <div class="col-md-1">
                                <label class="form-label-custom small">&nbsp;</label>
                                <div>
                                    <button type="submit" class="btn action-button w-100">
                                        <i class="fas fa-search"></i> Lọc
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <a href="{{ route('seller.refunds.index', ['tab' => 'disputes']) }}" class="btn back-button btn-sm">
                                    <i class="fas fa-redo"></i> Reset
                                </a>
                            </div>
                        </div>
                    </form>

                    @if ($disputes->isEmpty())
                        <div class="empty-state">
                            <div class="empty-state-icon">
                                <i class="fas fa-check-circle"></i>
                            </div>
                            <h4>Không có khiếu nại nào</h4>
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
                                            <th class="column-small text-center">Ngày tạo</th>
                                            <th class="column-small text-center">Trạng thái</th>
                                        </tr>
                                    </thead>
                                    <tbody class="text-center">
                                        @foreach ($disputes as $key => $dispute)
                                            <tr>
                                                <td>{{ $disputes->firstItem() + $key }}</td>
                                                <td>
                                                    <div class="action-buttons-wrapper">
                                                        <a href="{{ route('seller.refunds.dispute.show', $dispute->slug) }}"
                                                            class="action-icon view-icon" title="Xem chi tiết">
                                                            <i class="fas fa-eye"></i>
                                                        </a>
                                                    </div>
                                                </td>
                                                <td class="text-start">
                                                    <div class="product-info">
                                                        <div class="product-details">
                                                            <span class="item-name">#{{ $dispute->order->slug }}</span>
                                                            <small class="text-muted d-block">
                                                                {{ number_format($dispute->order->total_amount, 0, ',', '.') }}₫
                                                            </small>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td class="text-start">
                                                    <a href="{{ route('seller.products.show', $dispute->orderItem->productVariant->product->slug) }}" class="item-name">{{ $dispute->orderItem->productVariant->product->name }}</a>
                                                    <small class="text-muted d-block">
                                                        {{ $dispute->orderItem->productVariant->name }}
                                                    </small>
                                                </td>
                                                <td class="text-start">
                                                    <small>{{ $dispute->buyer->full_name }}</small>
                                                    <br>
                                                    <small class="text-muted">{{ $dispute->buyer->email }}</small>
                                                </td>
                                                <td>
                                                    {{ $dispute->created_at->format('d/m/Y H:i') }}
                                                </td>
                                                <td>
                                                    <span class="status-badge bg-{{ $dispute->status->badgeColor() }} text-white">
                                                        {{ $dispute->status->label() }}
                                                    </span>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>

                        <div class="mt-4">
                            {{ $disputes->appends(request()->query())->links('components.paginate') }}
                        </div>
                    @endif
                @endif
            </div>
        </div>
    </div>
@endsection

@push('styles')
    @vite('resources/assets/admin/css/product-common.css')
@endpush

