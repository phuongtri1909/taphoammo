<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\WalletTransaction;
use App\Models\Config;
use App\Enums\OrderStatus;
use App\Enums\WalletTransactionType;
use App\Enums\WalletTransactionReferenceType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $seller = Auth::user();
        
        $query = Order::where('seller_id', $seller->id)
            ->with(['buyer', 'items.productVariant.product', 'items.productValues'])
            ->latest();

        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }

        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('slug', 'like', "%{$search}%")
                  ->orWhereHas('buyer', function ($buyerQ) use ($search) {
                      $buyerQ->where('full_name', 'like', "%{$search}%")
                             ->orWhere('email', 'like', "%{$search}%");
                  });
            });
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

        return view('seller.pages.orders.index', compact('orders'));
    }

    public function show(Order $order)
    {
        $seller = Auth::user();
        
        if ($order->seller_id !== $seller->id) {
            abort(403);
        }

        $order->load([
            'buyer',
            'items.productVariant.product',
            'items.productValues',
            'disputes.orderItem',
            'disputes.items.productValue',
            'refunds.items.productValue'
        ]);

        $sellerSaleTransactions = WalletTransaction::where('reference_type', WalletTransactionReferenceType::ORDER->value)
            ->where('reference_id', $order->id)
            ->where('type', WalletTransactionType::SALE->value)
            ->get();

        $sellerEarnings = $sellerSaleTransactions->sum('amount');
        $sellerSaleTransaction = $sellerSaleTransactions->first();
        
        $totalRefunded = $order->refunds()
            ->where('status', \App\Enums\RefundStatus::COMPLETED)
            ->sum('total_amount');

        $commissionRate = (float) Config::getConfig('commission_rate', 10);
        $commissionRatePercent = $commissionRate / 100;

        $expectedCommission = 0;
        $expectedSellerAmount = 0;
        $expectedRefundAmount = 0;
        $disputedValuesCount = 0;

        if ($order->status === OrderStatus::DISPUTED) {
            $openDisputes = $order->disputes()->whereIn('status', [
                \App\Enums\DisputeStatus::OPEN,
                \App\Enums\DisputeStatus::REVIEWING
            ])->get();

            foreach ($openDisputes as $dispute) {
                $disputedValueIds = $dispute->items->pluck('product_value_id');
                $disputedValues = \App\Models\ProductValue::whereIn('id', $disputedValueIds)->get();
                
                $pricePerValue = $dispute->orderItem->price;
                
                foreach ($disputedValues as $value) {
                    $expectedRefundAmount += $pricePerValue;
                    $disputedValuesCount++;
                }
            }

            $nonDisputedAmount = 0;
            foreach ($order->items as $item) {
                $itemDisputes = $openDisputes->where('order_item_id', $item->id);
                $disputedItemValueIds = collect();
                foreach ($itemDisputes as $dispute) {
                    $disputedItemValueIds = $disputedItemValueIds->merge($dispute->items->pluck('product_value_id'));
                }
                
                $itemTotalValues = $item->quantity;
                $itemDisputedCount = $disputedItemValueIds->unique()->count();
                $itemNonDisputedCount = $itemTotalValues - $itemDisputedCount;
                
                $pricePerValue = $item->price;
                $nonDisputedAmount += $itemNonDisputedCount * $pricePerValue;
            }
            
            $expectedSellerAmount = $nonDisputedAmount * (1 - $commissionRatePercent);
            $expectedCommission = $nonDisputedAmount * $commissionRatePercent;

        } elseif (in_array($order->status, [OrderStatus::PARTIAL_REFUNDED, OrderStatus::REFUNDED])) {
        } elseif ($order->status === OrderStatus::COMPLETED) {
            $expectedSellerAmount = $sellerEarnings;
            $expectedCommission = $order->total_amount - $sellerEarnings;

        } else {
            $expectedCommission = $order->total_amount * $commissionRatePercent;
            $expectedSellerAmount = $order->total_amount * (1 - $commissionRatePercent);
        }

        return view('seller.pages.orders.show', compact(
            'order',
            'sellerSaleTransaction',
            'sellerEarnings',
            'totalRefunded',
            'expectedCommission',
            'expectedSellerAmount',
            'expectedRefundAmount',
            'disputedValuesCount',
            'commissionRate'
        ));
    }
}
