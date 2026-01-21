@extends('seller.layouts.sidebar')

@section('title', 'Dashboard - Tổng quan')

@section('main-content')
    <div class="seller-dashboard">
        <!-- Header with Period Filter -->
        <div class="db-header">
            <div class="db-header-left">
                <h1 class="db-title">
                    <i class="fas fa-chart-line"></i>
                    Dashboard
                </h1>
                <p class="db-subtitle">Xin chào, <strong>{{ Auth::user()->full_name }}</strong>! Đây là tổng quan hoạt động của bạn.</p>
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

        <!-- Pending Actions Alert -->
        @if($pendingActions['total'] > 0)
        <div class="db-alert">
            <div class="db-alert-icon">
                <i class="fas fa-exclamation-triangle"></i>
            </div>
            <div class="db-alert-content">
                <strong>Bạn có {{ $pendingActions['total'] }} việc cần xử lý:</strong>
                <ul>
                    @if($pendingActions['pending_service_orders'] > 0)
                        <li><a href="{{ route('seller.service-orders.index') }}?status=paid">{{ $pendingActions['pending_service_orders'] }} đơn dịch vụ chờ xác nhận</a></li>
                    @endif
                    @if($pendingActions['product_disputes'] > 0)
                        <li><a href="{{ route('seller.refunds.index') }}">{{ $pendingActions['product_disputes'] }} khiếu nại sản phẩm chờ xử lý</a></li>
                    @endif
                    @if($pendingActions['service_disputes'] > 0)
                        <li><a href="{{ route('seller.service-disputes.index') }}">{{ $pendingActions['service_disputes'] }} khiếu nại dịch vụ chờ xử lý</a></li>
                    @endif
                </ul>
            </div>
        </div>
        @endif

        <!-- Stats Cards -->
        <div class="db-stats">
            <div class="db-stat-card earnings">
                <div class="db-stat-icon"><i class="fas fa-wallet"></i></div>
                <div class="db-stat-info">
                    <span class="db-stat-label">Doanh thu ({{ $dateRange['label'] }})</span>
                    <span class="db-stat-value">{{ number_format($stats['total_earnings'], 0, ',', '.') }}₫</span>
                    <span class="db-stat-sub">Tổng: {{ number_format($stats['all_time']['earnings'], 0, ',', '.') }}₫</span>
                </div>
            </div>

            <div class="db-stat-card orders">
                <div class="db-stat-icon"><i class="fas fa-shopping-cart"></i></div>
                <div class="db-stat-info">
                    <span class="db-stat-label">Đơn sản phẩm</span>
                    <span class="db-stat-value">{{ $stats['product_orders']['total'] }}</span>
                    <span class="db-stat-sub">
                        <span class="text-success">{{ $stats['product_orders']['completed'] }} hoàn thành</span>
                        @if($stats['product_orders']['disputed'] > 0)
                            | <span class="text-warning">{{ $stats['product_orders']['disputed'] }} tranh chấp</span>
                        @endif
                    </span>
                </div>
            </div>

            <div class="db-stat-card services">
                <div class="db-stat-icon"><i class="fas fa-concierge-bell"></i></div>
                <div class="db-stat-info">
                    <span class="db-stat-label">Đơn dịch vụ</span>
                    <span class="db-stat-value">{{ $stats['service_orders']['total'] }}</span>
                    <span class="db-stat-sub">
                        <span class="text-success">{{ $stats['service_orders']['completed'] }} hoàn thành</span>
                        @if($stats['service_orders']['disputed'] > 0)
                            | <span class="text-warning">{{ $stats['service_orders']['disputed'] }} tranh chấp</span>
                        @endif
                    </span>
                </div>
            </div>

            <div class="db-stat-card products">
                <div class="db-stat-icon"><i class="fas fa-box"></i></div>
                <div class="db-stat-info">
                    <span class="db-stat-label">Sản phẩm</span>
                    <span class="db-stat-value">{{ $stats['products_count'] }}</span>
                    <span class="db-stat-sub">Doanh thu: {{ number_format($stats['product_orders']['revenue'], 0, ',', '.') }}₫</span>
                </div>
            </div>

            <div class="db-stat-card service-count">
                <div class="db-stat-icon"><i class="fas fa-cogs"></i></div>
                <div class="db-stat-info">
                    <span class="db-stat-label">Dịch vụ</span>
                    <span class="db-stat-value">{{ $stats['services_count'] }}</span>
                    <span class="db-stat-sub">Doanh thu: {{ number_format($stats['service_orders']['revenue'], 0, ',', '.') }}₫</span>
                </div>
            </div>
        </div>

        <!-- Charts Section -->
        <div class="db-charts-section">
            <!-- Revenue Chart -->
            <div class="db-chart-card db-chart-main">
                <div class="db-chart-header">
                    <h3><i class="fas fa-chart-area"></i> Biểu đồ doanh thu</h3>
                    <div class="db-chart-controls">
                        <select id="revenueChartPeriod" class="form-select form-select-sm" onchange="loadRevenueChart()">
                            <option value="month">Theo tháng</option>
                            <option value="year">Theo năm</option>
                        </select>
                        <select id="revenueChartYear" class="form-select form-select-sm" onchange="loadRevenueChart()">
                            @for($y = date('Y'); $y >= date('Y') - 2; $y--)
                                <option value="{{ $y }}">{{ $y }}</option>
                            @endfor
                        </select>
                        <select id="revenueChartMonth" class="form-select form-select-sm" onchange="loadRevenueChart()">
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

            <!-- Orders Chart -->
            <div class="db-chart-card">
                <div class="db-chart-header">
                    <h3><i class="fas fa-chart-bar"></i> Số đơn hàng</h3>
                </div>
                <div class="db-chart-body">
                    <canvas id="ordersChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Top Products & Services -->
        <div class="db-top-section">
            <div class="db-chart-card">
                <div class="db-chart-header">
                    <h3><i class="fas fa-trophy"></i> Top sản phẩm bán chạy</h3>
                </div>
                <div class="db-chart-body">
                    <canvas id="topProductsChart" style="max-height: 200px;"></canvas>
                    <div id="topProductsList" class="db-top-list"></div>
                </div>
            </div>

            <div class="db-chart-card">
                <div class="db-chart-header">
                    <h3><i class="fas fa-star"></i> Top dịch vụ bán chạy</h3>
                </div>
                <div class="db-chart-body">
                    <canvas id="topServicesChart" style="max-height: 200px;"></canvas>
                    <div id="topServicesList" class="db-top-list"></div>
                </div>
            </div>
        </div>

        <!-- Recent Orders -->
        <div class="db-recent-section">
            <div class="db-recent-card">
                <div class="db-recent-header">
                    <h3><i class="fas fa-clock"></i> Đơn sản phẩm gần đây</h3>
                    <a href="{{ route('seller.orders.index') }}" class="db-view-all">Xem tất cả <i class="fas fa-arrow-right"></i></a>
                </div>
                <div class="db-recent-body">
                    @forelse($recentOrders as $order)
                        <div class="db-recent-item">
                            <div class="db-recent-info">
                                <a href="{{ route('seller.orders.show', $order->slug) }}" class="db-recent-id">#{{ $order->slug }}</a>
                                <span class="db-recent-buyer">{{ $order->buyer->full_name }}</span>
                            </div>
                            <div class="db-recent-meta">
                                <span class="db-recent-amount">{{ number_format($order->total_amount, 0, ',', '.') }}₫</span>
                                <span class="badge bg-{{ $order->status->badgeColor() }}">{{ $order->status->label() }}</span>
                            </div>
                            <div class="db-recent-time">{{ $order->created_at->diffForHumans() }}</div>
                        </div>
                    @empty
                        <div class="db-empty"><i class="fas fa-inbox"></i><p>Chưa có đơn hàng nào</p></div>
                    @endforelse
                </div>
            </div>

            <div class="db-recent-card">
                <div class="db-recent-header">
                    <h3><i class="fas fa-clock"></i> Đơn dịch vụ gần đây</h3>
                    <a href="{{ route('seller.service-orders.index') }}" class="db-view-all">Xem tất cả <i class="fas fa-arrow-right"></i></a>
                </div>
                <div class="db-recent-body">
                    @forelse($recentServiceOrders as $order)
                        <div class="db-recent-item">
                            <div class="db-recent-info">
                                <a href="{{ route('seller.service-orders.show', $order->slug) }}" class="db-recent-id">#{{ $order->slug }}</a>
                                <span class="db-recent-buyer">{{ $order->buyer->full_name }}</span>
                                @if($order->serviceVariant && $order->serviceVariant->service)
                                    <span class="db-recent-service">{{ Str::limit($order->serviceVariant->service->name, 30) }}</span>
                                @endif
                            </div>
                            <div class="db-recent-meta">
                                <span class="db-recent-amount">{{ number_format($order->total_amount, 0, ',', '.') }}₫</span>
                                <span class="badge bg-{{ $order->status->badgeColor() }}">{{ $order->status->label() }}</span>
                            </div>
                            <div class="db-recent-time">{{ $order->created_at->diffForHumans() }}</div>
                        </div>
                    @empty
                        <div class="db-empty"><i class="fas fa-inbox"></i><p>Chưa có đơn dịch vụ nào</p></div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
<style>
/* Seller Dashboard Styles */
.seller-dashboard {
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
    background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);
    border: 1px solid #f59e0b;
    border-radius: 12px;
    padding: 15px 20px;
    margin-bottom: 20px;
    display: flex;
    align-items: flex-start;
    gap: 15px;
}

.db-alert-icon {
    background: #f59e0b;
    color: white;
    width: 40px;
    height: 40px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}

.db-alert-content strong {
    color: #92400e;
    display: block;
    margin-bottom: 8px;
}

.db-alert-content ul {
    margin: 0;
    padding-left: 20px;
    color: #78350f;
}

.db-alert-content a {
    color: #92400e;
    text-decoration: underline;
}

/* Stats Grid */
.db-stats {
    display: grid;
    grid-template-columns: repeat(5, 1fr);
    gap: 15px;
    margin-bottom: 20px;
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

.db-stat-card.earnings .db-stat-icon { background: #dbeafe; color: #2563eb; }
.db-stat-card.orders .db-stat-icon { background: #d1fae5; color: #059669; }
.db-stat-card.services .db-stat-icon { background: #fce7f3; color: #db2777; }
.db-stat-card.products .db-stat-icon { background: #fef3c7; color: #d97706; }
.db-stat-card.service-count .db-stat-icon { background: #e0e7ff; color: #4f46e5; }

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

.db-stat-sub {
    display: block;
    font-size: 0.75rem;
    color: #9ca3af;
    margin-top: 4px;
}

.db-stat-sub .text-success { color: #059669; }
.db-stat-sub .text-warning { color: #d97706; }

/* Charts Section */
.db-charts-section {
    display: grid;
    grid-template-columns: 2fr 1fr;
    gap: 15px;
    margin-bottom: 20px;
}

.db-top-section {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 15px;
    margin-bottom: 20px;
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

/* Top List */
.db-top-list {
    margin-top: 15px;
    border-top: 1px solid #f3f4f6;
    padding-top: 15px;
}

.db-top-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 8px 0;
    border-bottom: 1px solid #f9fafb;
}

.db-top-item:last-child { border-bottom: none; }

.db-top-name {
    font-size: 0.85rem;
    color: #374151;
    flex: 1;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
    margin-right: 10px;
}

.db-top-value {
    font-size: 0.8rem;
    color: #6b7280;
    white-space: nowrap;
}

/* Recent Orders */
.db-recent-section {
    display: grid;
    grid-template-columns: 1fr 1fr;
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
    font-size: 0.95rem;
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
    display: flex;
    align-items: center;
    gap: 4px;
}

.db-view-all:hover { text-decoration: underline; }

.db-recent-body {
    max-height: 350px;
    overflow-y: auto;
}

.db-recent-item {
    padding: 12px 20px;
    border-bottom: 1px solid #f9fafb;
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 10px;
    flex-wrap: wrap;
}

.db-recent-item:last-child { border-bottom: none; }
.db-recent-item:hover { background: #f9fafb; }

.db-recent-info {
    flex: 1;
    min-width: 120px;
}

.db-recent-id {
    font-weight: 600;
    color: #3b82f6;
    text-decoration: none;
    font-size: 0.85rem;
}

.db-recent-id:hover { text-decoration: underline; }

.db-recent-buyer {
    display: block;
    font-size: 0.8rem;
    color: #6b7280;
}

.db-recent-service {
    display: block;
    font-size: 0.75rem;
    color: #9ca3af;
}

.db-recent-meta {
    text-align: right;
}

.db-recent-amount {
    display: block;
    font-weight: 600;
    color: #1f2937;
    font-size: 0.85rem;
}

.db-recent-time {
    font-size: 0.75rem;
    color: #9ca3af;
    white-space: nowrap;
}

.db-empty {
    padding: 40px 20px;
    text-align: center;
    color: #9ca3af;
}

.db-empty i {
    font-size: 2rem;
    margin-bottom: 10px;
    display: block;
}

/* Badges */
.badge {
    display: inline-block;
    padding: 2px 8px;
    border-radius: 9999px;
    font-size: 0.7rem;
    font-weight: 500;
    color: white;
}

.bg-success { background: #059669; }
.bg-warning { background: #d97706; }
.bg-danger { background: #dc2626; }
.bg-info { background: #0891b2; }
.bg-secondary { background: #6b7280; }

/* Responsive */
@media (max-width: 1400px) {
    .db-stats {
        grid-template-columns: repeat(3, 1fr);
    }
}

@media (max-width: 1200px) {
    .db-charts-section {
        grid-template-columns: 1fr;
    }
    .db-stats {
        grid-template-columns: repeat(2, 1fr);
    }
}

@media (max-width: 992px) {
    .db-top-section {
        grid-template-columns: 1fr;
    }
    .db-recent-section {
        grid-template-columns: 1fr;
    }
}

@media (max-width: 768px) {
    .seller-dashboard {
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
    .db-stats {
        grid-template-columns: 1fr;
    }
    .db-stat-card {
        padding: 15px;
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
    .db-recent-item {
        flex-direction: column;
        align-items: flex-start;
        gap: 8px;
    }
    .db-recent-meta {
        text-align: left;
        display: flex;
        align-items: center;
        gap: 10px;
    }
}
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
let revenueChart = null;
let ordersChart = null;
let topProductsChart = null;
let topServicesChart = null;

document.addEventListener('DOMContentLoaded', function() {
    loadRevenueChart();
    loadOrdersChart();
    loadTopProductsChart();
    loadTopServicesChart();
});

function changePeriod(period) {
    window.location.href = '{{ route("seller.dashboard") }}?period=' + period;
}

function loadRevenueChart() {
    const period = document.getElementById('revenueChartPeriod').value;
    const year = document.getElementById('revenueChartYear').value;
    const month = document.getElementById('revenueChartMonth').value;
    
    document.getElementById('revenueChartMonth').style.display = period === 'month' ? 'block' : 'none';
    
    fetch(`{{ route('seller.dashboard.chart-data') }}?type=revenue&period=${period}&year=${year}&month=${month}`)
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
                            ticks: {
                                callback: v => new Intl.NumberFormat('vi-VN', { notation: 'compact' }).format(v) + '₫'
                            }
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
    
    fetch(`{{ route('seller.dashboard.chart-data') }}?type=orders&period=${period}&year=${year}&month=${month}`)
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

function loadTopProductsChart() {
    fetch(`{{ route('seller.dashboard.chart-data') }}?type=products`)
        .then(response => response.json())
        .then(data => {
            const ctx = document.getElementById('topProductsChart').getContext('2d');
            
            if (topProductsChart) topProductsChart.destroy();
            
            if (data.labels.length === 0) {
                document.getElementById('topProductsList').innerHTML = '<div class="db-empty"><i class="fas fa-box-open"></i><p>Chưa có dữ liệu</p></div>';
                ctx.canvas.style.display = 'none';
                return;
            }
            
            ctx.canvas.style.display = 'block';
            topProductsChart = new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels: data.labels,
                    datasets: [{ data: data.data, backgroundColor: ['#3b82f6', '#10b981', '#f59e0b', '#ef4444', '#8b5cf6'] }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { display: false } }
                }
            });
            
            let html = '';
            for (let i = 0; i < data.labels.length; i++) {
                html += `<div class="db-top-item">
                    <span class="db-top-name">${i + 1}. ${data.labels[i]}</span>
                    <span class="db-top-value">${data.data[i]} bán | ${new Intl.NumberFormat('vi-VN').format(data.revenue[i])}₫</span>
                </div>`;
            }
            document.getElementById('topProductsList').innerHTML = html;
        });
}

function loadTopServicesChart() {
    fetch(`{{ route('seller.dashboard.chart-data') }}?type=services`)
        .then(response => response.json())
        .then(data => {
            const ctx = document.getElementById('topServicesChart').getContext('2d');
            
            if (topServicesChart) topServicesChart.destroy();
            
            if (data.labels.length === 0) {
                document.getElementById('topServicesList').innerHTML = '<div class="db-empty"><i class="fas fa-concierge-bell"></i><p>Chưa có dữ liệu</p></div>';
                ctx.canvas.style.display = 'none';
                return;
            }
            
            ctx.canvas.style.display = 'block';
            topServicesChart = new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels: data.labels,
                    datasets: [{ data: data.data, backgroundColor: ['#10b981', '#3b82f6', '#f59e0b', '#ef4444', '#8b5cf6'] }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { display: false } }
                }
            });
            
            let html = '';
            for (let i = 0; i < data.labels.length; i++) {
                html += `<div class="db-top-item">
                    <span class="db-top-name">${i + 1}. ${data.labels[i]}</span>
                    <span class="db-top-value">${data.data[i]} bán | ${new Intl.NumberFormat('vi-VN').format(data.revenue[i])}₫</span>
                </div>`;
            }
            document.getElementById('topServicesList').innerHTML = html;
        });
}
</script>
@endpush
