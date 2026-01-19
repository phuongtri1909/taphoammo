@extends('admin.layouts.sidebar')

@section('title', 'Quản lý hoàn tiền')

@section('main-content')
    <div class="category-container">
        <div class="content-card">
            <div class="card-top">
                <h2 class="page-title">Danh sách hoàn tiền</h2>
            </div>
            <div class="card-content">
                <!-- Filters -->
                <form action="{{ route('admin.refunds.index') }}" method="GET" class="filter-form mb-4">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <input type="text" name="search" class="custom-input" placeholder="Tìm kiếm theo order, buyer..."
                                value="{{ request('search') }}">
                        </div>
                        <div class="col-md-3">
                            <select name="status" class="custom-select">
                                <option value="">Tất cả trạng thái</option>
                                <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Chờ xử lý</option>
                                <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Hoàn thành</option>
                                <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>Từ chối</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <button type="submit" class="btn action-button">
                                <i class="fas fa-search"></i> Lọc
                            </button>
                            <a href="{{ route('admin.refunds.index') }}" class="btn back-button">
                                <i class="fas fa-redo"></i> Reset
                            </a>
                        </div>
                    </div>
                </form>

                @if ($refunds->isEmpty())
                    <div class="empty-state">
                        <div class="empty-state-icon">
                            <i class="fas fa-check-circle"></i>
                        </div>
                        <h4>Không có yêu cầu hoàn tiền nào</h4>
                        <p>Tất cả yêu cầu đã được xử lý</p>
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
                                                <a href="{{ route('admin.refunds.show', $refund->slug) }}"
                                                    class="action-icon view-icon" title="Xem chi tiết">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                            </div>
                                        </td>
                                        <td class="text-start">
                                            <div class="product-info">
                                                <div class="product-details">
                                                    <a class="item-name color-primary" href="{{ route('admin.orders.show', $refund->order->slug) }}"><strong>#{{ $refund->order->slug }}</strong></a>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="text-start">
                                            <small>{{ $refund->buyer->full_name }}</small>
                                            <br>
                                            <small class="text-muted">{{ $refund->buyer->email }}</small>
                                        </td>
                                        <td class="text-center">
                                            <span class="font-bold text-primary">
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
            </div>
        </div>
    </div>
@endsection

@push('styles')
    @vite('resources/assets/admin/css/product-common.css')
@endpush


