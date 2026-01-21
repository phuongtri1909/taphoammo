<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Order;
use App\Models\ServiceOrder;
use App\Models\Product;
use App\Models\Service;
use App\Models\Deposit;
use App\Models\Withdrawal;
use App\Models\Dispute;
use App\Models\ServiceDispute;
use App\Models\SellerRegistration;
use App\Models\WalletTransaction;
use App\Enums\OrderStatus;
use App\Enums\ServiceOrderStatus;
use App\Enums\DisputeStatus;
use App\Enums\ServiceDisputeStatus;
use App\Enums\DepositStatus;
use App\Enums\WithdrawalStatus;
use App\Enums\ProductStatus;
use App\Enums\ServiceStatus;
use App\Enums\WalletTransactionType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $period = $request->get('period', 'month');
        $dateRange = $this->getDateRange($period);
        
        $cacheKey = "admin_dashboard_{$period}";
        $cacheTTL = 300; // 5 minutes
        
        $stats = Cache::remember($cacheKey, $cacheTTL, function () use ($dateRange) {
            return $this->getStats($dateRange);
        });
        
        // Real-time pending items
        $pendingItems = $this->getPendingItems();
        
        // Recent activities
        $recentOrders = $this->getRecentOrders(5);
        $recentServiceOrders = $this->getRecentServiceOrders(5);
        $recentDeposits = $this->getRecentDeposits(5);
        $recentWithdrawals = $this->getRecentWithdrawals(5);
        
        return view('admin.pages.dashboard', compact(
            'stats',
            'pendingItems',
            'recentOrders',
            'recentServiceOrders',
            'recentDeposits',
            'recentWithdrawals',
            'period',
            'dateRange'
        ));
    }
    
    /**
     * API endpoint for chart data (AJAX)
     */
    public function chartData(Request $request)
    {
        $type = $request->get('type', 'revenue');
        $period = $request->get('period', 'month');
        $year = $request->get('year', date('Y'));
        $month = $request->get('month', date('m'));
        
        $data = [];
        
        switch ($type) {
            case 'revenue':
                $data = $this->getRevenueChartData($period, $year, $month);
                break;
            case 'orders':
                $data = $this->getOrdersChartData($period, $year, $month);
                break;
            case 'users':
                $data = $this->getUsersChartData($period, $year, $month);
                break;
            case 'deposits':
                $data = $this->getDepositsChartData($period, $year, $month);
                break;
            case 'top_sellers':
                $data = $this->getTopSellersData();
                break;
            case 'order_status':
                $data = $this->getOrderStatusData();
                break;
        }
        
        return response()->json($data);
    }
    
    private function getDateRange(string $period): array
    {
        $now = Carbon::now();
        
        return match ($period) {
            'today' => [
                'start' => $now->copy()->startOfDay(),
                'end' => $now->copy()->endOfDay(),
                'label' => 'Hôm nay'
            ],
            'week' => [
                'start' => $now->copy()->startOfWeek(),
                'end' => $now->copy()->endOfWeek(),
                'label' => 'Tuần này'
            ],
            'month' => [
                'start' => $now->copy()->startOfMonth(),
                'end' => $now->copy()->endOfMonth(),
                'label' => 'Tháng này'
            ],
            'year' => [
                'start' => $now->copy()->startOfYear(),
                'end' => $now->copy()->endOfYear(),
                'label' => 'Năm nay'
            ],
            'all' => [
                'start' => Carbon::create(2020, 1, 1),
                'end' => $now->copy()->endOfDay(),
                'label' => 'Tất cả'
            ],
            default => [
                'start' => $now->copy()->startOfMonth(),
                'end' => $now->copy()->endOfMonth(),
                'label' => 'Tháng này'
            ]
        };
    }
    
    private function getStats(array $dateRange): array
    {
        $startDate = $dateRange['start'];
        $endDate = $dateRange['end'];
        
        // Users stats
        $totalUsers = User::count();
        $totalSellers = User::where('role', User::ROLE_SELLER)->count();
        $newUsers = User::whereBetween('created_at', [$startDate, $endDate])->count();
        
        // Products & Services
        $totalProducts = Product::count();
        $totalServices = Service::count();
        $activeProducts = Product::where('status', ProductStatus::APPROVED)->count();
        $activeServices = Service::where('status', ServiceStatus::APPROVED)->count();
        
        // Orders stats - Single optimized query
        $productOrderStats = Order::whereBetween('created_at', [$startDate, $endDate])
            ->selectRaw('
                COUNT(*) as total,
                SUM(CASE WHEN status = ? THEN 1 ELSE 0 END) as completed,
                SUM(CASE WHEN status = ? THEN 1 ELSE 0 END) as disputed,
                SUM(total_amount) as total_amount,
                SUM(CASE WHEN status = ? THEN total_amount ELSE 0 END) as completed_amount
            ', [
                OrderStatus::COMPLETED->value,
                OrderStatus::DISPUTED->value,
                OrderStatus::COMPLETED->value
            ])
            ->first();
        
        $serviceOrderStats = ServiceOrder::whereBetween('created_at', [$startDate, $endDate])
            ->selectRaw('
                COUNT(*) as total,
                SUM(CASE WHEN status = ? THEN 1 ELSE 0 END) as completed,
                SUM(CASE WHEN status = ? THEN 1 ELSE 0 END) as disputed,
                SUM(total_amount) as total_amount,
                SUM(CASE WHEN status = ? THEN total_amount ELSE 0 END) as completed_amount
            ', [
                ServiceOrderStatus::COMPLETED->value,
                ServiceOrderStatus::DISPUTED->value,
                ServiceOrderStatus::COMPLETED->value
            ])
            ->first();
        
        // Financial stats
        $depositStats = Deposit::whereBetween('created_at', [$startDate, $endDate])
            ->selectRaw('
                COUNT(*) as total,
                SUM(CASE WHEN status = ? THEN amount ELSE 0 END) as completed_amount
            ', [DepositStatus::SUCCESS->value])
            ->first();
        
        $withdrawalStats = Withdrawal::whereBetween('created_at', [$startDate, $endDate])
            ->selectRaw('
                COUNT(*) as total,
                SUM(CASE WHEN status = ? THEN amount ELSE 0 END) as completed_amount
            ', [WithdrawalStatus::COMPLETED->value])
            ->first();
        
        // Commission earned (from completed orders)
        $commissionEarned = WalletTransaction::where('type', WalletTransactionType::COMMISSION->value)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->sum('amount');
        
        // All time totals
        $allTimeOrders = Order::where('status', OrderStatus::COMPLETED)->count() 
            + ServiceOrder::where('status', ServiceOrderStatus::COMPLETED)->count();
        $allTimeRevenue = Order::where('status', OrderStatus::COMPLETED)->sum('total_amount')
            + ServiceOrder::where('status', ServiceOrderStatus::COMPLETED)->sum('total_amount');
        $allTimeCommission = WalletTransaction::where('type', WalletTransactionType::COMMISSION->value)->sum('amount');
        
        return [
            'users' => [
                'total' => $totalUsers,
                'sellers' => $totalSellers,
                'new' => $newUsers,
            ],
            'products' => [
                'total' => $totalProducts,
                'active' => $activeProducts,
            ],
            'services' => [
                'total' => $totalServices,
                'active' => $activeServices,
            ],
            'product_orders' => [
                'total' => (int) $productOrderStats->total,
                'completed' => (int) $productOrderStats->completed,
                'disputed' => (int) $productOrderStats->disputed,
                'amount' => (float) $productOrderStats->total_amount,
                'completed_amount' => (float) $productOrderStats->completed_amount,
            ],
            'service_orders' => [
                'total' => (int) $serviceOrderStats->total,
                'completed' => (int) $serviceOrderStats->completed,
                'disputed' => (int) $serviceOrderStats->disputed,
                'amount' => (float) $serviceOrderStats->total_amount,
                'completed_amount' => (float) $serviceOrderStats->completed_amount,
            ],
            'deposits' => [
                'total' => (int) $depositStats->total,
                'amount' => (float) $depositStats->completed_amount,
            ],
            'withdrawals' => [
                'total' => (int) $withdrawalStats->total,
                'amount' => (float) $withdrawalStats->completed_amount,
            ],
            'commission' => (float) $commissionEarned,
            'all_time' => [
                'orders' => $allTimeOrders,
                'revenue' => (float) $allTimeRevenue,
                'commission' => (float) $allTimeCommission,
            ]
        ];
    }
    
    private function getPendingItems(): array
    {
        return [
            'seller_registrations' => SellerRegistration::where('status', 'pending')->count(),
            'pending_products' => Product::where('status', ProductStatus::PENDING)->count(),
            'pending_services' => Service::where('status', ServiceStatus::PENDING)->count(),
            'product_disputes' => Dispute::where('status', DisputeStatus::REVIEWING)->count(),
            'service_disputes' => ServiceDispute::where('status', ServiceDisputeStatus::REVIEWING)->count(),
            'pending_withdrawals' => Withdrawal::whereIn('status', [WithdrawalStatus::PENDING, WithdrawalStatus::PROCESSING])->count(),
        ];
    }
    
    private function getRecentOrders(int $limit)
    {
        return Order::with(['buyer:id,full_name', 'seller:id,full_name'])
            ->select('id', 'slug', 'buyer_id', 'seller_id', 'total_amount', 'status', 'created_at')
            ->latest()
            ->limit($limit)
            ->get();
    }
    
    private function getRecentServiceOrders(int $limit)
    {
        return ServiceOrder::with(['buyer:id,full_name', 'seller:id,full_name', 'serviceVariant:id,name'])
            ->select('id', 'slug', 'buyer_id', 'seller_id', 'service_variant_id', 'total_amount', 'status', 'created_at')
            ->latest()
            ->limit($limit)
            ->get();
    }
    
    private function getRecentDeposits(int $limit)
    {
        return Deposit::with(['user:id,full_name'])
            ->select('id', 'slug', 'user_id', 'amount', 'status', 'created_at')
            ->latest()
            ->limit($limit)
            ->get();
    }
    
    private function getRecentWithdrawals(int $limit)
    {
        return Withdrawal::with(['user:id,full_name'])
            ->select('id', 'slug', 'user_id', 'amount', 'status', 'created_at')
            ->latest()
            ->limit($limit)
            ->get();
    }
    
    private function getRevenueChartData(string $period, int $year, int $month): array
    {
        $labels = [];
        $productData = [];
        $serviceData = [];
        $commissionData = [];
        
        if ($period === 'year') {
            for ($m = 1; $m <= 12; $m++) {
                $labels[] = "T{$m}";
                $startDate = Carbon::create($year, $m, 1)->startOfMonth();
                $endDate = Carbon::create($year, $m, 1)->endOfMonth();
                
                $productRev = Order::where('status', OrderStatus::COMPLETED)
                    ->whereBetween('created_at', [$startDate, $endDate])
                    ->sum('total_amount');
                
                $serviceRev = ServiceOrder::where('status', ServiceOrderStatus::COMPLETED)
                    ->whereBetween('created_at', [$startDate, $endDate])
                    ->sum('total_amount');
                
                $commission = WalletTransaction::where('type', WalletTransactionType::COMMISSION->value)
                    ->whereBetween('created_at', [$startDate, $endDate])
                    ->sum('amount');
                
                $productData[] = (float) $productRev;
                $serviceData[] = (float) $serviceRev;
                $commissionData[] = (float) $commission;
            }
        } else {
            $daysInMonth = Carbon::create($year, $month, 1)->daysInMonth;
            
            for ($d = 1; $d <= $daysInMonth; $d++) {
                $labels[] = $d;
                $date = Carbon::create($year, $month, $d);
                
                $productRev = Order::where('status', OrderStatus::COMPLETED)
                    ->whereDate('created_at', $date)
                    ->sum('total_amount');
                
                $serviceRev = ServiceOrder::where('status', ServiceOrderStatus::COMPLETED)
                    ->whereDate('created_at', $date)
                    ->sum('total_amount');
                
                $commission = WalletTransaction::where('type', WalletTransactionType::COMMISSION->value)
                    ->whereDate('created_at', $date)
                    ->sum('amount');
                
                $productData[] = (float) $productRev;
                $serviceData[] = (float) $serviceRev;
                $commissionData[] = (float) $commission;
            }
        }
        
        return [
            'labels' => $labels,
            'datasets' => [
                [
                    'label' => 'Doanh thu sản phẩm',
                    'data' => $productData,
                    'borderColor' => '#3b82f6',
                    'backgroundColor' => 'rgba(59, 130, 246, 0.1)',
                    'fill' => true,
                ],
                [
                    'label' => 'Doanh thu dịch vụ',
                    'data' => $serviceData,
                    'borderColor' => '#10b981',
                    'backgroundColor' => 'rgba(16, 185, 129, 0.1)',
                    'fill' => true,
                ],
                [
                    'label' => 'Phí sàn thu được',
                    'data' => $commissionData,
                    'borderColor' => '#f59e0b',
                    'backgroundColor' => 'rgba(245, 158, 11, 0.1)',
                    'fill' => true,
                ]
            ]
        ];
    }
    
    private function getOrdersChartData(string $period, int $year, int $month): array
    {
        $labels = [];
        $productData = [];
        $serviceData = [];
        
        if ($period === 'year') {
            for ($m = 1; $m <= 12; $m++) {
                $labels[] = "T{$m}";
                $startDate = Carbon::create($year, $m, 1)->startOfMonth();
                $endDate = Carbon::create($year, $m, 1)->endOfMonth();
                
                $productData[] = Order::whereBetween('created_at', [$startDate, $endDate])->count();
                $serviceData[] = ServiceOrder::whereBetween('created_at', [$startDate, $endDate])->count();
            }
        } else {
            $daysInMonth = Carbon::create($year, $month, 1)->daysInMonth;
            
            for ($d = 1; $d <= $daysInMonth; $d++) {
                $labels[] = $d;
                $date = Carbon::create($year, $month, $d);
                
                $productData[] = Order::whereDate('created_at', $date)->count();
                $serviceData[] = ServiceOrder::whereDate('created_at', $date)->count();
            }
        }
        
        return [
            'labels' => $labels,
            'datasets' => [
                ['label' => 'Đơn sản phẩm', 'data' => $productData, 'backgroundColor' => '#3b82f6'],
                ['label' => 'Đơn dịch vụ', 'data' => $serviceData, 'backgroundColor' => '#10b981'],
            ]
        ];
    }
    
    private function getUsersChartData(string $period, int $year, int $month): array
    {
        $labels = [];
        $userData = [];
        $sellerData = [];
        
        if ($period === 'year') {
            for ($m = 1; $m <= 12; $m++) {
                $labels[] = "T{$m}";
                $startDate = Carbon::create($year, $m, 1)->startOfMonth();
                $endDate = Carbon::create($year, $m, 1)->endOfMonth();
                
                $userData[] = User::whereBetween('created_at', [$startDate, $endDate])->count();
                $sellerData[] = User::where('role', User::ROLE_SELLER)
                    ->whereBetween('created_at', [$startDate, $endDate])->count();
            }
        } else {
            $daysInMonth = Carbon::create($year, $month, 1)->daysInMonth;
            
            for ($d = 1; $d <= $daysInMonth; $d++) {
                $labels[] = $d;
                $date = Carbon::create($year, $month, $d);
                
                $userData[] = User::whereDate('created_at', $date)->count();
                $sellerData[] = User::where('role', User::ROLE_SELLER)
                    ->whereDate('created_at', $date)->count();
            }
        }
        
        return [
            'labels' => $labels,
            'datasets' => [
                ['label' => 'Người dùng mới', 'data' => $userData, 'backgroundColor' => '#8b5cf6'],
                ['label' => 'Seller mới', 'data' => $sellerData, 'backgroundColor' => '#ec4899'],
            ]
        ];
    }
    
    private function getDepositsChartData(string $period, int $year, int $month): array
    {
        $labels = [];
        $depositData = [];
        $withdrawalData = [];
        
        if ($period === 'year') {
            for ($m = 1; $m <= 12; $m++) {
                $labels[] = "T{$m}";
                $startDate = Carbon::create($year, $m, 1)->startOfMonth();
                $endDate = Carbon::create($year, $m, 1)->endOfMonth();
                
                $depositData[] = (float) Deposit::where('status', DepositStatus::SUCCESS)
                    ->whereBetween('created_at', [$startDate, $endDate])->sum('amount');
                $withdrawalData[] = (float) Withdrawal::where('status', WithdrawalStatus::COMPLETED)
                    ->whereBetween('created_at', [$startDate, $endDate])->sum('amount');
            }
        } else {
            $daysInMonth = Carbon::create($year, $month, 1)->daysInMonth;
            
            for ($d = 1; $d <= $daysInMonth; $d++) {
                $labels[] = $d;
                $date = Carbon::create($year, $month, $d);
                
                $depositData[] = (float) Deposit::where('status', DepositStatus::SUCCESS)
                    ->whereDate('created_at', $date)->sum('amount');
                $withdrawalData[] = (float) Withdrawal::where('status', WithdrawalStatus::COMPLETED)
                    ->whereDate('created_at', $date)->sum('amount');
            }
        }
        
        return [
            'labels' => $labels,
            'datasets' => [
                ['label' => 'Nạp tiền', 'data' => $depositData, 'backgroundColor' => '#10b981'],
                ['label' => 'Rút tiền', 'data' => $withdrawalData, 'backgroundColor' => '#ef4444'],
            ]
        ];
    }
    
    private function getTopSellersData(): array
    {
        $topSellers = DB::table('orders')
            ->join('users', 'orders.seller_id', '=', 'users.id')
            ->where('orders.status', OrderStatus::COMPLETED->value)
            ->select(
                'users.id',
                'users.full_name',
                DB::raw('COUNT(*) as total_orders'),
                DB::raw('SUM(orders.total_amount) as total_revenue')
            )
            ->groupBy('users.id', 'users.full_name')
            ->orderByDesc('total_revenue')
            ->limit(5)
            ->get();
        
        return [
            'labels' => $topSellers->pluck('full_name')->toArray(),
            'orders' => $topSellers->pluck('total_orders')->toArray(),
            'revenue' => $topSellers->pluck('total_revenue')->toArray(),
        ];
    }
    
    private function getOrderStatusData(): array
    {
        $productStatusCounts = Order::selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();
        
        $serviceStatusCounts = ServiceOrder::selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();
        
        return [
            'product' => $productStatusCounts,
            'service' => $serviceStatusCounts,
        ];
    }
}
