<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ServiceOrder;
use App\Models\User;
use App\Models\WalletTransaction;
use App\Models\Config;
use App\Enums\ServiceOrderStatus;
use App\Enums\WalletTransactionType;
use App\Enums\WalletTransactionReferenceType;
use Illuminate\Http\Request;

class ServiceOrderController extends Controller
{
    public function index(Request $request)
    {
        $query = ServiceOrder::with(['buyer', 'seller', 'serviceVariant.service'])
            ->latest();

        if ($request->has('status') && $request->status) {
            try {
                $status = ServiceOrderStatus::from($request->status);
                $query->where('status', $status);
            } catch (\ValueError $e) {
                // Ignore invalid status
            }
        }

        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('slug', 'like', "%{$search}%")
                  ->orWhereHas('buyer', function ($buyerQ) use ($search) {
                      $buyerQ->where('full_name', 'like', "%{$search}%")
                             ->orWhere('email', 'like', "%{$search}%");
                  })
                  ->orWhereHas('seller', function ($sellerQ) use ($search) {
                      $sellerQ->where('full_name', 'like', "%{$search}%")
                              ->orWhere('email', 'like', "%{$search}%");
                  })
                  ->orWhereHas('serviceVariant.service', function ($serviceQ) use ($search) {
                      $serviceQ->where('name', 'like', "%{$search}%");
                  });
            });
        }

        if ($request->filled('seller_id')) {
            $query->where('seller_id', $request->seller_id);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        if ($request->filled('amount_min')) {
            $query->where('total_amount', '>=', $request->amount_min);
        }
        if ($request->filled('amount_max')) {
            $query->where('total_amount', '<=', $request->amount_max);
        }

        $orders = $query->paginate(20);

        $sellerIds = ServiceOrder::distinct()->pluck('seller_id')->toArray();
        $sellers = User::where('role', User::ROLE_SELLER)
            ->whereIn('id', $sellerIds)
            ->orderBy('full_name')
            ->limit(100)
            ->get(['id', 'full_name', 'email']);

        return view('admin.pages.service-orders.index', compact('orders', 'sellers'));
    }

    public function show(ServiceOrder $serviceOrder)
    {
        $serviceOrder->load([
            'buyer',
            'seller',
            'serviceVariant.service',
            'disputes.resolvedBy',
            'refunds'
        ]);

        $sellerSaleTransactions = WalletTransaction::where('reference_type', WalletTransactionReferenceType::SERVICE_ORDER->value)
            ->where('reference_id', $serviceOrder->id)
            ->where('type', WalletTransactionType::SALE->value)
            ->get();

        $sellerEarnings = $sellerSaleTransactions->sum('amount');
        $sellerSaleTransaction = $sellerSaleTransactions->first();
        
        $totalRefunded = $serviceOrder->refunds()
            ->where('status', \App\Enums\ServiceRefundStatus::COMPLETED)
            ->sum('total_amount');

        $commissionRate = (float) Config::getConfig('commission_rate', 10);
        $commissionRatePercent = $commissionRate / 100;

        $expectedCommission = 0;
        $expectedSellerAmount = 0;
        $expectedRefundAmount = 0;

        if ($serviceOrder->status === ServiceOrderStatus::DISPUTED) {
            $openDisputes = $serviceOrder->disputes()->whereIn('status', [
                \App\Enums\ServiceDisputeStatus::OPEN,
                \App\Enums\ServiceDisputeStatus::REVIEWING
            ])->get();

            foreach ($openDisputes as $dispute) {
                $expectedRefundAmount += $serviceOrder->total_amount;
            }

            $nonDisputedAmount = $serviceOrder->total_amount - $expectedRefundAmount;
            $expectedSellerAmount = $nonDisputedAmount * (1 - $commissionRatePercent);
            $expectedCommission = $nonDisputedAmount * $commissionRatePercent;

        } elseif (in_array($serviceOrder->status, [ServiceOrderStatus::REFUNDED, ServiceOrderStatus::PARTIAL_REFUNDED])) {
            // Already refunded
        } elseif ($serviceOrder->status === ServiceOrderStatus::COMPLETED) {
            $expectedSellerAmount = $sellerEarnings;
            $expectedCommission = $serviceOrder->total_amount - $sellerEarnings;

        } else {
            $expectedCommission = $serviceOrder->total_amount * $commissionRatePercent;
            $expectedSellerAmount = $serviceOrder->total_amount * (1 - $commissionRatePercent);
        }

        return view('admin.pages.service-orders.show', compact(
            'serviceOrder',
            'sellerSaleTransaction',
            'sellerEarnings',
            'totalRefunded',
            'expectedCommission',
            'expectedSellerAmount',
            'expectedRefundAmount',
            'commissionRate'
        ));
    }
}