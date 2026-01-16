@extends('admin.layouts.sidebar')

@section('title', 'Chi tiết sản phẩm - ' . $product->name)

@section('main-content')
    <div class="category-container">
        <div class="mb-4">
            <a href="{{ route('admin.products.index') }}" class="btn back-button">
                <i class="fas fa-arrow-left"></i> Quay lại danh sách
            </a>
        </div>

        @if ($product->admin_note)
            <div class="alert alert-{{ $product->status === \App\Enums\ProductStatus::REJECTED || $product->status === \App\Enums\ProductStatus::BANNED ? 'danger' : 'info' }} mb-4">
                <h5><i class="fas fa-sticky-note"></i> Ghi chú Admin:</h5>
                <p class="mb-0">{{ $product->admin_note }}</p>
            </div>
        @endif

        <div class="row">
            <!-- Product Info -->
            <div class="col-lg-8">
                <!-- Product Header -->
                <div class="product-show-header mb-4">
                    <div class="product-header-content">
                        <div class="product-image-wrapper">
                            @if ($product->image)
                                <img src="{{ Storage::url($product->image) }}" alt="{{ $product->name }}">
                            @else
                                <div class="image-placeholder">
                                    <i class="fas fa-image"></i>
                                </div>
                            @endif
                        </div>
                        <div class="product-info-wrapper">
                            <h1 class="product-title">{{ $product->name }}</h1>
                            <div class="product-meta">
                                <div class="product-meta-item">
                                    <i class="fas fa-folder"></i>
                                    <span>{{ $product->subCategory->category->name }} > {{ $product->subCategory->name }}</span>
                                </div>
                                <div class="product-meta-item">
                                    <i class="fas fa-user"></i>
                                    <span>Seller: <strong>{{ $product->seller->full_name }}</strong> ({{ $product->seller->email }})</span>
                                </div>
                                <div class="product-meta-item">
                                    <i class="fas fa-clock"></i>
                                    <span>{{ $product->created_at->format('d/m/Y H:i') }}</span>
                                </div>
                            </div>
                            <span class="product-status-badge status-badge {{ $product->status->value }}">
                                {{ $product->status->label() }}
                            </span>
                        </div>
                    </div>
                </div>

                @if ($product->description || $product->long_description || in_array($product->status, [\App\Enums\ProductStatus::APPROVED, \App\Enums\ProductStatus::HIDDEN, \App\Enums\ProductStatus::PENDING, \App\Enums\ProductStatus::REJECTED, \App\Enums\ProductStatus::BANNED]))
                    <div class="product-info-card mb-4">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-info-circle"></i>
                                Thông tin sản phẩm
                            </h3>
                        </div>
                        <div class="card-body">
                            @if ($product->description)
                                <div class="product-description-section mb-3">
                                    <h5><i class="fas fa-align-left"></i> Mô tả ngắn</h5>
                                    <p class="mb-0">{{ $product->description }}</p>
                                </div>
                            @endif

                            @if ($product->long_description)
                                <div class="product-description-section mb-3">
                                    <h5><i class="fas fa-file-alt"></i> Mô tả chi tiết</h5>
                                    <div class="long-description-display">{!! nl2br(e($product->long_description)) !!}</div>
                                </div>
                            @endif

                            @if (in_array($product->status, [\App\Enums\ProductStatus::APPROVED, \App\Enums\ProductStatus::HIDDEN]))
                                <div class="product-actions">
                                    <form action="{{ route('admin.products.ban', $product) }}" method="POST" class="d-inline">
                                        @csrf
                                        <div class="form-group mb-3">
                                            <label class="form-label-custom">Lý do cấm <span class="required-mark">*</span></label>
                                            <textarea name="admin_note" class="custom-input" rows="3" required placeholder="Nhập lý do cấm sản phẩm..."></textarea>
                                        </div>
                                        <button type="submit" class="btn-modern danger">
                                            <i class="fas fa-ban"></i> Cấm sản phẩm
                                        </button>
                                    </form>
                                </div>
                            @endif

                            @if ($product->status === \App\Enums\ProductStatus::BANNED)
                                <div class="product-actions">
                                    <form action="{{ route('admin.products.unban', $product) }}" method="POST" class="d-inline">
                                        @csrf
                                        <p class="text-muted mb-3">Bỏ cấm sẽ chuyển sản phẩm về trạng thái chờ duyệt.</p>
                                        <button type="submit" class="btn-modern primary">
                                            <i class="fas fa-unlock"></i> Bỏ cấm sản phẩm
                                        </button>
                                    </form>
                                </div>
                            @endif

                            @if ($product->status === \App\Enums\ProductStatus::PENDING)
                                <div class="product-actions">
                                    <a href="{{ route('admin.products.review', $product) }}" class="btn-modern primary">
                                        <i class="fas fa-check-circle"></i> Đi đến trang duyệt
                                    </a>
                                </div>
                            @endif

                            @if ($product->status === \App\Enums\ProductStatus::REJECTED)
                                <div class="alert alert-warning mb-0">
                                    <i class="fas fa-info-circle"></i>
                                    Sản phẩm đã bị từ chối. Seller cần chỉnh sửa và gửi duyệt lại.
                                </div>
                            @endif
                        </div>
                    </div>
                @endif

                <!-- Variants & Values -->
                <div class="variants-accordion">
                    @foreach ($product->variants as $index => $variant)
                        <div class="variant-card-accordion mb-3">
                            <div class="variant-accordion-header">
                                <div class="variant-header-content">
                                    <div class="variant-header-info" data-bs-toggle="collapse" data-bs-target="#variantCollapse{{ $variant->id }}" aria-expanded="{{ $index === 0 ? 'true' : 'false' }}" aria-controls="variantCollapse{{ $variant->id }}">
                                        <h4 class="variant-name">{{ $variant->name }}</h4>
                                        <div class="variant-badges-compact">
                                            <span class="variant-badge price">{{ number_format($variant->price, 0, ',', '.') }} VNĐ</span>
                                            <span class="variant-badge stock">Tồn: {{ $variant->stock_quantity }}</span>
                                            <span class="variant-badge sold">Đã bán: {{ $variant->sold_count }}</span>
                                            <span class="variant-badge status-{{ $variant->status->value }}">
                                                {{ $variant->status->label() }}
                                            </span>
                                        </div>
                                    </div>
                                    <div class="variant-header-actions">
                                        <i class="fas fa-chevron-down accordion-icon"></i>
                                    </div>
                                </div>
                            </div>

                            <div class="collapse {{ $index === 0 ? 'show' : '' }}" id="variantCollapse{{ $variant->id }}">
                                <div class="variant-accordion-body">
                                    <div class="variant-field-name">
                                        <i class="fas fa-key"></i>
                                        <span>Field Name:</span>
                                        <code>{{ $variant->field_name }}</code>
                                    </div>

                                    <!-- Values List -->
                                    @if ($variant->productValues && $variant->productValues->count() > 0)
                                        <div class="values-table-wrapper">
                                            <table class="values-table">
                                                <thead>
                                                    <tr>
                                                        <th style="width: 60px;" class="text-center">STT</th>
                                                        <th>Giá trị</th>
                                                        <th style="width: 150px;" class="text-center">Trạng thái</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach ($variant->productValues as $key => $value)
                                                        <tr>
                                                            <td class="text-center">{{ $key + 1 }}</td>
                                                            <td>
                                                                <span class="text-muted">***ẩn*** <small class="text-info">(Admin không được xem)</small></span>
                                                            </td>
                                                            <td class="text-center">
                                                                <span class="status-badge value-{{ $value->status->value }}">
                                                                    {{ $value->status->label() }}
                                                                </span>
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    @else
                                        <div class="empty-state">
                                            <i class="fas fa-inbox"></i>
                                            <p>Chưa có giá trị nào</p>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Summary -->
            <div class="col-lg-4">
                <div class="summary-card">
                    <div class="summary-header">
                        <h3>
                            <i class="fas fa-chart-bar"></i>
                            Tổng quan
                        </h3>
                    </div>
                    <div class="summary-body">
                        <div class="summary-item-modern">
                            <span class="label">Tổng biến thể:</span>
                            <span class="value">{{ $product->variants->count() }}</span>
                        </div>
                        <div class="summary-item-modern">
                            <span class="label">Tổng tồn kho:</span>
                            <span class="value">{{ $product->variants->sum('stock_quantity') }}</span>
                        </div>
                        <div class="summary-item-modern">
                            <span class="label">Tổng đã bán:</span>
                            <span class="value">{{ $product->variants->sum('sold_count') }}</span>
                        </div>
                        <div class="summary-item-modern">
                            <span class="label">Giá thấp nhất:</span>
                            <span class="value price">{{ number_format($product->variants->min('price'), 0, ',', '.') }} VNĐ</span>
                        </div>
                        <div class="summary-item-modern">
                            <span class="label">Giá cao nhất:</span>
                            <span class="value price">{{ number_format($product->variants->max('price'), 0, ',', '.') }} VNĐ</span>
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
