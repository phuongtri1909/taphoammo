@extends('admin.layouts.sidebar')

@section('title', 'Lịch sử đề xuất')

@section('main-content')
    <div class="category-container">
        <div class="content-card">
            <div class="card-top">
                <h2 class="page-title">Lịch sử đề xuất</h2>
                <div class="d-flex gap-2">
                    <span class="badge bg-success">Tổng doanh thu: {{ number_format($totalRevenue, 0, ',', '.') }} VNĐ</span>
                    <span class="badge bg-info">Đang hoạt động: {{ $totalActive }}</span>
                    <span class="badge bg-primary">Sản phẩm: {{ $totalProduct }}</span>
                    <span class="badge bg-warning text-dark">Dịch vụ: {{ $totalService }}</span>
                </div>
            </div>
            <div class="card-content">
                <form action="{{ route('admin.featured-histories.index') }}" method="GET" class="filter-form mb-4">
                    <div class="row g-3">
                        <div class="col-md-3">
                            <label class="form-label-custom small">Tìm kiếm</label>
                            <input type="text" name="search" class="custom-input" placeholder="Tên sản phẩm/dịch vụ..."
                                value="{{ request('search') }}">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label-custom small">Người bán</label>
                            <input type="text" name="seller" class="custom-input" placeholder="Tên/Email..."
                                value="{{ request('seller') }}">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label-custom small">Loại</label>
                            <select name="type" class="custom-select">
                                <option value="">Tất cả</option>
                                <option value="product" {{ request('type') === 'product' ? 'selected' : '' }}>Sản phẩm</option>
                                <option value="service" {{ request('type') === 'service' ? 'selected' : '' }}>Dịch vụ</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label-custom small">Trạng thái</label>
                            <select name="status" class="custom-select">
                                <option value="">Tất cả</option>
                                <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Đang hoạt động</option>
                                <option value="expired" {{ request('status') === 'expired' ? 'selected' : '' }}>Hết hạn</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label-custom small">Từ ngày</label>
                            <input type="date" name="from_date" class="custom-input" value="{{ request('from_date') }}">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label-custom small">Đến ngày</label>
                            <input type="date" name="to_date" class="custom-input" value="{{ request('to_date') }}">
                        </div>
                        <div class="col-md-12">
                            <button type="submit" class="btn action-button">
                                <i class="fas fa-search"></i> Lọc
                            </button>
                            <a href="{{ route('admin.featured-histories.index') }}" class="btn back-button">
                                <i class="fas fa-redo"></i> Reset
                            </a>
                        </div>
                    </div>
                </form>

                @if ($featuredHistories->isEmpty())
                    <div class="empty-state">
                        <div class="empty-state-icon">
                            <i class="fas fa-star"></i>
                        </div>
                        <h4>Chưa có lịch sử đề xuất</h4>
                        <p>Chưa có seller nào mua gói đề xuất.</p>
                    </div>
                @else
                    <div class="data-table-container">
                        <table class="data-table">
                            <thead>
                                <tr class="text-center">
                                    <th class="column-stt text-center">STT</th>
                                    <th class="column-medium">Mã</th>
                                    <th class="column-medium">Người bán</th>
                                    <th class="column-small text-center">Loại</th>
                                    <th class="column-large">Sản phẩm/Dịch vụ</th>
                                    <th class="column-small text-end">Số tiền</th>
                                    <th class="column-small text-center">Thời gian</th>
                                    <th class="column-medium text-center">Trạng thái</th>
                                    <th class="column-small text-center">Ngày tạo</th>
                                    <th class="column-small text-center">Thao tác</th>
                                </tr>
                            </thead>
                            <tbody class="text-center">
                                @foreach($featuredHistories as $index => $history)
                                    <tr>
                                        <td>{{ $featuredHistories->firstItem() + $index }}</td>
                                        <td>
                                            <span class="text-primary fw-bold">{{ $history->slug }}</span>
                                        </td>
                                        <td class="text-start">
                                            @if ($history->seller)
                                                <a href="{{ route('admin.sellers.show', $history->seller->full_name) }}"
                                                    class="item-name color-primary">
                                                    {{ $history->seller->full_name }}
                                                </a>
                                                <br>
                                                <small class="text-muted">{{ $history->seller->email }}</small>
                                            @else
                                                <span class="text-muted">N/A</span>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="status-badge bg-{{ $history->featurable_type_badge }} text-white">
                                                {{ $history->featurable_type_label }}
                                            </span>
                                        </td>
                                        <td class="text-start">
                                            @if ($history->featurable)
                                                <a href="{{ $history->featurable_type === 'App\\Models\\Product'
                                                    ? route('admin.products.show', $history->featurable->slug)
                                                    : route('admin.services.show', $history->featurable->slug) }}"
                                                    class="item-name color-primary" target="_blank">
                                                    {{ Str::limit($history->featurable->name, 40) }}
                                                </a>
                                            @else
                                                <span class="text-muted">Đã xóa</span>
                                            @endif
                                        </td>
                                        <td class="text-end">
                                            <span class="fw-bold text-success">{{ $history->formatted_amount }}</span>
                                        </td>
                                        <td>{{ $history->hours }} giờ</td>
                                        <td>
                                            @if ($history->isActive())
                                                <span class="status-badge bg-success text-white">Đang hoạt động</span>
                                                <br>
                                                <small class="text-muted">
                                                    Còn {{ $history->featured_until->diffForHumans(null, true, false, 2) }}
                                                </small>
                                            @else
                                                <span class="status-badge bg-secondary text-white">Hết hạn</span>
                                            @endif
                                        </td>
                                        <td>
                                            <span title="{{ $history->created_at->format('d/m/Y H:i:s') }}">
                                                {{ $history->created_at->format('d/m/Y') }}
                                            </span>
                                        </td>
                                        <td>
                                            <a href="{{ route('admin.featured-histories.show', $history->slug) }}"
                                                class="btn btn-sm action-button" title="Xem chi tiết">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="pagination-container">
                        {{ $featuredHistories->links('components.paginate') }}
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
