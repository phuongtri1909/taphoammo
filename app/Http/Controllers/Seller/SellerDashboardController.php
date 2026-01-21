<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\ServiceOrder;
use App\Models\Product;
use App\Models\Service;
use App\Models\Dispute;
use App\Models\ServiceDispute;
use App\Models\WalletTransaction;
use App\Enums\OrderStatus;
use App\Enums\ServiceOrderStatus;
use App\Enums\DisputeStatus;
use App\Enums\ServiceDisputeStatus;
use App\Enums\WalletTransactionType;
use App\Enums\WalletTransactionReferenceType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;

class SellerDashboardController extends Controller
{
    public function index(Request $request)
    {
        $seller = Auth::user();
        $sellerId = $seller->id;
        
        // Period filter (default: this month)
        $period = $request->get('period', 'month');
        $dateRange = $this->getDateRange($period);
        
        // Cache key unique per seller and period
        $cacheKey = "seller_dashboard_{$sellerId}_{$period}";
        $cacheTTL = 300; // 5 minutes
        
        // Get all stats in optimized queries
        $stats = Cache::remember($cacheKey, $cacheTTL, function () use ($sellerId, $dateRange) {
            return $this->getStats($sellerId, $dateRange);
        });
        
        // Recent orders (không cache vì cần real-time)
        $recentOrders = $this->getRecentOrders($sellerId, 5);
        $recentServiceOrders = $this->getRecentServiceOrders($sellerId, 5);
        
        // Pending actions
        $pendingActions = $this->getPendingActions($sellerId);
        
        return view('seller.pages.dashboard', compact(
            'stats',
            'recentOrders',
            'recentServiceOrders',
            'pendingActions',
            'period',
            'dateRange'
        ));
    }
    
    /**
     * API endpoint for chart data (AJAX)
     */
    public function chartData(Request $request)
    {
        $seller = Auth::user();
        $sellerId = $seller->id;
        $type = $request->get('type', 'revenue'); // revenue, orders, products
        $period = $request->get('period', 'month');
        $year = $request->get('year', date('Y'));
        $month = $request->get('month', date('m'));
        
        $data = [];
        
        switch ($type) {
            case 'revenue':
                $data = $this->getRevenueChartData($sellerId, $period, $year, $month);
                break;
            case 'orders':
                $data = $this->getOrdersChartData($sellerId, $period, $year, $month);
                break;
            case 'products':
                $data = $this->getTopProductsData($sellerId);
                break;
            case 'services':
                $data = $this->getTopServicesData($sellerId);
                break;
        }
        
        return response()->json($data);
    }
    
    /**
     * Get date range based on period
     */
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
    
    /**
     * Get all statistics in optimized queries
     */
    private function getStats(int $sellerId, array $dateRange): array
    {
        $startDate = $dateRange['start'];
        $endDate = $dateRange['end'];
        
        // Product Orders Stats - Single query with multiple aggregations
        $productOrderStats = Order::where('seller_id', $sellerId)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->selectRaw('
                COUNT(*) as total_orders,
                SUM(CASE WHEN status = ? THEN 1 ELSE 0 END) as completed_orders,
                SUM(CASE WHEN status = ? THEN 1 ELSE 0 END) as disputed_orders,
                SUM(CASE WHEN status IN (?, ?) THEN 1 ELSE 0 END) as refunded_orders,
                SUM(CASE WHEN status = ? THEN total_amount ELSE 0 END) as completed_revenue
            ', [
                OrderStatus::COMPLETED->value,
                OrderStatus::DISPUTED->value,
                OrderStatus::REFUNDED->value,
                OrderStatus::PARTIAL_REFUNDED->value,
                OrderStatus::COMPLETED->value
            ])
            ->first();
        
        // Service Orders Stats - Single query
        $serviceOrderStats = ServiceOrder::where('seller_id', $sellerId)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->selectRaw('
                COUNT(*) as total_orders,
                SUM(CASE WHEN status = ? THEN 1 ELSE 0 END) as completed_orders,
                SUM(CASE WHEN status = ? THEN 1 ELSE 0 END) as disputed_orders,
                SUM(CASE WHEN status IN (?, ?) THEN 1 ELSE 0 END) as refunded_orders,
                SUM(CASE WHEN status = ? THEN total_amount ELSE 0 END) as completed_revenue
            ', [
                ServiceOrderStatus::COMPLETED->value,
                ServiceOrderStatus::DISPUTED->value,
                ServiceOrderStatus::REFUNDED->value,
                ServiceOrderStatus::PARTIAL_REFUNDED->value,
                ServiceOrderStatus::COMPLETED->value
            ])
            ->first();
        
        // Products & Services count
        $productCount = Product::where('seller_id', $sellerId)->count();
        $serviceCount = Service::where('seller_id', $sellerId)->count();
        
        // Total earnings from wallet transactions (actual money received)
        $totalEarnings = WalletTransaction::whereHas('wallet', function ($q) use ($sellerId) {
                $q->where('user_id', $sellerId);
            })
            ->where('type', WalletTransactionType::SALE->value)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->sum('amount');
        
        // All time stats
        $allTimeProductOrders = Order::where('seller_id', $sellerId)
            ->where('status', OrderStatus::COMPLETED)
            ->count();
        $allTimeServiceOrders = ServiceOrder::where('seller_id', $sellerId)
            ->where('status', ServiceOrderStatus::COMPLETED)
            ->count();
        $allTimeEarnings = WalletTransaction::whereHas('wallet', function ($q) use ($sellerId) {
                $q->where('user_id', $sellerId);
            })
            ->where('type', WalletTransactionType::SALE->value)
            ->sum('amount');
        
        return [
            'product_orders' => [
                'total' => (int) $productOrderStats->total_orders,
                'completed' => (int) $productOrderStats->completed_orders,
                'disputed' => (int) $productOrderStats->disputed_orders,
                'refunded' => (int) $productOrderStats->refunded_orders,
                'revenue' => (float) $productOrderStats->completed_revenue,
            ],
            'service_orders' => [
                'total' => (int) $serviceOrderStats->total_orders,
                'completed' => (int) $serviceOrderStats->completed_orders,
                'disputed' => (int) $serviceOrderStats->disputed_orders,
                'refunded' => (int) $serviceOrderStats->refunded_orders,
                'revenue' => (float) $serviceOrderStats->completed_revenue,
            ],
            'products_count' => $productCount,
            'services_count' => $serviceCount,
            'total_earnings' => (float) $totalEarnings,
            'all_time' => [
                'product_orders' => $allTimeProductOrders,
                'service_orders' => $allTimeServiceOrders,
                'earnings' => (float) $allTimeEarnings,
            ]
        ];
    }
    
    /**
     * Get recent product orders
     */
    private function getRecentOrders(int $sellerId, int $limit): \Illuminate\Database\Eloquent\Collection
    {
        return Order::where('seller_id', $sellerId)
            ->with(['buyer:id,full_name,email'])
            ->select('id', 'slug', 'buyer_id', 'total_amount', 'status', 'created_at')
            ->latest()
            ->limit($limit)
            ->get();
    }
    
    /**
     * Get recent service orders
     */
    private function getRecentServiceOrders(int $sellerId, int $limit): \Illuminate\Database\Eloquent\Collection
    {
        return ServiceOrder::where('seller_id', $sellerId)
            ->with(['buyer:id,full_name,email', 'serviceVariant:id,name,service_id', 'serviceVariant.service:id,name'])
            ->select('id', 'slug', 'buyer_id', 'service_variant_id', 'total_amount', 'status', 'created_at')
            ->latest()
            ->limit($limit)
            ->get();
    }
    
    /**
     * Get pending actions that need seller attention
     */
    private function getPendingActions(int $sellerId): array
    {
        // Product disputes waiting for seller response
        $pendingProductDisputes = Dispute::where('seller_id', $sellerId)
            ->where('status', DisputeStatus::OPEN)
            ->count();
        
        // Service disputes waiting for seller response
        $pendingServiceDisputes = ServiceDispute::where('seller_id', $sellerId)
            ->where('status', ServiceDisputeStatus::OPEN)
            ->count();
        
        // Service orders waiting for seller confirmation
        $pendingServiceOrders = ServiceOrder::where('seller_id', $sellerId)
            ->where('status', ServiceOrderStatus::PAID)
            ->count();
        
        return [
            'product_disputes' => $pendingProductDisputes,
            'service_disputes' => $pendingServiceDisputes,
            'pending_service_orders' => $pendingServiceOrders,
            'total' => $pendingProductDisputes + $pendingServiceDisputes + $pendingServiceOrders,
        ];
    }
    
    /**
     * Get revenue chart data
     */
    private function getRevenueChartData(int $sellerId, string $period, int $year, int $month): array
    {
        $labels = [];
        $productData = [];
        $serviceData = [];
        
        if ($period === 'year') {
            // Monthly data for the year
            for ($m = 1; $m <= 12; $m++) {
                $labels[] = "T{$m}";
                
                $startDate = Carbon::create($year, $m, 1)->startOfMonth();
                $endDate = Carbon::create($year, $m, 1)->endOfMonth();
                
                // Product revenue
                $productRev = WalletTransaction::whereHas('wallet', function ($q) use ($sellerId) {
                        $q->where('user_id', $sellerId);
                    })
                    ->where('type', WalletTransactionType::SALE->value)
                    ->where('reference_type', WalletTransactionReferenceType::ORDER->value)
                    ->whereBetween('created_at', [$startDate, $endDate])
                    ->sum('amount');
                
                // Service revenue
                $serviceRev = WalletTransaction::whereHas('wallet', function ($q) use ($sellerId) {
                        $q->where('user_id', $sellerId);
                    })
                    ->where('type', WalletTransactionType::SALE->value)
                    ->where('reference_type', WalletTransactionReferenceType::SERVICE_ORDER->value)
                    ->whereBetween('created_at', [$startDate, $endDate])
                    ->sum('amount');
                
                $productData[] = (float) $productRev;
                $serviceData[] = (float) $serviceRev;
            }
        } else {
            // Daily data for the month
            $daysInMonth = Carbon::create($year, $month, 1)->daysInMonth;
            
            for ($d = 1; $d <= $daysInMonth; $d++) {
                $labels[] = $d;
                
                $date = Carbon::create($year, $month, $d);
                
                $productRev = WalletTransaction::whereHas('wallet', function ($q) use ($sellerId) {
                        $q->where('user_id', $sellerId);
                    })
                    ->where('type', WalletTransactionType::SALE->value)
                    ->where('reference_type', WalletTransactionReferenceType::ORDER->value)
                    ->whereDate('created_at', $date)
                    ->sum('amount');
                
                $serviceRev = WalletTransaction::whereHas('wallet', function ($q) use ($sellerId) {
                        $q->where('user_id', $sellerId);
                    })
                    ->where('type', WalletTransactionType::SALE->value)
                    ->where('reference_type', WalletTransactionReferenceType::SERVICE_ORDER->value)
                    ->whereDate('created_at', $date)
                    ->sum('amount');
                
                $productData[] = (float) $productRev;
                $serviceData[] = (float) $serviceRev;
            }
        }
        
        return [
            'labels' => $labels,
            'datasets' => [
                [
                    'label' => 'Sản phẩm',
                    'data' => $productData,
                    'borderColor' => '#3b82f6',
                    'backgroundColor' => 'rgba(59, 130, 246, 0.1)',
                    'fill' => true,
                ],
                [
                    'label' => 'Dịch vụ',
                    'data' => $serviceData,
                    'borderColor' => '#10b981',
                    'backgroundColor' => 'rgba(16, 185, 129, 0.1)',
                    'fill' => true,
                ]
            ]
        ];
    }
    
    /**
     * Get orders chart data
     */
    private function getOrdersChartData(int $sellerId, string $period, int $year, int $month): array
    {
        $labels = [];
        $productData = [];
        $serviceData = [];
        
        if ($period === 'year') {
            for ($m = 1; $m <= 12; $m++) {
                $labels[] = "T{$m}";
                
                $startDate = Carbon::create($year, $m, 1)->startOfMonth();
                $endDate = Carbon::create($year, $m, 1)->endOfMonth();
                
                $productCount = Order::where('seller_id', $sellerId)
                    ->whereBetween('created_at', [$startDate, $endDate])
                    ->count();
                
                $serviceCount = ServiceOrder::where('seller_id', $sellerId)
                    ->whereBetween('created_at', [$startDate, $endDate])
                    ->count();
                
                $productData[] = $productCount;
                $serviceData[] = $serviceCount;
            }
        } else {
            $daysInMonth = Carbon::create($year, $month, 1)->daysInMonth;
            
            for ($d = 1; $d <= $daysInMonth; $d++) {
                $labels[] = $d;
                
                $date = Carbon::create($year, $month, $d);
                
                $productCount = Order::where('seller_id', $sellerId)
                    ->whereDate('created_at', $date)
                    ->count();
                
                $serviceCount = ServiceOrder::where('seller_id', $sellerId)
                    ->whereDate('created_at', $date)
                    ->count();
                
                $productData[] = $productCount;
                $serviceData[] = $serviceCount;
            }
        }
        
        return [
            'labels' => $labels,
            'datasets' => [
                [
                    'label' => 'Đơn sản phẩm',
                    'data' => $productData,
                    'backgroundColor' => '#3b82f6',
                ],
                [
                    'label' => 'Đơn dịch vụ',
                    'data' => $serviceData,
                    'backgroundColor' => '#10b981',
                ]
            ]
        ];
    }
    
    /**
     * Get top selling products
     */
    private function getTopProductsData(int $sellerId): array
    {
        $topProducts = DB::table('order_items')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->join('product_variants', 'order_items.product_variant_id', '=', 'product_variants.id')
            ->join('products', 'product_variants.product_id', '=', 'products.id')
            ->where('orders.seller_id', $sellerId)
            ->where('orders.status', OrderStatus::COMPLETED->value)
            ->select(
                'products.name',
                DB::raw('SUM(order_items.quantity) as total_sold'),
                DB::raw('SUM(order_items.price * order_items.quantity) as total_revenue')
            )
            ->groupBy('products.id', 'products.name')
            ->orderByDesc('total_sold')
            ->limit(5)
            ->get();
        
        return [
            'labels' => $topProducts->pluck('name')->toArray(),
            'data' => $topProducts->pluck('total_sold')->toArray(),
            'revenue' => $topProducts->pluck('total_revenue')->toArray(),
        ];
    }
    
    /**
     * Get top selling services
     */
    private function getTopServicesData(int $sellerId): array
    {
        $topServices = DB::table('service_orders')
            ->join('service_variants', 'service_orders.service_variant_id', '=', 'service_variants.id')
            ->join('services', 'service_variants.service_id', '=', 'services.id')
            ->where('service_orders.seller_id', $sellerId)
            ->where('service_orders.status', ServiceOrderStatus::COMPLETED->value)
            ->select(
                'services.name',
                DB::raw('COUNT(*) as total_sold'),
                DB::raw('SUM(service_orders.total_amount) as total_revenue')
            )
            ->groupBy('services.id', 'services.name')
            ->orderByDesc('total_sold')
            ->limit(5)
            ->get();
        
        return [
            'labels' => $topServices->pluck('name')->toArray(),
            'data' => $topServices->pluck('total_sold')->toArray(),
            'revenue' => $topServices->pluck('total_revenue')->toArray(),
        ];
    }
}
