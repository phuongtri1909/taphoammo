@extends('admin.layouts.sidebar')

@section('title', 'Quản lý dịch vụ')

@section('main-content')
    <div class="category-container">
        <div class="content-card">
            <div class="card-top">
                <h2 class="page-title">Quản lý dịch vụ</h2>
            </div>
            <div class="card-content">
                <!-- Filters -->
                <form action="{{ route('admin.services.index') }}" method="GET" class="filter-form mb-4">
                    <div class="row g-3">
                        <div class="col-md-3">
                            <input type="text" name="search" class="custom-input" placeholder="Tìm kiếm dịch vụ..."
                                value="{{ request('search') }}">
                        </div>
                        <div class="col-md-3">
                            <select name="category_id" class="custom-select">
                                <option value="">Tất cả danh mục</option>
                                @foreach ($categories as $category)
                                    <option value="{{ $category->id }}"
                                        {{ request('category_id') == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <select name="status" class="custom-select">
                                <option value="">Tất cả trạng thái</option>
                                @foreach ($statuses as $status)
                                    <option value="{{ $status->value }}"
                                        {{ request('status') == $status->value ? 'selected' : '' }}>
                                        {{ $status->label() }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <button type="submit" class="btn action-button">
                                <i class="fas fa-search"></i> Lọc
                            </button>
                            <a href="{{ route('admin.services.index') }}" class="btn back-button">
                                <i class="fas fa-redo"></i> Reset
                            </a>
                        </div>
                    </div>
                </form>

                @if ($services->isEmpty())
                    <div class="empty-state">
                        <div class="empty-state-icon">
                            <i class="fas fa-concierge-bell"></i>
                        </div>
                        <h4>Không tìm thấy dịch vụ</h4>
                        <p>Thử thay đổi bộ lọc để tìm kiếm dịch vụ</p>
                    </div>
                @else
                    <div class="data-table-container">
                        <table class="data-table">
                            <thead>
                                <tr class="text-center">
                                    <th class="column-stt text-center">STT</th>
                                    <th class="column-small text-center">Thao tác</th>
                                    <th class="column-medium">Dịch vụ</th>
                                    <th class="column-medium">Danh mục</th>
                                    <th class="column-small">Seller</th>
                                    <th class="column-small text-center">Biến thể</th>
                                    <th class="column-small text-center">Trạng thái</th>
                                </tr>
                            </thead>
                            <tbody class="text-center">
                                @foreach ($services as $key => $service)
                                    <tr class="text-center">
                                        <td>{{ $services->firstItem() + $key }}</td>
                                        <td>
                                            <div class="action-buttons-wrapper">
                                                <a href="{{ route('admin.services.show', $service) }}"
                                                    class="action-icon view-icon" title="Xem chi tiết">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                            </div>
                                        </td>
                                        <td class="text-start">
                                            <div class="product-info">
                                                @if ($service->image)
                                                    <img src="{{ Storage::url($service->image) }}"
                                                        alt="{{ $service->name }}" class="product-thumb">
                                                @else
                                                    <div class="product-thumb-placeholder">
                                                        <i class="fas fa-concierge-bell"></i>
                                                    </div>
                                                @endif
                                                <div class="product-details">
                                                    <span class="item-name">{{ $service->name }}</span>
                                                    <small
                                                        class="text-muted d-block">{{ Str::limit($service->description, 50) }}</small>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="badge bg-secondary">{{ $service->serviceSubCategory->serviceCategory->name ?? 'N/A' }}</span>
                                            <br>
                                            <small class="text-muted">{{ $service->serviceSubCategory->name ?? 'N/A' }}</small>
                                        </td>
                                        <td>
                                            <span class="seller-name">{{ $service->seller->full_name }}</span>
                                        </td>
                                        <td>
                                            <span class="badge bg-info">{{ $service->variants->count() }}</span>
                                        </td>
                                        <td>
                                            <span class="status-badge {{ $service->status->value }}">
                                                {{ $service->status->label() }}
                                            </span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4">
                        {{ $services->appends(request()->query())->links('components.paginate') }}
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection

@push('styles')
    @vite('resources/assets/admin/css/product-common.css')
@endpush
