@extends('admin.layouts.sidebar')

@section('title', 'Chi tiết đề xuất - ' . $featuredHistory->slug)

@section('main-content')
    <div class="category-container">
        <div class="mb-4">
            <a href="{{ route('admin.featured-histories.index') }}" class="btn back-button">
                <i class="fas fa-arrow-left"></i> Quay lại danh sách
            </a>
        </div>

        <div class="row">
            <div class="col-lg-8">
                <div class="product-info-card mb-3">
                    <div class="card-header py-2">
                        <h3 class="card-title mb-0" style="font-size: 0.95rem; font-weight: 600;">
                            <i class="fas fa-star"></i>
                            Thông tin đề xuất
                        </h3>
                    </div>
                    <div class="card-body py-2">
                        <div class="row g-2 mb-2">
                            <div class="col-6">
                                <small class="text-muted">Mã đề xuất:</small>
                                <p class="mb-0"><strong>#{{ $featuredHistory->slug }}</strong></p>
                            </div>
                            <div class="col-6">
                                <small class="text-muted">Loại:</small>
                                <p class="mb-0">
                                    <span class="status-badge bg-{{ $featuredHistory->featurable_type_badge }} text-white">
                                        {{ $featuredHistory->featurable_type_label }}
                                    </span>
                                </p>
                            </div>
                        </div>
                        <div class="row g-2 mb-2">
                            <div class="col-6">
                                <small class="text-muted">Sản phẩm/Dịch vụ:</small>
                                <p class="mb-0">
                                    @if ($featuredHistory->featurable)
                                        <a href="{{ $featuredHistory->featurable_type === 'App\\Models\\Product'
                                            ? route('admin.products.show', $featuredHistory->featurable->slug)
                                            : route('admin.services.show', $featuredHistory->featurable->slug) }}"
                                            class="item-name color-primary" target="_blank">
                                            <strong>{{ $featuredHistory->featurable->name }}</strong>
                                        </a>
                                    @else
                                        <span class="text-danger">Đã bị xóa</span>
                                    @endif
                                </p>
                            </div>
                            <div class="col-6">
                                <small class="text-muted">Số tiền:</small>
                                <p class="mb-0">
                                    <strong class="text-success" style="font-size: 1.2rem;">
                                        {{ $featuredHistory->formatted_amount }}
                                    </strong>
                                </p>
                            </div>
                        </div>
                        <div class="row g-2 mb-2">
                            <div class="col-6">
                                <small class="text-muted">Thời lượng:</small>
                                <p class="mb-0">
                                    <strong>{{ $featuredHistory->hours }} giờ</strong>
                                </p>
                            </div>
                            <div class="col-6">
                                <small class="text-muted">Trạng thái:</small>
                                <p class="mb-0">
                                    @if ($featuredHistory->isActive())
                                        <span class="status-badge bg-success text-white">Đang hoạt động</span>
                                    @else
                                        <span class="status-badge bg-secondary text-white">Đã hết hạn</span>
                                    @endif
                                </p>
                            </div>
                        </div>
                        <div class="row g-2 mb-2">
                            <div class="col-6">
                                <small class="text-muted">Thời gian bắt đầu:</small>
                                <p class="mb-0">
                                    @if ($featuredHistory->featured_from)
                                        {{ $featuredHistory->featured_from->format('d/m/Y H:i:s') }}
                                    @else
                                        <span class="text-muted">N/A</span>
                                    @endif
                                </p>
                            </div>
                            <div class="col-6">
                                <small class="text-muted">Thời gian kết thúc:</small>
                                <p class="mb-0">
                                    @if ($featuredHistory->featured_until)
                                        {{ $featuredHistory->featured_until->format('d/m/Y H:i:s') }}
                                        @if ($featuredHistory->isActive())
                                            <br>
                                            <small class="text-success">
                                                (Còn {{ $featuredHistory->featured_until->diffForHumans(null, true, false, 2) }})
                                            </small>
                                        @else
                                            <br>
                                            <small class="text-danger">(Đã hết hạn)</small>
                                        @endif
                                    @else
                                        <span class="text-muted">N/A</span>
                                    @endif
                                </p>
                            </div>
                        </div>
                        <div class="row g-2">
                            <div class="col-6">
                                <small class="text-muted">Ngày tạo:</small>
                                <p class="mb-0">{{ $featuredHistory->created_at->format('d/m/Y H:i:s') }}</p>
                            </div>
                            <div class="col-6">
                                <small class="text-muted">Cập nhật lần cuối:</small>
                                <p class="mb-0">{{ $featuredHistory->updated_at->format('d/m/Y H:i:s') }}</p>
                            </div>
                        </div>
                        @if ($featuredHistory->note)
                            <hr class="my-2">
                            <div class="row g-2">
                                <div class="col-12">
                                    <small class="text-muted">Ghi chú:</small>
                                    <p class="mb-0">{{ $featuredHistory->note }}</p>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

                <div class="product-info-card mb-3">
                    <div class="card-header py-2">
                        <h3 class="card-title mb-0" style="font-size: 0.95rem; font-weight: 600;">
                            <i class="fas fa-user"></i>
                            Thông tin người bán
                        </h3>
                    </div>
                    <div class="card-body py-2">
                        @if ($featuredHistory->seller)
                            <div class="row g-2 mb-2">
                                <div class="col-6">
                                    <small class="text-muted">Họ tên:</small>
                                    <p class="mb-0">
                                        <a href="{{ route('admin.sellers.show', $featuredHistory->seller->full_name) }}"
                                            class="item-name color-primary">
                                            <strong>{{ $featuredHistory->seller->full_name }}</strong>
                                        </a>
                                    </p>
                                </div>
                                <div class="col-6">
                                    <small class="text-muted">Email:</small>
                                    <p class="mb-0">{{ $featuredHistory->seller->email }}</p>
                                </div>
                            </div>
                            <div class="row g-2">
                                <div class="col-6">
                                    <small class="text-muted">Số dư ví:</small>
                                    <p class="mb-0">
                                        <strong>
                                            {{ number_format($featuredHistory->seller->wallet?->balance ?? 0, 0, ',', '.') }} VNĐ
                                        </strong>
                                    </p>
                                </div>
                                <div class="col-6">
                                    <small class="text-muted">Ngày tham gia:</small>
                                    <p class="mb-0">{{ $featuredHistory->seller->created_at->format('d/m/Y') }}</p>
                                </div>
                            </div>
                        @else
                            <div class="text-center py-3">
                                <i class="fas fa-user-slash fa-2x text-muted mb-2"></i>
                                <p class="text-muted mb-0">Người bán không tồn tại</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="summary-card" style="position: sticky; top: 20px;">
                    <div class="summary-header">
                        <h3>
                            <i class="fas fa-info-circle"></i>
                            Trạng thái
                        </h3>
                    </div>
                    <div class="summary-body">
                        <div class="text-center py-3">
                            @if ($featuredHistory->isActive())
                                <div class="mb-3">
                                    <i class="fas fa-star fa-3x text-warning"></i>
                                </div>
                                <p class="text-muted mb-2">Trạng thái đề xuất</p>
                                <span class="status-badge bg-success text-white" style="font-size: 1rem; padding: 0.5rem 1rem;">
                                    Đang hoạt động
                                </span>
                                <p class="text-success mt-2 mb-0 small">
                                    Còn {{ $featuredHistory->featured_until->diffForHumans(null, true, false, 2) }}
                                </p>
                            @else
                                <div class="mb-3">
                                    <i class="fas fa-clock fa-3x text-secondary"></i>
                                </div>
                                <p class="text-muted mb-2">Trạng thái đề xuất</p>
                                <span class="status-badge bg-secondary text-white" style="font-size: 1rem; padding: 0.5rem 1rem;">
                                    Đã hết hạn
                                </span>
                                <p class="text-muted mt-2 mb-0 small">
                                    Hết hạn {{ $featuredHistory->featured_until->diffForHumans() }}
                                </p>
                            @endif
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
