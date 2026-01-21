@extends('seller.layouts.sidebar')

@section('title', 'Sản phẩm của tôi')

@section('main-content')
    <div class="category-container">
        <div class="content-card">
            <div class="card-top">
                <h2 class="page-title">Sản phẩm của tôi</h2>
                <a href="{{ route('seller.products.create') }}" class="action-button">
                    <i class="fas fa-plus"></i> Thêm sản phẩm
                </a>
            </div>
            <div class="card-content">
                <!-- Filters -->
                <form action="{{ route('seller.products.index') }}" method="GET" class="filter-form mb-4">
                    <div class="row g-3">
                        <div class="col-md-4">
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
                        <div class="col-md-4">
                            <button type="submit" class="btn action-button">
                                <i class="fas fa-search"></i> Lọc
                            </button>
                            <a href="{{ route('seller.products.index') }}" class="btn back-button">
                                <i class="fas fa-redo"></i> Reset
                            </a>
                        </div>
                    </div>
                </form>

                @if ($products->isEmpty())
                    <div class="empty-state">
                        <div class="empty-state-icon">
                            <i class="fas fa-box-open"></i>
                        </div>
                        <h4>Bạn chưa có sản phẩm nào</h4>
                        <p>Tạo sản phẩm đầu tiên để bắt đầu bán hàng</p>
                        <a href="{{ route('seller.products.create') }}" class="action-button">
                            <i class="fas fa-plus"></i> Thêm sản phẩm
                        </a>
                    </div>
                @else
                    <div class="data-table-container">
                        <table class="data-table">
                            <thead>
                                <tr class="text-center">
                                    <th class="column-stt text-center">STT</th>
                                    <th class="column-small text-center">Thao tác</th>
                                    <th class="column-medium">Sản phẩm</th>
                                    <th class="column-medium">Danh mục</th>
                                    <th class="column-small text-center">Biến thể</th>
                                    <th class="column-small text-center">Tồn kho</th>
                                    <th class="column-small text-center">Trạng thái</th>
                                </tr>
                            </thead>
                            <tbody class="text-center">
                                @foreach ($products as $key => $product)
                                    <tr class="text-center">
                                        <td>{{ $products->firstItem() + $key }}</td>
                                        <td>
                                            <div class="action-buttons-wrapper">
                                                @if (in_array($product->status, [\App\Enums\ProductStatus::PENDING, \App\Enums\ProductStatus::REJECTED]))
                                                    <a href="{{ route('seller.products.edit', $product) }}"
                                                        class="action-icon edit-icon" title="Chỉnh sửa">
                                                        <i class="fas fa-pencil-alt"></i>
                                                    </a>
                                                @endif
                                                @if (!$product->hasOrders())
                                                    @include('components.delete-form', [
                                                        'id' => $product->slug,
                                                        'route' => route('seller.products.destroy', $product),
                                                        'message' => "Bạn có chắc chắn muốn xóa sản phẩm '{$product->name}'?",
                                                    ])
                                                @endif
                                                @if ($product->status === \App\Enums\ProductStatus::APPROVED)
                                                    <a href="{{ route('seller.products.show', $product) }}"
                                                        class="action-icon view-icon" title="Quản lý">
                                                        <i class="fas fa-cog"></i>
                                                    </a>
                                                @else
                                                    <a href="{{ route('seller.products.show', $product) }}"
                                                        class="action-icon view-icon" title="Xem">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                @endif
                                            </div>
                                        </td>
                                        <td class="text-start">
                                            <div class="product-info">
                                                @if ($product->image)
                                                    <img src="{{ Storage::url($product->image) }}"
                                                        alt="{{ $product->name }}" class="product-thumb">
                                                @else
                                                    <div class="product-thumb-placeholder">
                                                        <i class="fas fa-image"></i>
                                                    </div>
                                                @endif
                                                <div class="product-details">
                                                    <span class="item-name">{{ $product->name }}</span>
                                                    <small
                                                        class="text-muted d-block">{{ Str::limit($product->description, 50) }}</small>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <span
                                                class="badge bg-secondary">{{ $product->subCategory->category->name }}</span>
                                            <br>
                                            <small class="text-muted">{{ $product->subCategory->name }}</small>
                                        </td>
                                        <td>
                                            <span class="badge bg-info">{{ $product->variants->count() }}</span>
                                        </td>
                                        <td>
                                            @php
                                                $totalStock = $product->variants->sum('stock_quantity');
                                            @endphp
                                            <span class="badge bg-{{ $totalStock > 0 ? 'success' : 'secondary' }}">
                                                {{ $totalStock }}
                                            </span>
                                        </td>
                                        <td>
                                            <span class="status-badge {{ $product->status->value }}">
                                                {{ $product->status->label() }}
                                            </span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4">
                        {{ $products->appends(request()->query())->links('components.paginate') }}
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection

@push('styles')
    @vite('resources/assets/admin/css/product-common.css')
@endpush
