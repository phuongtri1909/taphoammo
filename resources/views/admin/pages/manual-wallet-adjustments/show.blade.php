@extends('admin.layouts.sidebar')

@section('title', 'Chi tiết điều chỉnh ví - ' . $adjustment->slug)

@section('main-content')
    <div class="category-container">
        <div class="mb-4">
            <a href="{{ route('admin.manual-wallet-adjustments.index') }}" class="btn back-button">
                <i class="fas fa-arrow-left"></i> Quay lại danh sách
            </a>
        </div>

        <div class="row">
            <div class="col-lg-8">
                <div class="product-info-card mb-3">
                    <div class="card-header py-2">
                        <h3 class="card-title mb-0" style="font-size: 0.95rem; font-weight: 600;">
                            <i class="fas fa-wallet"></i>
                            Thông tin điều chỉnh
                        </h3>
                    </div>
                    <div class="card-body py-3">
                        <div class="row g-3 mb-3">
                            <div class="col-6">
                                <small class="text-muted">Mã điều chỉnh:</small>
                                <p class="mb-0"><strong>#{{ $adjustment->slug }}</strong></p>
                            </div>
                            <div class="col-6">
                                <small class="text-muted">Loại:</small>
                                <p class="mb-0">
                                    <span class="badge bg-{{ $adjustment->adjustment_type->badgeColor() }} text-white">
                                        <i class="fas fa-{{ $adjustment->adjustment_type->icon() }}"></i>
                                        {{ $adjustment->adjustment_type->label() }}
                                    </span>
                                </p>
                            </div>
                        </div>
                        <div class="row g-3 mb-3">
                            <div class="col-6">
                                <small class="text-muted">Số tiền:</small>
                                <p class="mb-0">
                                    <strong class="{{ $adjustment->adjustment_type === \App\Enums\ManualAdjustmentType::ADD ? 'text-success' : 'text-danger' }}" style="font-size: 1.2rem;">
                                        {{ $adjustment->adjustment_type === \App\Enums\ManualAdjustmentType::ADD ? '+' : '-' }}{{ number_format($adjustment->amount, 0, ',', '.') }}₫
                                    </strong>
                                </p>
                            </div>
                            <div class="col-6">
                                <small class="text-muted">Ngày xử lý:</small>
                                <p class="mb-0">{{ $adjustment->processed_at->format('d/m/Y H:i:s') }}</p>
                            </div>
                        </div>
                        <div class="row g-3 mb-3">
                            <div class="col-12">
                                <small class="text-muted">Lý do:</small>
                                <p class="mb-0">{{ $adjustment->reason }}</p>
                            </div>
                        </div>
                        @if($adjustment->admin_note)
                            <div class="row g-3">
                                <div class="col-12">
                                    <small class="text-muted">Ghi chú Admin:</small>
                                    <p class="mb-0">{{ $adjustment->admin_note }}</p>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

                <div class="product-info-card mb-3">
                    <div class="card-header py-2">
                        <h3 class="card-title mb-0" style="font-size: 0.95rem; font-weight: 600;">
                            <i class="fas fa-user"></i>
                            Thông tin người dùng
                        </h3>
                    </div>
                    <div class="card-body py-3">
                        <div class="row g-3">
                            <div class="col-6">
                                <small class="text-muted">Họ tên:</small>
                                <p class="mb-0"><strong>{{ $adjustment->user->full_name }}</strong></p>
                            </div>
                            <div class="col-6">
                                <small class="text-muted">Email:</small>
                                <p class="mb-0">{{ $adjustment->user->email }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                @if($adjustment->walletTransaction)
                    <div class="product-info-card mb-3">
                        <div class="card-header py-2">
                            <h3 class="card-title mb-0" style="font-size: 0.95rem; font-weight: 600;">
                                <i class="fas fa-exchange-alt"></i>
                                Giao dịch ví
                            </h3>
                        </div>
                        <div class="card-body py-3">
                            <div class="row g-3 mb-3">
                                <div class="col-6">
                                    <small class="text-muted">Số dư trước:</small>
                                    <p class="mb-0"><strong>{{ number_format($adjustment->walletTransaction->balance_before, 0, ',', '.') }}₫</strong></p>
                                </div>
                                <div class="col-6">
                                    <small class="text-muted">Số dư sau:</small>
                                    <p class="mb-0"><strong>{{ number_format($adjustment->walletTransaction->balance_after, 0, ',', '.') }}₫</strong></p>
                                </div>
                            </div>
                            <div class="row g-3">
                                <div class="col-12">
                                    <small class="text-muted">Mô tả:</small>
                                    <p class="mb-0">{{ $adjustment->walletTransaction->description }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            </div>

            <div class="col-lg-4">
                <div class="summary-card" style="position: sticky; top: 20px;">
                    <div class="summary-header">
                        <h3>
                            <i class="fas fa-info-circle"></i>
                            Thông tin bổ sung
                        </h3>
                    </div>
                    <div class="summary-body">
                        <div class="mb-3">
                            <small class="text-muted">Người xử lý:</small>
                            <p class="mb-0"><strong>{{ $adjustment->processedBy->full_name }}</strong></p>
                        </div>
                        <div class="mb-3">
                            <small class="text-muted">Ngày tạo:</small>
                            <p class="mb-0">{{ $adjustment->created_at->format('d/m/Y H:i:s') }}</p>
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
