@extends('admin.layouts.sidebar')

@section('title', 'Quản lý tranh chấp dịch vụ')

@section('main-content')
    <div class="category-container">
        <div class="content-card">
            <div class="card-top">
                <h2 class="page-title">Danh sách tranh chấp dịch vụ</h2>
            </div>
            <div class="card-content">
                <!-- Filters -->
                <form action="{{ route('admin.service-disputes.index') }}" method="GET" class="filter-form mb-4">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <input type="text" name="search" class="custom-input" placeholder="Tìm kiếm theo đơn hàng, buyer, seller..."
                                value="{{ request('search') }}">
                        </div>
                        <div class="col-md-3">
                            <select name="status" class="custom-select">
                                <option value="">Tất cả trạng thái</option>
                                <option value="open" {{ request('status') === 'open' ? 'selected' : '' }}>Mở</option>
                                <option value="reviewing" {{ request('status') === 'reviewing' ? 'selected' : '' }}>Đang chờ Admin</option>
                                <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>Chấp nhận</option>
                                <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>Từ chối</option>
                                <option value="withdrawn" {{ request('status') === 'withdrawn' ? 'selected' : '' }}>Đã rút</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <button type="submit" class="btn action-button">
                                <i class="fas fa-search"></i> Lọc
                            </button>
                            <a href="{{ route('admin.service-disputes.index') }}" class="btn back-button">
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
                        <h4>Không có tranh chấp nào</h4>
                        <p>Tất cả tranh chấp đã được xử lý</p>
                    </div>
                @else
                    <div class="data-table-container">
                        <table class="data-table">
                            <thead>
                                <tr class="text-center">
                                    <th class="column-stt text-center">STT</th>
                                    <th class="column-small text-center">Thao tác</th>
                                    <th class="column-medium">Đơn hàng</th>
                                    <th class="column-medium">Dịch vụ</th>
                                    <th class="column-small">Người mua</th>
                                    <th class="column-small">Người bán</th>
                                    <th class="column-small text-center">Ngày tạo</th>
                                    <th class="column-small text-center">Trạng thái</th>
                                </tr>
                            </thead>
                            <tbody class="text-center">
                                @foreach ($disputes as $key => $dispute)
                                    <tr class="{{ $dispute->status === \App\Enums\ServiceDisputeStatus::REVIEWING ? 'table-warning' : '' }}">
                                        <td>{{ $disputes->firstItem() + $key }}</td>
                                        <td>
                                            <div class="action-buttons-wrapper">
                                                <a href="{{ route('admin.service-disputes.show', $dispute->slug) }}"
                                                    class="action-icon view-icon" title="Xem chi tiết">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                            </div>
                                        </td>
                                        <td class="text-start">
                                            <div class="product-info">
                                                <div class="product-details">
                                                    <a class="item-name color-primary" href="{{ route('admin.service-orders.show', $dispute->serviceOrder->slug) }}">
                                                        <strong>#{{ $dispute->serviceOrder->slug }}</strong>
                                                    </a>
                                                    <small class="text-muted d-block">
                                                        {{ number_format($dispute->serviceOrder->total_amount, 0, ',', '.') }}₫
                                                    </small>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="text-start">
                                            @if($dispute->serviceOrder->serviceVariant && $dispute->serviceOrder->serviceVariant->service)
                                                <span class="item-name">{{ $dispute->serviceOrder->serviceVariant->service->name }}</span>
                                                <small class="text-muted d-block">
                                                    {{ $dispute->serviceOrder->serviceVariant->name }}
                                                </small>
                                            @else
                                                <span class="text-muted">N/A</span>
                                            @endif
                                        </td>
                                        <td class="text-start">
                                            <small>{{ $dispute->serviceOrder->buyer->full_name }}</small>
                                            <br>
                                            <small class="text-muted">{{ $dispute->serviceOrder->buyer->email }}</small>
                                        </td>
                                        <td class="text-start">
                                            <small>{{ $dispute->serviceOrder->seller->full_name }}</small>
                                            <br>
                                            <small class="text-muted">{{ $dispute->serviceOrder->seller->email }}</small>
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
            </div>
        </div>
    </div>
@endsection

@push('styles')
    @vite('resources/assets/admin/css/product-common.css')
@endpush
