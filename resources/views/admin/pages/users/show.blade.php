@extends('admin.layouts.sidebar')

@php
    use Illuminate\Support\Facades\Storage;
@endphp

@section('title', 'Chi tiết người dùng - ' . $user->full_name)

@section('main-content')
    <div class="category-container">
        <div class="mb-4">
            <a href="{{ route('admin.users.index') }}" class="btn back-button">
                <i class="fas fa-arrow-left"></i> Quay lại danh sách
            </a>
        </div>

        <div class="row">
            <div class="col-lg-8">
                <div class="product-show-header mb-4">
                    <div class="product-header-content">
                        <div class="product-image-wrapper">
                            @if ($user->avatar)
                                <img src="{{ Storage::url($user->avatar) }}" alt="{{ $user->full_name }}">
                            @else
                                <div class="image-placeholder">
                                    <span style="font-size: 48px;">{{ strtoupper(substr($user->full_name, 0, 1)) }}</span>
                                </div>
                            @endif
                        </div>
                        <div class="product-info-wrapper">
                            <h1 class="product-title">{{ $user->full_name }}</h1>
                            <div class="product-meta">
                                <div class="product-meta-item">
                                    <i class="fas fa-envelope"></i>
                                    <span>{{ $user->email }}</span>
                                </div>
                                <div class="product-meta-item">
                                    <i class="fas fa-user-tag"></i>
                                    <span>
                                        @if($user->role === 'admin')
                                            Admin
                                        @elseif($user->role === 'seller')
                                            Người bán
                                        @else
                                            Người dùng
                                        @endif
                                    </span>
                                </div>
                                <div class="product-meta-item">
                                    <i class="fas fa-clock"></i>
                                    <span>Tham gia: {{ $user->created_at->format('d/m/Y H:i') }}</span>
                                </div>
                                @if($user->wallet)
                                    <div class="product-meta-item">
                                        <i class="fas fa-wallet"></i>
                                        <span class="text-success">
                                            Số dư: {{ number_format($user->wallet->balance, 0, ',', '.') }}₫
                                        </span>
                                    </div>
                                @endif
                            </div>
                            <div class="mt-2">
                                @if($user->is_seller_banned)
                                    <span class="product-status-badge status-badge banned">
                                        <i class="fas fa-ban"></i> Đã khóa
                                    </span>
                                @elseif($user->active)
                                    <span class="product-status-badge status-badge active">
                                        <i class="fas fa-check-circle"></i> Hoạt động
                                    </span>
                                @else
                                    <span class="product-status-badge status-badge inactive">
                                        <i class="fas fa-times-circle"></i> Chưa kích hoạt
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <div class="product-info-card mb-3">
                    <div class="card-header py-2">
                        <h3 class="card-title mb-0" style="font-size: 0.95rem; font-weight: 600;">
                            <i class="fas fa-chart-bar"></i>
                            Thống kê tổng quan
                        </h3>
                    </div>
                    <div class="card-body py-2">
                        <div class="row g-2">
                            <div class="col-6 col-md-3">
                                <div class="text-center p-2 bg-light rounded">
                                    <div class="fw-bold text-info mb-1" style="font-size: 1.1rem;">
                                        {{ number_format($totalDeposited, 0, ',', '.') }}₫
                                    </div>
                                    <div class="text-muted" style="font-size: 0.75rem;">Đã nạp</div>
                                </div>
                            </div>
                            @if($user->role === 'seller')
                                <div class="col-6 col-md-3">
                                    <div class="text-center p-2 bg-light rounded">
                                        <div class="fw-bold text-warning mb-1" style="font-size: 1.1rem;">
                                            {{ number_format($totalWithdrawn, 0, ',', '.') }}₫
                                        </div>
                                        <div class="text-muted" style="font-size: 0.75rem;">Đã rút</div>
                                    </div>
                                </div>
                            @endif
                            <div class="col-6 col-md-3">
                                <div class="text-center p-2 bg-light rounded">
                                    <div class="fw-bold text-primary mb-1" style="font-size: 1.1rem;">
                                        {{ number_format($totalSpent, 0, ',', '.') }}₫
                                    </div>
                                    <div class="text-muted" style="font-size: 0.75rem;">Đã mua</div>
                                </div>
                            </div>
                            <div class="col-6 col-md-3">
                                <div class="text-center p-2 bg-light rounded">
                                    <div class="fw-bold text-success mb-1" style="font-size: 1.1rem;">
                                        {{ $productOrders->total() + $serviceOrders->total() }}
                                    </div>
                                    <div class="text-muted" style="font-size: 0.75rem;">Tổng đơn hàng</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                @if($user->role === 'seller' && $salesStats)
                    <div class="product-info-card mb-3">
                        <div class="card-header py-2">
                            <h3 class="card-title mb-0" style="font-size: 0.95rem; font-weight: 600;">
                                <i class="fas fa-store"></i>
                                Thống kê bán hàng
                            </h3>
                        </div>
                        <div class="card-body py-2">
                            <div class="row g-2">
                                <div class="col-6 col-md-3">
                                    <div class="text-center p-2 bg-light rounded">
                                        <div class="fw-bold text-primary mb-1" style="font-size: 1.1rem;">{{ $salesStats['total_products'] }}</div>
                                        <div class="text-muted" style="font-size: 0.75rem;">Sản phẩm</div>
                                    </div>
                                </div>
                                <div class="col-6 col-md-3">
                                    <div class="text-center p-2 bg-light rounded">
                                        <div class="fw-bold text-success mb-1" style="font-size: 1.1rem;">{{ $salesStats['total_product_orders'] + $salesStats['total_service_orders'] }}</div>
                                        <div class="text-muted" style="font-size: 0.75rem;">Tổng đơn bán</div>
                                    </div>
                                </div>
                                <div class="col-6 col-md-3">
                                    <div class="text-center p-2 bg-light rounded">
                                        <div class="fw-bold text-info mb-1" style="font-size: 1.1rem;">
                                            {{ number_format($salesStats['total_product_sales'] + $salesStats['total_service_sales'], 0, ',', '.') }}₫
                                        </div>
                                        <div class="text-muted" style="font-size: 0.75rem;">Tổng doanh thu</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                @if($user->role === 'seller' && ($user->bank_name || $user->bank_account_number))
                    <div class="product-info-card mb-3">
                        <div class="card-header py-2">
                            <h3 class="card-title mb-0" style="font-size: 0.95rem; font-weight: 600;">
                                <i class="fas fa-university"></i>
                                Thông tin ngân hàng
                            </h3>
                        </div>
                        <div class="card-body py-2">
                            <div class="row g-2 mb-2">
                                @if($user->bank_name)
                                    <div class="col-md-6">
                                        <small class="text-muted">Ngân hàng:</small>
                                        <p class="mb-0"><strong>{{ $user->bank_name }}</strong></p>
                                    </div>
                                @endif
                                @if($user->bank_account_number)
                                    <div class="col-md-6">
                                        <small class="text-muted">Số tài khoản:</small>
                                        <p class="mb-0"><strong>{{ $user->bank_account_number }}</strong></p>
                                    </div>
                                @endif
                                @if($user->bank_account_name)
                                    <div class="col-md-6">
                                        <small class="text-muted">Tên chủ tài khoản:</small>
                                        <p class="mb-0"><strong>{{ $user->bank_account_name }}</strong></p>
                                    </div>
                                @endif
                                @if($user->qr_code)
                                    <div class="col-md-6">
                                        <small class="text-muted">Mã QR:</small>
                                        <p class="mb-0">
                                            <img src="{{ Storage::url($user->qr_code) }}" alt="QR Code" style="max-width: 100px; max-height: 100px;">
                                        </p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Product Ordrs -->
                @if($productOrders->count() > 0)
                    <div class="product-info-card mb-3">
                        <div class="card-header py-2 d-flex justify-content-between align-items-center">
                            <h3 class="card-title mb-0" style="font-size: 0.95rem; font-weight: 600;">
                                <i class="fas fa-shopping-cart"></i>
                                Đơn hàng sản phẩm ({{ $productOrders->total() }})
                            </h3>
                        </div>
                        <div class="card-body py-2">
                            <div class="data-table-container">
                                <table class="data-table">
                                    <thead>
                                        <tr class="text-center">
                                            <th class="column-stt">STT</th>
                                            <th>Mã đơn</th>
                                            <th>Người bán</th>
                                            <th>Tổng tiền</th>
                                            <th>Trạng thái</th>
                                            <th>Ngày tạo</th>
                                            <th>Thao tác</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($productOrders as $key => $order)
                                            <tr>
                                                <td>{{ $productOrders->firstItem() + $key }}</td>
                                                <td>#{{ $order->slug }}</td>
                                                <td class="text-start">
                                                    <small>{{ $order->seller->full_name }}</small>
                                                </td>
                                                <td class="text-primary">
                                                    <strong>{{ number_format($order->total_amount, 0, ',', '.') }}₫</strong>
                                                </td>
                                                <td>
                                                    <span class="status-badge bg-{{ $order->status->badgeColor() }} text-white">
                                                        {{ $order->status->label() }}
                                                    </span>
                                                </td>
                                                <td>
                                                    <small>{{ $order->created_at->format('d/m/Y H:i') }}</small>
                                                </td>
                                                <td>
                                                    <a href="{{ route('admin.orders.show', $order->slug) }}" class="action-icon view-icon" title="Xem chi tiết">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            <div class="mt-2">
                                {{ $productOrders->appends(request()->except('product_orders'))->links('components.paginate') }}
                            </div>
                        </div>
                    </div>
                @endif

                @if($serviceOrders->count() > 0)
                    <div class="product-info-card mb-3">
                        <div class="card-header py-2 d-flex justify-content-between align-items-center">
                            <h3 class="card-title mb-0" style="font-size: 0.95rem; font-weight: 600;">
                                <i class="fas fa-concierge-bell"></i>
                                Đơn hàng dịch vụ ({{ $serviceOrders->total() }})
                            </h3>
                        </div>
                        <div class="card-body py-2">
                            <div class="data-table-container">
                                <table class="data-table">
                                    <thead>
                                        <tr class="text-center">
                                            <th class="column-stt">STT</th>
                                            <th>Mã đơn</th>
                                            <th>Người bán</th>
                                            <th>Dịch vụ</th>
                                            <th>Tổng tiền</th>
                                            <th>Trạng thái</th>
                                            <th>Ngày tạo</th>
                                            <th>Thao tác</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($serviceOrders as $key => $order)
                                            <tr>
                                                <td>{{ $serviceOrders->firstItem() + $key }}</td>
                                                <td>#{{ $order->slug }}</td>
                                                <td class="text-start">
                                                    <small>{{ $order->seller->full_name }}</small>
                                                </td>
                                                <td class="text-start">
                                                    <small>{{ $order->serviceVariant->service->name ?? 'N/A' }}</small>
                                                </td>
                                                <td class="text-primary">
                                                    <strong>{{ number_format($order->total_amount, 0, ',', '.') }}₫</strong>
                                                </td>
                                                <td>
                                                    <span class="status-badge bg-{{ $order->status->badgeColor() }} text-white">
                                                        {{ $order->status->label() }}
                                                    </span>
                                                </td>
                                                <td>
                                                    <small>{{ $order->created_at->format('d/m/Y H:i') }}</small>
                                                </td>
                                                <td>
                                                    <a href="{{ route('admin.service-orders.show', $order->slug) }}" class="action-icon view-icon" title="Xem chi tiết">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            <div class="mt-2">
                                {{ $serviceOrders->appends(request()->except('service_orders'))->links('components.paginate') }}
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Deposits -->
                @if($deposits->count() > 0)
                    <div class="product-info-card mb-3">
                        <div class="card-header py-2 d-flex justify-content-between align-items-center">
                            <h3 class="card-title mb-0" style="font-size: 0.95rem; font-weight: 600;">
                                <i class="fas fa-credit-card"></i>
                                Lịch sử nạp tiền ({{ $deposits->total() }})
                            </h3>
                        </div>
                        <div class="card-body py-2">
                            <div class="data-table-container">
                                <table class="data-table">
                                    <thead>
                                        <tr class="text-center">
                                            <th class="column-stt">STT</th>
                                            <th>Mã GD</th>
                                            <th>Số tiền</th>
                                            <th>Ngân hàng</th>
                                            <th>Trạng thái</th>
                                            <th>Ngày tạo</th>
                                            <th>Thao tác</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($deposits as $key => $deposit)
                                            <tr>
                                                <td>{{ $deposits->firstItem() + $key }}</td>
                                                <td>#{{ $deposit->slug }}</td>
                                                <td class="text-success">
                                                    <strong>{{ number_format($deposit->amount, 0, ',', '.') }}₫</strong>
                                                </td>
                                                <td>
                                                    <small>{{ $deposit->bank_name }}</small>
                                                </td>
                                                <td>
                                                    <span class="status-badge bg-{{ $deposit->status->badgeColor() }} text-white">
                                                        {{ $deposit->status->label() }}
                                                    </span>
                                                </td>
                                                <td>
                                                    <small>{{ $deposit->created_at->format('d/m/Y H:i') }}</small>
                                                </td>
                                                <td>
                                                    <a href="{{ route('admin.deposits.show', $deposit->slug) }}" class="action-icon view-icon" title="Xem chi tiết">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            <div class="mt-2">
                                {{ $deposits->appends(request()->except('deposits'))->links('components.paginate') }}
                            </div>
                        </div>
                    </div>
                @endif

                @if($user->role === 'seller' && $withdrawals && $withdrawals->count() > 0)
                    <div class="product-info-card mb-3">
                        <div class="card-header py-2 d-flex justify-content-between align-items-center">
                            <h3 class="card-title mb-0" style="font-size: 0.95rem; font-weight: 600;">
                                <i class="fas fa-money-check-alt"></i>
                                Lịch sử rút tiền ({{ $withdrawals->total() }})
                            </h3>
                        </div>
                        <div class="card-body py-2">
                            <div class="data-table-container">
                                <table class="data-table">
                                    <thead>
                                        <tr class="text-center">
                                            <th class="column-stt">STT</th>
                                            <th>Mã GD</th>
                                            <th>Số tiền</th>
                                            <th>Ngân hàng</th>
                                            <th>Trạng thái</th>
                                            <th>Ngày tạo</th>
                                            <th>Thao tác</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($withdrawals as $key => $withdrawal)
                                            <tr>
                                                <td>{{ $withdrawals->firstItem() + $key }}</td>
                                                <td>#{{ $withdrawal->slug }}</td>
                                                <td class="text-warning">
                                                    <strong>{{ number_format($withdrawal->amount, 0, ',', '.') }}₫</strong>
                                                </td>
                                                <td>
                                                    <small>{{ $withdrawal->bank_name }}</small>
                                                </td>
                                                <td>
                                                    <span class="status-badge bg-{{ $withdrawal->status->badgeColor() }} text-white">
                                                        {{ $withdrawal->status->label() }}
                                                    </span>
                                                </td>
                                                <td>
                                                    <small>{{ $withdrawal->created_at->format('d/m/Y H:i') }}</small>
                                                </td>
                                                <td>
                                                    <a href="{{ route('admin.withdrawals.show', $withdrawal->slug) }}" class="action-icon view-icon" title="Xem chi tiết">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            <div class="mt-2">
                                {{ $withdrawals->appends(request()->except('withdrawals'))->links('components.paginate') }}
                            </div>
                        </div>
                    </div>
                @endif

                @if($walletTransactions && $walletTransactions->count() > 0)
                    <div class="product-info-card mb-3">
                        <div class="card-header py-2 d-flex justify-content-between align-items-center">
                            <h3 class="card-title mb-0" style="font-size: 0.95rem; font-weight: 600;">
                                <i class="fas fa-wallet"></i>
                                Lịch sử giao dịch ví ({{ $walletTransactions->total() }})
                            </h3>
                        </div>
                        <div class="card-body py-2">
                            <div class="data-table-container">
                                <table class="data-table">
                                    <thead>
                                        <tr class="text-center">
                                            <th class="column-stt">STT</th>
                                            <th>Mã GD</th>
                                            <th>Loại</th>
                                            <th>Số tiền</th>
                                            <th>Số dư trước</th>
                                            <th>Số dư sau</th>
                                            <th>Trạng thái</th>
                                            <th>Ngày tạo</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($walletTransactions as $key => $transaction)
                                            @php
                                                $isPositive = in_array($transaction->type->value, ['deposit', 'refund', 'sale', 'commission']);
                                                $amountClass = $isPositive ? 'text-success' : 'text-danger';
                                            @endphp
                                            <tr>
                                                <td>{{ $walletTransactions->firstItem() + $key }}</td>
                                                <td>#{{ $transaction->slug }}</td>
                                                <td>
                                                    <small>{{ $transaction->type->label() }}</small>
                                                </td>
                                                <td class="{{ $amountClass }}">
                                                    <strong>{{ $isPositive ? '+' : '-' }}{{ number_format($transaction->amount, 0, ',', '.') }}₫</strong>
                                                </td>
                                                <td>
                                                    <small>{{ number_format($transaction->balance_before, 0, ',', '.') }}₫</small>
                                                </td>
                                                <td>
                                                    <small class="text-success">{{ number_format($transaction->balance_after, 0, ',', '.') }}₫</small>
                                                </td>
                                                <td>
                                                    <span class="status-badge bg-{{ $transaction->status->badgeColor() }} text-white">
                                                        {{ $transaction->status->label() }}
                                                    </span>
                                                </td>
                                                <td>
                                                    <small>{{ $transaction->created_at->format('d/m/Y H:i') }}</small>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            <div class="mt-2">
                                {{ $walletTransactions->appends(request()->except('transactions'))->links('components.paginate') }}
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
                            Thông tin chi tiết
                        </h3>
                    </div>
                    <div class="summary-body">
                        <div class="summary-item">
                            <span class="summary-label">Email:</span>
                            <span class="summary-value">{{ $user->email }}</span>
                        </div>
                        <div class="summary-item">
                            <span class="summary-label">Vai trò:</span>
                            <span class="summary-value">
                                @if($user->role === 'admin')
                                    <span class="badge bg-danger">Admin</span>
                                @elseif($user->role === 'seller')
                                    <span class="badge bg-primary">Seller</span>
                                @else
                                    <span class="badge bg-secondary">User</span>
                                @endif
                            </span>
                        </div>
                        <div class="summary-item">
                            <span class="summary-label">Trạng thái:</span>
                            <span class="summary-value">
                                @if($user->is_seller_banned)
                                    <span class="status-badge banned">Đã khóa</span>
                                @elseif($user->active)
                                    <span class="status-badge active">Hoạt động</span>
                                @else
                                    <span class="status-badge inactive">Chưa kích hoạt</span>
                                @endif
                            </span>
                        </div>
                        @if($user->wallet)
                            <div class="summary-item">
                                <span class="summary-label">Số dư ví:</span>
                                <span class="summary-value text-success">
                                    <strong>{{ number_format($user->wallet->balance, 0, ',', '.') }}₫</strong>
                                </span>
                            </div>
                        @endif
                        <div class="summary-item">
                            <span class="summary-label">Ngày tham gia:</span>
                            <span class="summary-value">{{ $user->created_at->format('d/m/Y H:i') }}</span>
                        </div>
                        @if($user->sellerRegistration)
                            <div class="summary-item">
                                <span class="summary-label">Đăng ký seller:</span>
                                <span class="summary-value">{{ $user->sellerRegistration->created_at->format('d/m/Y H:i') }}</span>
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
