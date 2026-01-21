@extends('admin.layouts.sidebar')

@section('title', 'Chi tiết nạp tiền - ' . $deposit->slug)

@section('main-content')
    <div class="category-container">
        <div class="mb-4">
            <a href="{{ route('admin.deposits.index') }}" class="btn back-button">
                <i class="fas fa-arrow-left"></i> Quay lại danh sách
            </a>
        </div>

        <div class="row">
            <div class="col-lg-8">
                <div class="product-info-card mb-3">
                    <div class="card-header py-2">
                        <h3 class="card-title mb-0" style="font-size: 0.95rem; font-weight: 600;">
                            <i class="fas fa-credit-card"></i>
                            Thông tin nạp tiền
                        </h3>
                    </div>
                    <div class="card-body py-2">
                        <div class="row g-2 mb-2">
                            <div class="col-6">
                                <small class="text-muted">Mã giao dịch:</small>
                                <p class="mb-0"><strong>#{{ $deposit->slug }}</strong></p>
                            </div>
                            <div class="col-6">
                                <small class="text-muted">Trạng thái:</small>
                                <p class="mb-0">
                                    <span class="status-badge bg-{{ $deposit->status->badgeColor() }} text-white">
                                        {{ $deposit->status->label() }}
                                    </span>
                                </p>
                            </div>
                        </div>
                        <div class="row g-2 mb-2">
                            <div class="col-6">
                                <small class="text-muted">Mã giao dịch ngân hàng:</small>
                                <p class="mb-0"><strong class="font-monospace">{{ $deposit->transaction_code }}</strong></p>
                            </div>
                            <div class="col-6">
                                <small class="text-muted">Ngày tạo:</small>
                                <p class="mb-0">{{ $deposit->created_at->format('d/m/Y H:i:s') }}</p>
                            </div>
                        </div>
                        <div class="row g-2 mb-2">
                            <div class="col-6">
                                <small class="text-muted">Số tiền:</small>
                                <p class="mb-0">
                                    <strong class="text-success" style="font-size: 1.2rem;">
                                        {{ $deposit->amount_formatted }}
                                    </strong>
                                </p>
                            </div>
                            @if($deposit->amount_received)
                                <div class="col-6">
                                    <small class="text-muted">Số tiền nhận được:</small>
                                    <p class="mb-0">
                                        <strong class="text-success" style="font-size: 1.2rem;">
                                            {{ $deposit->amount_received_formatted }}
                                        </strong>
                                    </p>
                                </div>
                            @endif
                        </div>
                        @if($deposit->processed_at)
                            <div class="row g-2">
                                <div class="col-6">
                                    <small class="text-muted">Ngày xử lý:</small>
                                    <p class="mb-0">{{ $deposit->processed_at->format('d/m/Y H:i:s') }}</p>
                                </div>
                                @if($deposit->casso_transaction_id)
                                    <div class="col-6">
                                        <small class="text-muted">Casso Transaction ID:</small>
                                        <p class="mb-0"><code>{{ $deposit->casso_transaction_id }}</code></p>
                                    </div>
                                @endif
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
                    <div class="card-body py-2">
                        <div class="row g-2">
                            <div class="col-6">
                                <small class="text-muted">Họ tên:</small>
                                <p class="mb-0"><strong>{{ $deposit->user->full_name }}</strong></p>
                            </div>
                            <div class="col-6">
                                <small class="text-muted">Email:</small>
                                <p class="mb-0">{{ $deposit->user->email }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="product-info-card mb-3">
                    <div class="card-header py-2">
                        <h3 class="card-title mb-0" style="font-size: 0.95rem; font-weight: 600;">
                            <i class="fas fa-university"></i>
                            Thông tin ngân hàng nhận tiền
                        </h3>
                    </div>
                    <div class="card-body py-2">
                        <div class="row g-2 mb-2">
                            <div class="col-6">
                                <small class="text-muted">Ngân hàng:</small>
                                <p class="mb-0"><strong>{{ $deposit->bank_name }}</strong></p>
                            </div>
                            <div class="col-6">
                                <small class="text-muted">Mã ngân hàng:</small>
                                <p class="mb-0"><code>{{ $deposit->bank_code }}</code></p>
                            </div>
                        </div>
                        <div class="row g-2">
                            <div class="col-6">
                                <small class="text-muted">Số tài khoản:</small>
                                <p class="mb-0"><strong class="font-monospace">{{ $deposit->bank_account_number }}</strong></p>
                            </div>
                            <div class="col-6">
                                <small class="text-muted">Chủ tài khoản:</small>
                                <p class="mb-0"><strong>{{ $deposit->bank_account_name }}</strong></p>
                            </div>
                        </div>
                    </div>
                </div>

                @if($deposit->note)
                    <div class="product-info-card mb-3">
                        <div class="card-header py-2">
                            <h3 class="card-title mb-0" style="font-size: 0.95rem; font-weight: 600;">
                                <i class="fas fa-sticky-note"></i>
                                Ghi chú
                            </h3>
                        </div>
                        <div class="card-body py-2">
                            <p class="mb-0">{{ $deposit->note }}</p>
                        </div>
                    </div>
                @endif

                @if($deposit->casso_response)
                    <div class="product-info-card mb-3">
                        <div class="card-header py-2">
                            <h3 class="card-title mb-0" style="font-size: 0.95rem; font-weight: 600;">
                                <i class="fas fa-code"></i>
                                Casso Response
                            </h3>
                        </div>
                        <div class="card-body py-2">
                            <pre class="bg-light p-3 rounded small" style="max-height: 300px; overflow-y: auto;">{{ json_encode($deposit->casso_response, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                        </div>
                    </div>
                @endif
            </div>

            <div class="col-lg-4">
                <div class="summary-card" style="position: sticky; top: 20px;">
                    <div class="summary-header">
                        <h3>
                            <i class="fas fa-info-circle"></i>
                            Thông tin
                        </h3>
                    </div>
                    <div class="summary-body">
                        <div class="text-center py-3">
                            <p class="text-muted mb-2">Trạng thái giao dịch</p>
                            <span class="badge bg-{{ $deposit->status->badgeColor() }} text-white" style="font-size: 1rem; padding: 0.5rem 1rem;">
                                {{ $deposit->status->label() }}
                            </span>
                        </div>
                        @if($deposit->processed_at)
                            <hr class="my-3">
                            <div class="text-muted small">
                                <p class="mb-1"><strong>Ngày xử lý:</strong></p>
                                <p class="mb-0">{{ $deposit->processed_at->format('d/m/Y H:i:s') }}</p>
                            </div>
                        @endif
                        @if($deposit->casso_transaction_id)
                            <hr class="my-3">
                            <div class="text-muted small">
                                <p class="mb-1"><strong>Casso ID:</strong></p>
                                <p class="mb-0"><code>{{ $deposit->casso_transaction_id }}</code></p>
                            </div>
                        @endif
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
