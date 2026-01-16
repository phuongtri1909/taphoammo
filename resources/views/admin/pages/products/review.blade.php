@extends('admin.layouts.sidebar')

@section('title', 'Duyệt sản phẩm - ' . $product->name)

@section('main-content')
    <div class="category-container">
        <div class="mb-4">
            <a href="{{ route('admin.products.pending') }}" class="btn back-button">
                <i class="fas fa-arrow-left"></i> Quay lại danh sách
            </a>
        </div>

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
                            <span class="product-status-badge status-badge pending">Chờ duyệt</span>
                        </div>
                    </div>
                </div>

                @if ($product->description || $product->long_description)
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
                        </div>
                    </div>
                @endif

                <!-- Variants -->
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
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Actions -->
            <div class="col-lg-4">
                <div class="summary-card" style="position: sticky; top: 20px;">
                    <div class="summary-header">
                        <h3>
                            <i class="fas fa-check-circle"></i>
                            Duyệt sản phẩm
                        </h3>
                    </div>
                    <div class="summary-body">
                        <!-- Approve Form -->
                        <form action="{{ route('admin.products.approve', $product) }}" method="POST" class="mb-4">
                            @csrf
                            <div class="form-group mb-3">
                                <label class="form-label-custom">Ghi chú (tùy chọn)</label>
                                <textarea name="admin_note" class="custom-input" rows="2" placeholder="Ghi chú cho seller..."></textarea>
                            </div>
                            <button type="submit" class="btn-modern primary w-100">
                                <i class="fas fa-check"></i> Duyệt sản phẩm
                            </button>
                        </form>

                        <hr class="my-4">

                        <!-- Reject Form -->
                        <form action="{{ route('admin.products.reject', $product) }}" method="POST">
                            @csrf
                            <div class="form-group mb-3">
                                <label class="form-label-custom">Lý do từ chối <span class="required-mark">*</span></label>
                                <textarea name="admin_note" class="custom-input" rows="3" required placeholder="Nhập lý do từ chối sản phẩm..."></textarea>
                            </div>
                            <button type="submit" class="btn-modern danger w-100">
                                <i class="fas fa-times"></i> Từ chối sản phẩm
                            </button>
                        </form>
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
