@extends('admin.layouts.sidebar')

@section('title', 'Quản lý người bán')

@section('main-content')
    <div class="category-container">
        <div class="content-card">
            <div class="card-top">
                <h2 class="page-title">Danh sách người bán</h2>
                <a href="{{ route('admin.seller-registrations.index') }}" class="action-button">
                    <i class="fas fa-user-plus"></i> Đăng ký chờ duyệt
                    @if(isset($pendingCount) && $pendingCount > 0)
                        <span class="badge bg-warning text-dark ms-2">{{ $pendingCount }}</span>
                    @endif
                </a>
            </div>
            <div class="card-content">
                <!-- Filters -->
                <form action="{{ route('admin.sellers.index') }}" method="GET" class="filter-form mb-4">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <input type="text" name="search" class="custom-input" placeholder="Tìm kiếm theo tên, email..."
                                value="{{ request('search') }}">
                        </div>
                        <div class="col-md-3">
                            <select name="status" class="custom-select">
                                <option value="">Tất cả trạng thái</option>
                                <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Đang hoạt động</option>
                                <option value="banned" {{ request('status') === 'banned' ? 'selected' : '' }}>Đã khóa</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <button type="submit" class="btn action-button">
                                <i class="fas fa-search"></i> Lọc
                            </button>
                            <a href="{{ route('admin.sellers.index') }}" class="btn back-button">
                                <i class="fas fa-redo"></i> Reset
                            </a>
                        </div>
                    </div>
                </form>

                @if ($sellers->isEmpty())
                    <div class="empty-state">
                        <div class="empty-state-icon">
                            <i class="fas fa-store-slash"></i>
                        </div>
                        <h4>Không tìm thấy người bán</h4>
                        <p>Thử thay đổi bộ lọc để tìm kiếm</p>
                    </div>
                @else
                    <div class="data-table-container">
                        <table class="data-table">
                            <thead>
                                <tr class="text-center">
                                    <th class="column-stt text-center">STT</th>
                                    <th class="column-small text-center">Thao tác</th>
                                    <th class="column-medium">Thông tin</th>
                                    <th class="column-small text-center">Sản phẩm</th>
                                    <th class="column-small text-center">Ngày tham gia</th>
                                    <th class="column-small text-center">Trạng thái</th>
                                </tr>
                            </thead>
                            <tbody class="text-center">
                                @foreach ($sellers as $key => $seller)
                                    <tr class="text-center">
                                        <td>{{ $sellers->firstItem() + $key }}</td>
                                        <td>
                                            <div class="action-buttons-wrapper">
                                                <a href="{{ route('admin.sellers.show', $seller->full_name) }}"
                                                    class="action-icon view-icon" title="Xem chi tiết">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                            </div>
                                        </td>
                                        <td class="text-start">
                                            <div class="product-info">
                                                <div class="product-details">
                                                    <span class="item-name">{{ $seller->full_name }}</span>
                                                    <small class="text-muted d-block">{{ $seller->email }}</small>
                                                    @if($seller->sellerRegistration)
                                                        <small class="text-muted d-block">
                                                            <i class="fas fa-phone"></i> {{ $seller->sellerRegistration->phone }}
                                                        </small>
                                                    @endif
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="badge bg-info">{{ $seller->products_count }}</span>
                                        </td>
                                        <td>
                                            {{ $seller->created_at->format('d/m/Y') }}
                                        </td>
                                        <td>
                                            @if($seller->is_seller_banned)
                                                <span class="status-badge banned">
                                                    <i class="fas fa-ban"></i> Đã khóa
                                                </span>
                                            @else
                                                <span class="status-badge active">
                                                    <i class="fas fa-check-circle"></i> Hoạt động
                                                </span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4">
                        {{ $sellers->appends(request()->query())->links('components.paginate') }}
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection

@push('styles')
    @vite('resources/assets/admin/css/product-common.css')
@endpush

