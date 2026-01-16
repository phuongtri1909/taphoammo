@extends('seller.layouts.sidebar')

@section('title', 'Quản lý sản phẩm - ' . $product->name)

@section('main-content')
    <div class="category-container">
        <!-- Back Button -->
        <div class="mb-4">
            <a href="{{ route('seller.products.index') }}" class="btn back-button">
                <i class="fas fa-arrow-left"></i> Quay lại danh sách
            </a>
        </div>

        @if ($product->admin_note)
            <div class="alert alert-{{ $product->status === \App\Enums\ProductStatus::REJECTED || $product->status === \App\Enums\ProductStatus::BANNED ? 'danger' : 'info' }} mb-4">
                <h5><i class="fas fa-sticky-note"></i> Ghi chú từ Admin:</h5>
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

                @if ($product->description || $product->long_description || in_array($product->status, [\App\Enums\ProductStatus::APPROVED, \App\Enums\ProductStatus::HIDDEN, \App\Enums\ProductStatus::PENDING, \App\Enums\ProductStatus::REJECTED]))
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
                                    <form action="{{ route('seller.products.update-status', $product) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('PATCH')
                                        @if ($product->status === \App\Enums\ProductStatus::APPROVED)
                                            <input type="hidden" name="status" value="hidden">
                                            <button type="submit" class="btn-modern warning">
                                                <i class="fas fa-eye-slash"></i> Ẩn sản phẩm
                                            </button>
                                        @else
                                            <input type="hidden" name="status" value="approved">
                                            <button type="submit" class="btn-modern primary">
                                                <i class="fas fa-eye"></i> Hiển thị sản phẩm
                                            </button>
                                        @endif
                                    </form>
                                </div>
                            @endif

                            @if (in_array($product->status, [\App\Enums\ProductStatus::PENDING, \App\Enums\ProductStatus::REJECTED]))
                                <div class="product-actions">
                                    <a href="{{ route('seller.products.edit', $product) }}" class="btn-modern primary">
                                        <i class="fas fa-edit"></i> Chỉnh sửa sản phẩm
                                    </a>
                                    @include('components.delete-form', [
                                        'id' => $product->slug,
                                        'route' => route('seller.products.destroy', $product),
                                        'message' => "Bạn có chắc chắn muốn xóa sản phẩm '{$product->name}'?",
                                    ])
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
                                        @if ($product->status === \App\Enums\ProductStatus::APPROVED)
                                            <form action="{{ route('seller.products.update-variant-status', $variant) }}" method="POST" class="d-inline" onclick="event.stopPropagation();">
                                                @csrf
                                                @method('PATCH')
                                                @if ($variant->status === \App\Enums\CommonStatus::ACTIVE)
                                                    <input type="hidden" name="status" value="inactive">
                                                    <button type="submit" class="btn-variant-toggle warning" title="Tắt biến thể" onclick="event.stopPropagation();">
                                                        <i class="fas fa-pause"></i>
                                                    </button>
                                                @else
                                                    <input type="hidden" name="status" value="active">
                                                    <button type="submit" class="btn-variant-toggle primary" title="Bật biến thể" onclick="event.stopPropagation();">
                                                        <i class="fas fa-play"></i>
                                                    </button>
                                                @endif
                                            </form>
                                        @endif
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

                        @if ($product->status === \App\Enums\ProductStatus::APPROVED)
                            <!-- Add Values Form -->
                            <div class="add-values-section-modern">
                                <button type="button" class="btn-modern primary" data-bs-toggle="collapse" data-bs-target="#addValuesForm{{ $variant->id }}">
                                    <i class="fas fa-plus"></i> Thêm giá trị
                                </button>
                                <div class="collapse mt-3" id="addValuesForm{{ $variant->id }}">
                                    <form action="{{ route('seller.products.store-values', $variant) }}" method="POST">
                                        @csrf
                                        <div class="form-group mb-3">
                                            <label class="form-label-custom">Nhập giá trị (mỗi dòng 1 giá trị)</label>
                                            <textarea name="values" class="custom-input" rows="5" placeholder="email1@example.com|password1&#10;email2@example.com|password2&#10;..."></textarea>
                                            <div class="form-hint mt-1">
                                                <i class="fas fa-info-circle"></i> Mỗi dòng là 1 tài khoản/giá trị. Có thể dùng | để phân tách các trường.
                                            </div>
                                        </div>
                                        <button type="submit" class="btn-modern primary">
                                            <i class="fas fa-save"></i> Lưu giá trị
                                        </button>
                                    </form>
                                </div>
                            </div>
                        @endif

                        <!-- Values List -->
                        @if ($variant->productValues->count() > 0)
                            <div class="values-table-wrapper">
                                <table class="values-table">
                                    <thead>
                                        <tr>
                                            <th style="width: 60px;" class="text-center">STT</th>
                                            <th>Giá trị</th>
                                            <th style="width: 150px;" class="text-center">Trạng thái</th>
                                            @if ($product->status === \App\Enums\ProductStatus::APPROVED)
                                                <th style="width: 120px;" class="text-center">Thao tác</th>
                                            @endif
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($variant->productValues as $key => $value)
                                            <tr>
                                                <td class="text-center">{{ $key + 1 }}</td>
                                                <td>
                                                    @can('viewData', $value)
                                                        @if ($value->status === \App\Enums\ProductValueStatus::AVAILABLE)
                                                            <code class="value-text-display">{{ $value->data['value'] ?? 'N/A' }}</code>
                                                        @else
                                                            <span class="text-muted">***ẩn***</span>
                                                        @endif
                                                    @else
                                                        <span class="text-muted">***không có quyền xem***</span>
                                                    @endcan
                                                </td>
                                                <td class="text-center">
                                                    <span class="status-badge value-{{ $value->status->value }}">
                                                        {{ $value->status->label() }}
                                                    </span>
                                                </td>
                                                @if ($product->status === \App\Enums\ProductStatus::APPROVED)
                                                    <td class="text-center">
                                                        @if ($value->status === \App\Enums\ProductValueStatus::AVAILABLE)
                                                            <div class="action-buttons-wrapper">
                                                                <button type="button" class="action-icon edit-icon border-0" data-bs-toggle="modal" data-bs-target="#editValueModal{{ $value->id }}" title="Sửa">
                                                                    <i class="fas fa-pencil-alt"></i>
                                                                </button>

                                                                @include('components.delete-form', [
                                                                    'id' => $value->id,
                                                                    'route' => route('seller.products.destroy-value', $value),
                                                                    'message' => "Bạn có chắc chắn muốn xóa giá trị '{$value->data['value']}'?",
                                                                ])
                                                            </div>

                                                            <!-- Edit Value Modal -->
                                                            <div class="modal fade" id="editValueModal{{ $value->id }}" tabindex="-1" aria-hidden="true">
                                                                <div class="modal-dialog">
                                                                    <div class="modal-content modal-content-custom">
                                                                        <div class="modal-header">
                                                                            <h5 class="modal-title color-primary-6">Sửa giá trị</h5>
                                                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                                        </div>
                                                                        <form action="{{ route('seller.products.update-value', $value) }}" method="POST">
                                                                            @csrf
                                                                            @method('PUT')
                                                                            <div class="modal-body">
                                                                                <div class="form-group">
                                                                                    <label class="form-label-custom">Giá trị</label>
                                                                                    <input type="text" name="value" class="custom-input" value="{{ $value->data['value'] ?? '' }}" required>
                                                                                </div>
                                                                            </div>
                                                                            <div class="modal-footer">
                                                                                <button type="button" class="btn back-button" data-bs-dismiss="modal">Hủy</button>
                                                                                <button type="submit" class="btn action-button">Cập nhật</button>
                                                                            </div>
                                                                        </form>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        @else
                                                            <span class="text-muted">-</span>
                                                        @endif
                                                    </td>
                                                @endif
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
