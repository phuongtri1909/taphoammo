@extends('admin.layouts.sidebar')

@section('title', 'Duyệt sản phẩm')

@section('main-content')
    <div class="category-container">
        <div class="content-card">
            <div class="card-top">
                <h2 class="page-title">Sản phẩm chờ duyệt</h2>
                <span class="badge bg-warning text-dark fs-6">{{ $products->total() }} sản phẩm</span>
            </div>
            <div class="card-content">
                @if ($products->isEmpty())
                    <div class="empty-state">
                        <div class="empty-state-icon">
                            <i class="fas fa-check-circle"></i>
                        </div>
                        <h4>Không có sản phẩm chờ duyệt</h4>
                        <p>Tất cả sản phẩm đã được xử lý</p>
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
                                    <th class="column-small">Seller</th>
                                    <th class="column-small text-center">Biến thể</th>
                                    <th class="column-small text-center">Ngày tạo</th>
                                </tr>
                            </thead>
                            <tbody class="text-center">
                                @foreach ($products as $key => $product)
                                    <tr class="text-center">
                                        <td>{{ $products->firstItem() + $key }}</td>
                                        <td>
                                            <div class="action-buttons-wrapper">
                                                <a href="{{ route('admin.products.review', $product) }}"
                                                    class="action-icon view-icon" title="Xem chi tiết">
                                                    <i class="fas fa-eye"></i>
                                                </a>
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
                                                    <small class="text-muted d-block">{{ Str::limit($product->description, 50) }}</small>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="badge bg-secondary">{{ $product->subCategory->category->name }}</span>
                                            <br>
                                            <small class="text-muted">{{ $product->subCategory->name }}</small>
                                        </td>
                                        <td>
                                            <span class="seller-name">{{ $product->seller->full_name }}</span>
                                        </td>
                                        <td>
                                            <span class="badge bg-info">{{ $product->variants->count() }}</span>
                                        </td>
                                        <td>
                                            {{ $product->created_at->format('d/m/Y H:i') }}
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

