@extends('admin.layouts.sidebar')

@section('title', 'Dashboard - Tổng quan hệ thống')

@section('main-content')
    <div class="admin-dashboard">
        <!-- Header -->
        <div class="db-header">
            <div class="db-header-left">
                <h1 class="db-title">
                    <i class="fas fa-tachometer-alt"></i>
                    Dashboard Admin
                </h1>
                <p class="db-subtitle">Tổng quan hoạt động hệ thống - {{ $dateRange['label'] }}</p>
            </div>
            <div class="db-header-right">
                <select id="periodFilter" class="form-select" onchange="changePeriod(this.value)">
                    <option value="today" {{ $period === 'today' ? 'selected' : '' }}>Hôm nay</option>
                    <option value="week" {{ $period === 'week' ? 'selected' : '' }}>Tuần này</option>
                    <option value="month" {{ $period === 'month' ? 'selected' : '' }}>Tháng này</option>
                    <option value="year" {{ $period === 'year' ? 'selected' : '' }}>Năm nay</option>
                    <option value="all" {{ $period === 'all' ? 'selected' : '' }}>Tất cả</option>
                </select>
            </div>
        </div>

        <!-- Pending Actions -->
        @php $totalPending = array_sum($pendingItems); @endphp
        @if($totalPending > 0)
        <div class="db-alert db-alert-info">
            <div class="db-alert-icon"><i class="fas fa-bell"></i></div>
            <div class="db-alert-content">
                <strong>Có {{ $totalPending }} việc cần xử lý:</strong>
                <div class="db-alert-items">
                    @if($pendingItems['seller_registrations'] > 0)
                        <a href="{{ route('admin.seller-registrations.index') }}" class="db-alert-item">
                            <i class="fas fa-user-plus"></i> {{ $pendingItems['seller_registrations'] }} đăng ký seller
                        </a>
                    @endif
                    @if($pendingItems['pending_products'] > 0)
                        <a href="{{ route('admin.products.pending') }}" class="db-alert-item">
                            <i class="fas fa-box"></i> {{ $pendingItems['pending_products'] }} sản phẩm chờ duyệt
                        </a>
                    @endif
                    @if($pendingItems['pending_services'] > 0)
                        <a href="{{ route('admin.services.pending') }}" class="db-alert-item">
                            <i class="fas fa-cogs"></i> {{ $pendingItems['pending_services'] }} dịch vụ chờ duyệt
                        </a>
                    @endif
                    @if($pendingItems['product_disputes'] > 0)
                        <a href="{{ route('admin.disputes.index') }}?status=reviewing" class="db-alert-item">
                            <i class="fas fa-exclamation-triangle"></i> {{ $pendingItems['product_disputes'] }} khiếu nại SP
                        </a>
                    @endif
                    @if($pendingItems['service_disputes'] > 0)
                        <a href="{{ route('admin.service-disputes.index') }}?status=reviewing" class="db-alert-item">
                            <i class="fas fa-exclamation-circle"></i> {{ $pendingItems['service_disputes'] }} khiếu nại DV
                        </a>
                    @endif
                    @if($pendingItems['pending_withdrawals'] > 0)
                        <a href="{{ route('admin.withdrawals.index') }}" class="db-alert-item">
                            <i class="fas fa-money-bill-wave"></i> {{ $pendingItems['pending_withdrawals'] }} yêu cầu rút tiền
                        </a>
                    @endif
                </div>
            </div>
        </div>
        @endif

        <!-- Main Stats -->
        <div class="db-stats db-stats-main">
            <div class="db-stat-card commission">
                <div class="db-stat-icon"><i class="fas fa-coins"></i></div>
                <div class="db-stat-info">
                    <span class="db-stat-label">Phí sàn thu được</span>
                    <span class="db-stat-value">{{ number_format($stats['commission'], 0, ',', '.') }}₫</span>
                    <span class="db-stat-sub">Tổng: {{ number_format($stats['all_time']['commission'], 0, ',', '.') }}₫</span>
                </div>
            </div>

            <div class="db-stat-card revenue">
                <div class="db-stat-icon"><i class="fas fa-chart-line"></i></div>
                <div class="db-stat-info">
                    <span class="db-stat-label">Tổng doanh số</span>
                    <span class="db-stat-value">{{ number_format($stats['product_orders']['completed_amount'] + $stats['service_orders']['completed_amount'], 0, ',', '.') }}₫</span>
                    <span class="db-stat-sub">SP: {{ number_format($stats['product_orders']['completed_amount'], 0, ',', '.') }}₫ | DV: {{ number_format($stats['service_orders']['completed_amount'], 0, ',', '.') }}₫</span>
                </div>
            </div>

            <div class="db-stat-card orders">
                <div class="db-stat-icon"><i class="fas fa-shopping-bag"></i></div>
                <div class="db-stat-info">
                    <span class="db-stat-label">Tổng đơn hàng</span>
                    <span class="db-stat-value">{{ $stats['product_orders']['total'] + $stats['service_orders']['total'] }}</span>
                    <span class="db-stat-sub">SP: {{ $stats['product_orders']['total'] }} | DV: {{ $stats['service_orders']['total'] }}</span>
                </div>
            </div>

            <div class="db-stat-card users">
                <div class="db-stat-icon"><i class="fas fa-users"></i></div>
                <div class="db-stat-info">
                    <span class="db-stat-label">Người dùng</span>
                    <span class="db-stat-value">{{ number_format($stats['users']['total']) }}</span>
                    <span class="db-stat-sub">Sellers: {{ $stats['users']['sellers'] }} | Mới: +{{ $stats['users']['new'] }}</span>
                </div>
            </div>
        </div>

        <!-- Secondary Stats -->
        <div class="db-stats db-stats-secondary">
            <div class="db-stat-card small products">
                <div class="db-stat-icon-sm"><i class="fas fa-box"></i></div>
                <div class="db-stat-info">
                    <span class="db-stat-label">Sản phẩm</span>
                    <span class="db-stat-value-sm">{{ $stats['products']['total'] }}</span>
                    <span class="db-stat-sub">Active: {{ $stats['products']['active'] }}</span>
                </div>
            </div>

            <div class="db-stat-card small services">
                <div class="db-stat-icon-sm"><i class="fas fa-concierge-bell"></i></div>
                <div class="db-stat-info">
                    <span class="db-stat-label">Dịch vụ</span>
                    <span class="db-stat-value-sm">{{ $stats['services']['total'] }}</span>
                    <span class="db-stat-sub">Active: {{ $stats['services']['active'] }}</span>
                </div>
            </div>

            <div class="db-stat-card small deposits">
                <div class="db-stat-icon-sm"><i class="fas fa-arrow-down"></i></div>
                <div class="db-stat-info">
                    <span class="db-stat-label">Nạp tiền</span>
                    <span class="db-stat-value-sm">{{ number_format($stats['deposits']['amount'], 0, ',', '.') }}₫</span>
                    <span class="db-stat-sub">{{ $stats['deposits']['total'] }} giao dịch</span>
                </div>
            </div>

            <div class="db-stat-card small withdrawals">
                <div class="db-stat-icon-sm"><i class="fas fa-arrow-up"></i></div>
                <div class="db-stat-info">
                    <span class="db-stat-label">Rút tiền</span>
                    <span class="db-stat-value-sm">{{ number_format($stats['withdrawals']['amount'], 0, ',', '.') }}₫</span>
                    <span class="db-stat-sub">{{ $stats['withdrawals']['total'] }} giao dịch</span>
                </div>
            </div>

            <div class="db-stat-card small completed">
                <div class="db-stat-icon-sm"><i class="fas fa-check-circle"></i></div>
                <div class="db-stat-info">
                    <span class="db-stat-label">Đơn hoàn thành</span>
                    <span class="db-stat-value-sm">{{ $stats['product_orders']['completed'] + $stats['service_orders']['completed'] }}</span>
                    <span class="db-stat-sub">SP: {{ $stats['product_orders']['completed'] }} | DV: {{ $stats['service_orders']['completed'] }}</span>
                </div>
            </div>

            <div class="db-stat-card small disputed">
                <div class="db-stat-icon-sm"><i class="fas fa-exclamation-triangle"></i></div>
                <div class="db-stat-info">
                    <span class="db-stat-label">Đang tranh chấp</span>
                    <span class="db-stat-value-sm">{{ $stats['product_orders']['disputed'] + $stats['service_orders']['disputed'] }}</span>
                    <span class="db-stat-sub">SP: {{ $stats['product_orders']['disputed'] }} | DV: {{ $stats['service_orders']['disputed'] }}</span>
                </div>
            </div>
        </div>

        <!-- Charts Row 1 -->
        <div class="db-charts-section">
            <div class="db-chart-card db-chart-main">
                <div class="db-chart-header">
                    <h3><i class="fas fa-chart-area"></i> Biểu đồ doanh thu & Phí sàn</h3>
                    <div class="db-chart-controls">
                        <select id="revenueChartPeriod" class="form-select form-select-sm" onchange="loadAllCharts()">
                            <option value="month">Theo tháng</option>
                            <option value="year">Theo năm</option>
                        </select>
                        <select id="revenueChartYear" class="form-select form-select-sm" onchange="loadAllCharts()">
                            @for($y = date('Y'); $y >= date('Y') - 2; $y--)
                                <option value="{{ $y }}">{{ $y }}</option>
                            @endfor
                        </select>
                        <select id="revenueChartMonth" class="form-select form-select-sm" onchange="loadAllCharts()">
                            @for($m = 1; $m <= 12; $m++)
                                <option value="{{ $m }}" {{ $m == date('m') ? 'selected' : '' }}>Tháng {{ $m }}</option>
                            @endfor
                        </select>
                    </div>
                </div>
                <div class="db-chart-body">
                    <canvas id="revenueChart"></canvas>
                </div>
            </div>

            <div class="db-chart-card">
                <div class="db-chart-header">
                    <h3><i class="fas fa-chart-bar"></i> Số đơn hàng</h3>
                </div>
                <div class="db-chart-body">
                    <canvas id="ordersChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Charts Row 2 -->
        <div class="db-charts-section db-charts-triple">
            <div class="db-chart-card">
                <div class="db-chart-header">
                    <h3><i class="fas fa-user-plus"></i> Người dùng mới</h3>
                </div>
                <div class="db-chart-body">
                    <canvas id="usersChart"></canvas>
                </div>
            </div>

            <div class="db-chart-card">
                <div class="db-chart-header">
                    <h3><i class="fas fa-exchange-alt"></i> Nạp / Rút tiền</h3>
                </div>
                <div class="db-chart-body">
                    <canvas id="depositsChart"></canvas>
                </div>
            </div>

            <div class="db-chart-card">
                <div class="db-chart-header">
                    <h3><i class="fas fa-trophy"></i> Top Sellers</h3>
                </div>
                <div class="db-chart-body" style="padding: 0;">
                    <div id="topSellersList" class="db-top-list-full"></div>
                </div>
            </div>
        </div>

        <!-- Recent Activities -->
        <div class="db-recent-grid">
            <div class="db-recent-card">
                <div class="db-recent-header">
                    <h3><i class="fas fa-shopping-cart"></i> Đơn sản phẩm gần đây</h3>
                    <a href="{{ route('admin.orders.index') }}" class="db-view-all">Xem tất cả</a>
                </div>
                <div class="db-recent-body">
                    @forelse($recentOrders as $order)
                        <div class="db-recent-item">
                            <div class="db-recent-info">
                                <a href="{{ route('admin.orders.show', $order->slug) }}" class="db-recent-id">#{{ $order->slug }}</a>
                                <span class="db-recent-users">{{ $order->buyer->full_name }} → {{ $order->seller->full_name }}</span>
                            </div>
                            <div class="db-recent-meta">
                                <span class="db-recent-amount">{{ number_format($order->total_amount, 0, ',', '.') }}₫</span>
                                <span class="badge bg-{{ $order->status->badgeColor() }}">{{ $order->status->label() }}</span>
                            </div>
                        </div>
                    @empty
                        <div class="db-empty"><i class="fas fa-inbox"></i><p>Chưa có đơn hàng</p></div>
                    @endforelse
                </div>
            </div>

            <div class="db-recent-card">
                <div class="db-recent-header">
                    <h3><i class="fas fa-concierge-bell"></i> Đơn dịch vụ gần đây</h3>
                    <a href="{{ route('admin.service-orders.index') }}" class="db-view-all">Xem tất cả</a>
                </div>
                <div class="db-recent-body">
                    @forelse($recentServiceOrders as $order)
                        <div class="db-recent-item">
                            <div class="db-recent-info">
                                <a href="{{ route('admin.service-orders.show', $order->slug) }}" class="db-recent-id">#{{ $order->slug }}</a>
                                <span class="db-recent-users">{{ $order->buyer->full_name }} → {{ $order->seller->full_name }}</span>
                            </div>
                            <div class="db-recent-meta">
                                <span class="db-recent-amount">{{ number_format($order->total_amount, 0, ',', '.') }}₫</span>
                                <span class="badge bg-{{ $order->status->badgeColor() }}">{{ $order->status->label() }}</span>
                            </div>
                        </div>
                    @empty
                        <div class="db-empty"><i class="fas fa-inbox"></i><p>Chưa có đơn dịch vụ</p></div>
                    @endforelse
                </div>
            </div>

            <div class="db-recent-card">
                <div class="db-recent-header">
                    <h3><i class="fas fa-arrow-circle-down"></i> Nạp tiền gần đây</h3>
                    <a href="{{ route('admin.deposits.index') }}" class="db-view-all">Xem tất cả</a>
                </div>
                <div class="db-recent-body">
                    @forelse($recentDeposits as $deposit)
                        <div class="db-recent-item">
                            <div class="db-recent-info">
                                <a href="{{ route('admin.deposits.show', $deposit->slug) }}" class="db-recent-id">#{{ $deposit->slug }}</a>
                                <span class="db-recent-users">{{ $deposit->user->full_name }}</span>
                            </div>
                            <div class="db-recent-meta">
                                <span class="db-recent-amount text-success">+{{ number_format($deposit->amount, 0, ',', '.') }}₫</span>
                                <span class="badge bg-{{ $deposit->status->badgeColor() }}">{{ $deposit->status->label() }}</span>
                            </div>
                        </div>
                    @empty
                        <div class="db-empty"><i class="fas fa-inbox"></i><p>Chưa có giao dịch</p></div>
                    @endforelse
                </div>
            </div>

            <div class="db-recent-card">
                <div class="db-recent-header">
                    <h3><i class="fas fa-arrow-circle-up"></i> Rút tiền gần đây</h3>
                    <a href="{{ route('admin.withdrawals.index') }}" class="db-view-all">Xem tất cả</a>
                </div>
                <div class="db-recent-body">
                    @forelse($recentWithdrawals as $withdrawal)
                        <div class="db-recent-item">
                            <div class="db-recent-info">
                                <a href="{{ route('admin.withdrawals.show', $withdrawal->slug) }}" class="db-recent-id">#{{ $withdrawal->slug }}</a>
                                <span class="db-recent-users">{{ $withdrawal->user->full_name }}</span>
                            </div>
                            <div class="db-recent-meta">
                                <span class="db-recent-amount text-danger">-{{ number_format($withdrawal->amount, 0, ',', '.') }}₫</span>
                                <span class="badge bg-{{ $withdrawal->status->badgeColor() }}">{{ $withdrawal->status->label() }}</span>
                            </div>
                        </div>
                    @empty
                        <div class="db-empty"><i class="fas fa-inbox"></i><p>Chưa có yêu cầu</p></div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
<style>
/* Admin Dashboard Styles */
.admin-dashboard {
    padding: 20px;
    width: 100%;
    box-sizing: border-box;
}

/* Header */
.db-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 20px;
    flex-wrap: wrap;
    gap: 15px;
}

.db-title {
    font-size: 1.5rem;
    font-weight: 700;
    color: #1f2937;
    margin: 0;
    display: flex;
    align-items: center;
    gap: 10px;
}

.db-title i { color: #3b82f6; }

.db-subtitle {
    color: #6b7280;
    margin: 5px 0 0;
    font-size: 0.9rem;
}

.db-header-right .form-select {
    min-width: 140px;
    border-radius: 8px;
}

/* Alert */
.db-alert {
    border-radius: 12px;
    padding: 15px 20px;
    margin-bottom: 20px;
    display: flex;
    align-items: flex-start;
    gap: 15px;
}

.db-alert-info {
    background: linear-gradient(135deg, #dbeafe 0%, #bfdbfe 100%);
    border: 1px solid #3b82f6;
}

.db-alert-icon {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
    background: #3b82f6;
    color: white;
}

.db-alert-content strong {
    color: #1e40af;
    display: block;
    margin-bottom: 10px;
}

.db-alert-items {
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
}

.db-alert-item {
    background: white;
    padding: 6px 12px;
    border-radius: 6px;
    font-size: 0.85rem;
    color: #1e40af;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 6px;
    transition: all 0.2s;
}

.db-alert-item:hover {
    background: #1e40af;
    color: white;
}

/* Stats */
.db-stats {
    display: grid;
    gap: 15px;
    margin-bottom: 20px;
}

.db-stats-main {
    grid-template-columns: repeat(4, 1fr);
}

.db-stats-secondary {
    grid-template-columns: repeat(6, 1fr);
}

.db-stat-card {
    background: white;
    border-radius: 12px;
    padding: 20px;
    display: flex;
    align-items: flex-start;
    gap: 15px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.1);
    transition: transform 0.2s, box-shadow 0.2s;
}

.db-stat-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
}

.db-stat-card.small {
    padding: 15px;
}

.db-stat-icon {
    width: 48px;
    height: 48px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.25rem;
    flex-shrink: 0;
}

.db-stat-icon-sm {
    width: 40px;
    height: 40px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1rem;
    flex-shrink: 0;
}

.db-stat-card.commission .db-stat-icon, .db-stat-card.commission .db-stat-icon-sm { background: #fef3c7; color: #d97706; }
.db-stat-card.revenue .db-stat-icon, .db-stat-card.revenue .db-stat-icon-sm { background: #d1fae5; color: #059669; }
.db-stat-card.orders .db-stat-icon, .db-stat-card.orders .db-stat-icon-sm { background: #dbeafe; color: #2563eb; }
.db-stat-card.users .db-stat-icon, .db-stat-card.users .db-stat-icon-sm { background: #e0e7ff; color: #4f46e5; }
.db-stat-card.products .db-stat-icon-sm { background: #fce7f3; color: #db2777; }
.db-stat-card.services .db-stat-icon-sm { background: #f3e8ff; color: #9333ea; }
.db-stat-card.deposits .db-stat-icon-sm { background: #d1fae5; color: #059669; }
.db-stat-card.withdrawals .db-stat-icon-sm { background: #fee2e2; color: #dc2626; }
.db-stat-card.completed .db-stat-icon-sm { background: #d1fae5; color: #059669; }
.db-stat-card.disputed .db-stat-icon-sm { background: #fef3c7; color: #d97706; }

.db-stat-info {
    flex: 1;
    min-width: 0;
}

.db-stat-label {
    display: block;
    font-size: 0.8rem;
    color: #6b7280;
    margin-bottom: 4px;
}

.db-stat-value {
    display: block;
    font-size: 1.4rem;
    font-weight: 700;
    color: #1f2937;
    line-height: 1.2;
    word-break: break-word;
}

.db-stat-value-sm {
    display: block;
    font-size: 1.2rem;
    font-weight: 700;
    color: #1f2937;
    line-height: 1.2;
}

.db-stat-sub {
    display: block;
    font-size: 0.75rem;
    color: #9ca3af;
    margin-top: 4px;
}

/* Charts */
.db-charts-section {
    display: grid;
    grid-template-columns: 2fr 1fr;
    gap: 15px;
    margin-bottom: 20px;
}

.db-charts-triple {
    grid-template-columns: repeat(3, 1fr);
}

.db-chart-card {
    background: white;
    border-radius: 12px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.1);
    overflow: hidden;
}

.db-chart-header {
    padding: 15px 20px;
    border-bottom: 1px solid #f3f4f6;
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 10px;
}

.db-chart-header h3 {
    font-size: 0.95rem;
    font-weight: 600;
    color: #374151;
    margin: 0;
    display: flex;
    align-items: center;
    gap: 8px;
}

.db-chart-header h3 i { color: #6b7280; }

.db-chart-controls {
    display: flex;
    gap: 8px;
    flex-wrap: wrap;
}

.db-chart-controls .form-select-sm {
    padding: 4px 8px;
    font-size: 0.8rem;
    border-radius: 6px;
}

.db-chart-body {
    padding: 15px;
    min-height: 280px;
    position: relative;
}

/* Top Sellers List */
.db-top-list-full {
    padding: 15px 20px;
}

.db-top-item {
    display: flex;
    align-items: center;
    padding: 12px 0;
    border-bottom: 1px solid #f3f4f6;
}

.db-top-item:last-child { border-bottom: none; }

.db-top-rank {
    width: 28px;
    height: 28px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 0.75rem;
    font-weight: 600;
    margin-right: 12px;
    flex-shrink: 0;
}

.db-top-rank.gold { background: #fef3c7; color: #d97706; }
.db-top-rank.silver { background: #e5e7eb; color: #6b7280; }
.db-top-rank.bronze { background: #fed7aa; color: #c2410c; }
.db-top-rank.default { background: #f3f4f6; color: #9ca3af; }

.db-top-info {
    flex: 1;
    min-width: 0;
}

.db-top-name {
    font-size: 0.85rem;
    font-weight: 500;
    color: #374151;
    display: block;
}

.db-top-stats {
    font-size: 0.75rem;
    color: #9ca3af;
}

.db-top-value {
    font-size: 0.85rem;
    font-weight: 600;
    color: #059669;
    white-space: nowrap;
}

/* Recent Grid */
.db-recent-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 15px;
}

.db-recent-card {
    background: white;
    border-radius: 12px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.1);
    overflow: hidden;
}

.db-recent-header {
    padding: 15px 20px;
    border-bottom: 1px solid #f3f4f6;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.db-recent-header h3 {
    font-size: 0.9rem;
    font-weight: 600;
    color: #374151;
    margin: 0;
    display: flex;
    align-items: center;
    gap: 8px;
}

.db-view-all {
    font-size: 0.8rem;
    color: #3b82f6;
    text-decoration: none;
}

.db-view-all:hover { text-decoration: underline; }

.db-recent-body {
    max-height: 320px;
    overflow-y: auto;
}

.db-recent-item {
    padding: 12px 20px;
    border-bottom: 1px solid #f9fafb;
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 8px;
}

.db-recent-item:last-child { border-bottom: none; }
.db-recent-item:hover { background: #f9fafb; }

.db-recent-info {
    flex: 1;
    min-width: 0;
}

.db-recent-id {
    font-weight: 600;
    color: #3b82f6;
    text-decoration: none;
    font-size: 0.8rem;
}

.db-recent-id:hover { text-decoration: underline; }

.db-recent-users {
    display: block;
    font-size: 0.75rem;
    color: #6b7280;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.db-recent-meta {
    text-align: right;
    flex-shrink: 0;
}

.db-recent-amount {
    display: block;
    font-weight: 600;
    color: #1f2937;
    font-size: 0.8rem;
}

.db-recent-amount.text-success { color: #059669; }
.db-recent-amount.text-danger { color: #dc2626; }

.db-empty {
    padding: 40px 20px;
    text-align: center;
    color: #9ca3af;
}

.db-empty i {
    font-size: 1.5rem;
    margin-bottom: 10px;
    display: block;
}

/* Badges */
.badge {
    display: inline-block;
    padding: 2px 8px;
    border-radius: 9999px;
    font-size: 0.65rem;
    font-weight: 500;
    color: white;
}

.bg-success { background: #059669; }
.bg-warning { background: #d97706; }
.bg-danger { background: #dc2626; }
.bg-info { background: #0891b2; }
.bg-secondary { background: #6b7280; }
.bg-primary { background: #3b82f6; }

/* Responsive */
@media (max-width: 1400px) {
    .db-stats-main {
        grid-template-columns: repeat(2, 1fr);
    }
    .db-stats-secondary {
        grid-template-columns: repeat(3, 1fr);
    }
    .db-recent-grid {
        grid-template-columns: repeat(2, 1fr);
    }
}

@media (max-width: 1200px) {
    .db-charts-section {
        grid-template-columns: 1fr;
    }
    .db-charts-triple {
        grid-template-columns: 1fr 1fr;
    }
}

@media (max-width: 992px) {
    .db-charts-triple {
        grid-template-columns: 1fr;
    }
    .db-stats-secondary {
        grid-template-columns: repeat(2, 1fr);
    }
}

@media (max-width: 768px) {
    .admin-dashboard {
        padding: 15px;
    }
    .db-header {
        flex-direction: column;
        align-items: stretch;
    }
    .db-header-right {
        width: 100%;
    }
    .db-header-right .form-select {
        width: 100%;
    }
    .db-stats-main, .db-stats-secondary {
        grid-template-columns: 1fr;
    }
    .db-recent-grid {
        grid-template-columns: 1fr;
    }
    .db-chart-header {
        flex-direction: column;
        align-items: flex-start;
    }
    .db-chart-controls {
        width: 100%;
    }
    .db-chart-controls .form-select-sm {
        flex: 1;
    }
    .db-alert-items {
        flex-direction: column;
    }
    .db-alert-item {
        width: 100%;
        justify-content: flex-start;
    }
}
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
let revenueChart = null;
let ordersChart = null;
let usersChart = null;
let depositsChart = null;

document.addEventListener('DOMContentLoaded', function() {
    loadAllCharts();
    loadTopSellers();
});

function changePeriod(period) {
    window.location.href = '{{ route("admin.dashboard") }}?period=' + period;
}

function loadAllCharts() {
    const period = document.getElementById('revenueChartPeriod').value;
    document.getElementById('revenueChartMonth').style.display = period === 'month' ? 'block' : 'none';
    
    loadRevenueChart();
    loadOrdersChart();
    loadUsersChart();
    loadDepositsChart();
}

function loadRevenueChart() {
    const period = document.getElementById('revenueChartPeriod').value;
    const year = document.getElementById('revenueChartYear').value;
    const month = document.getElementById('revenueChartMonth').value;
    
    fetch(`{{ route('admin.dashboard.chart-data') }}?type=revenue&period=${period}&year=${year}&month=${month}`)
        .then(response => response.json())
        .then(data => {
            const ctx = document.getElementById('revenueChart').getContext('2d');
            if (revenueChart) revenueChart.destroy();
            
            revenueChart = new Chart(ctx, {
                type: 'line',
                data: { labels: data.labels, datasets: data.datasets },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { position: 'top' },
                        tooltip: {
                            callbacks: {
                                label: ctx => ctx.dataset.label + ': ' + new Intl.NumberFormat('vi-VN').format(ctx.raw) + '₫'
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: { callback: v => new Intl.NumberFormat('vi-VN', { notation: 'compact' }).format(v) + '₫' }
                        }
                    }
                }
            });
        });
}

function loadOrdersChart() {
    const period = document.getElementById('revenueChartPeriod').value;
    const year = document.getElementById('revenueChartYear').value;
    const month = document.getElementById('revenueChartMonth').value;
    
    fetch(`{{ route('admin.dashboard.chart-data') }}?type=orders&period=${period}&year=${year}&month=${month}`)
        .then(response => response.json())
        .then(data => {
            const ctx = document.getElementById('ordersChart').getContext('2d');
            if (ordersChart) ordersChart.destroy();
            
            ordersChart = new Chart(ctx, {
                type: 'bar',
                data: { labels: data.labels, datasets: data.datasets },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { position: 'top' } },
                    scales: { y: { beginAtZero: true, ticks: { stepSize: 1 } } }
                }
            });
        });
}

function loadUsersChart() {
    const period = document.getElementById('revenueChartPeriod').value;
    const year = document.getElementById('revenueChartYear').value;
    const month = document.getElementById('revenueChartMonth').value;
    
    fetch(`{{ route('admin.dashboard.chart-data') }}?type=users&period=${period}&year=${year}&month=${month}`)
        .then(response => response.json())
        .then(data => {
            const ctx = document.getElementById('usersChart').getContext('2d');
            if (usersChart) usersChart.destroy();
            
            usersChart = new Chart(ctx, {
                type: 'bar',
                data: { labels: data.labels, datasets: data.datasets },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { position: 'top' } },
                    scales: { y: { beginAtZero: true, ticks: { stepSize: 1 } } }
                }
            });
        });
}

function loadDepositsChart() {
    const period = document.getElementById('revenueChartPeriod').value;
    const year = document.getElementById('revenueChartYear').value;
    const month = document.getElementById('revenueChartMonth').value;
    
    fetch(`{{ route('admin.dashboard.chart-data') }}?type=deposits&period=${period}&year=${year}&month=${month}`)
        .then(response => response.json())
        .then(data => {
            const ctx = document.getElementById('depositsChart').getContext('2d');
            if (depositsChart) depositsChart.destroy();
            
            depositsChart = new Chart(ctx, {
                type: 'bar',
                data: { labels: data.labels, datasets: data.datasets },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { position: 'top' } },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: { callback: v => new Intl.NumberFormat('vi-VN', { notation: 'compact' }).format(v) + '₫' }
                        }
                    }
                }
            });
        });
}

function loadTopSellers() {
    fetch(`{{ route('admin.dashboard.chart-data') }}?type=top_sellers`)
        .then(response => response.json())
        .then(data => {
            let html = '';
            if (data.labels.length === 0) {
                html = '<div class="db-empty"><i class="fas fa-users"></i><p>Chưa có dữ liệu</p></div>';
            } else {
                for (let i = 0; i < data.labels.length; i++) {
                    const rankClass = i === 0 ? 'gold' : (i === 1 ? 'silver' : (i === 2 ? 'bronze' : 'default'));
                    html += `<div class="db-top-item">
                        <span class="db-top-rank ${rankClass}">${i + 1}</span>
                        <div class="db-top-info">
                            <span class="db-top-name">${data.labels[i]}</span>
                            <span class="db-top-stats">${data.orders[i]} đơn hàng</span>
                        </div>
                        <span class="db-top-value">${new Intl.NumberFormat('vi-VN').format(data.revenue[i])}₫</span>
                    </div>`;
                }
            }
            document.getElementById('topSellersList').innerHTML = html;
        });
}
</script>
@endpush
