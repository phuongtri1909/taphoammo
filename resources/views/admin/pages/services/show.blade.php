@extends('admin.layouts.sidebar')

@section('title', 'Chi tiết dịch vụ - ' . $service->name)

@section('main-content')
    <div class="category-container">
        <div class="mb-4">
            <a href="{{ route('admin.services.index') }}" class="btn back-button">
                <i class="fas fa-arrow-left"></i> Quay lại danh sách
            </a>
        </div>

        @if ($service->admin_note)
            <div class="alert alert-{{ $service->status === \App\Enums\ServiceStatus::REJECTED || $service->status === \App\Enums\ServiceStatus::BANNED ? 'danger' : 'info' }} mb-4">
                <h5><i class="fas fa-sticky-note"></i> Ghi chú Admin:</h5>
                <p class="mb-0">{{ $service->admin_note }}</p>
            </div>
        @endif

        <div class="row">
            <!-- Service Info -->
            <div class="col-lg-8">
                <!-- Service Header -->
                <div class="product-show-header mb-4">
                    <div class="product-header-content">
                        <div class="product-image-wrapper">
                            @if ($service->image)
                                <img src="{{ Storage::url($service->image) }}" alt="{{ $service->name }}">
                            @else
                                <div class="image-placeholder">
                                    <i class="fas fa-concierge-bell"></i>
                                </div>
                            @endif
                        </div>
                        <div class="product-info-wrapper">
                            <h1 class="product-title">{{ $service->name }}</h1>
                            <div class="product-meta">
                                <div class="product-meta-item">
                                    <i class="fas fa-folder"></i>
                                    <span>{{ $service->serviceSubCategory->serviceCategory->name ?? 'N/A' }} > {{ $service->serviceSubCategory->name ?? 'N/A' }}</span>
                                </div>
                                <div class="product-meta-item">
                                    <i class="fas fa-user"></i>
                                    <span>Seller: <strong>{{ $service->seller->full_name }}</strong> ({{ $service->seller->email }})</span>
                                </div>
                                <div class="product-meta-item">
                                    <i class="fas fa-clock"></i>
                                    <span>{{ $service->created_at->format('d/m/Y H:i') }}</span>
                                </div>
                            </div>
                            <span class="product-status-badge status-badge {{ $service->status->value }}">
                                {{ $service->status->label() }}
                            </span>
                        </div>
                    </div>
                </div>

                <div class="product-info-card mb-4">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-info-circle"></i>
                            Thông tin dịch vụ
                        </h3>
                    </div>
                    <div class="card-body">
                        @if ($service->description)
                            <div class="product-description-section mb-3">
                                <h5><i class="fas fa-align-left"></i> Mô tả ngắn</h5>
                                <p class="mb-0">{{ $service->description }}</p>
                            </div>
                        @endif

                        @if ($service->long_description)
                            <div class="product-description-section mb-3">
                                <h5><i class="fas fa-file-alt"></i> Mô tả chi tiết</h5>
                                <div class="long-description-display">{!! nl2br(e($service->long_description)) !!}</div>
                            </div>
                        @endif

                        @if (in_array($service->status, [\App\Enums\ServiceStatus::APPROVED, \App\Enums\ServiceStatus::HIDDEN]))
                            <div class="product-actions mt-4">
                                <form action="{{ route('admin.services.ban', $service) }}" method="POST" class="d-inline">
                                    @csrf
                                    <div class="form-group mb-3">
                                        <label class="form-label-custom">Lý do cấm <span class="required-mark">*</span></label>
                                        <textarea name="admin_note" class="custom-input" rows="3" required placeholder="Nhập lý do cấm dịch vụ..."></textarea>
                                    </div>
                                    <button type="submit" class="btn-modern danger">
                                        <i class="fas fa-ban"></i> Cấm dịch vụ
                                    </button>
                                </form>
                            </div>
                        @endif

                        @if ($service->status === \App\Enums\ServiceStatus::BANNED)
                            <div class="product-actions mt-4">
                                <form action="{{ route('admin.services.unban', $service) }}" method="POST" class="d-inline">
                                    @csrf
                                    <p class="text-muted mb-3">Bỏ cấm sẽ chuyển dịch vụ về trạng thái chờ duyệt.</p>
                                    <button type="submit" class="btn-modern primary">
                                        <i class="fas fa-unlock"></i> Bỏ cấm dịch vụ
                                    </button>
                                </form>
                            </div>
                        @endif

                        @if ($service->status === \App\Enums\ServiceStatus::PENDING)
                            <div class="product-actions mt-4">
                                <a href="{{ route('admin.services.review', $service) }}" class="btn-modern primary">
                                    <i class="fas fa-check-circle"></i> Đi đến trang duyệt
                                </a>
                            </div>
                        @endif

                        @if ($service->status === \App\Enums\ServiceStatus::REJECTED)
                            <div class="alert alert-warning mb-0 mt-4">
                                <i class="fas fa-info-circle"></i>
                                Dịch vụ đã bị từ chối. Seller cần chỉnh sửa và gửi duyệt lại.
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Variants -->
                <div class="variants-accordion">
                    @foreach ($service->variants as $index => $variant)
                        <div class="variant-card-accordion mb-3">
                            <div class="variant-accordion-header">
                                <div class="variant-header-content">
                                    <div class="variant-header-info" data-bs-toggle="collapse" data-bs-target="#variantCollapse{{ $variant->id }}" aria-expanded="{{ $index === 0 ? 'true' : 'false' }}" aria-controls="variantCollapse{{ $variant->id }}">
                                        <h4 class="variant-name">{{ $variant->name }}</h4>
                                        <div class="variant-badges-compact">
                                            <span class="variant-badge price">{{ number_format($variant->price, 0, ',', '.') }} VNĐ</span>
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
                                    <div class="mb-2">
                                        <strong class="text-sm">Slug:</strong>
                                        <code class="ms-2">{{ $variant->slug }}</code>
                                    </div>
                                    <div class="mb-2">
                                        <strong class="text-sm">Thứ tự:</strong>
                                        <span class="ms-2 text-muted">{{ $variant->order }}</span>
                                    </div>
                                    <div class="mb-0">
                                        <strong class="text-sm">Đã bán:</strong>
                                        <span class="ms-2 text-muted">{{ $variant->sold_count }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Sidebar -->
            <div class="col-lg-4">
                <div class="summary-card" style="position: sticky; top: 20px;">
                    <div class="summary-header">
                        <h3>
                            <i class="fas fa-info-circle"></i>
                            Thông tin
                        </h3>
                    </div>
                    <div class="summary-body">
                        <div class="summary-item">
                            <span class="summary-label">Trạng thái</span>
                            <span class="status-badge {{ $service->status->value }}">
                                {{ $service->status->label() }}
                            </span>
                        </div>
                        <div class="summary-item">
                            <span class="summary-label">Biến thể</span>
                            <span class="summary-value">{{ $service->variants->count() }}</span>
                        </div>
                        <div class="summary-item">
                            <span class="summary-label">Ngày tạo</span>
                            <span class="summary-value">{{ $service->created_at->format('d/m/Y H:i') }}</span>
                        </div>
                        <div class="summary-item">
                            <span class="summary-label">Cập nhật lần cuối</span>
                            <span class="summary-value">{{ $service->updated_at->format('d/m/Y H:i') }}</span>
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
