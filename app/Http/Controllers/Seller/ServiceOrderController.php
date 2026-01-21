<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Models\ServiceOrder;
use App\Models\WalletTransaction;
use App\Models\Config;
use App\Enums\ServiceOrderStatus;
use App\Enums\WalletTransactionType;
use App\Enums\WalletTransactionReferenceType;
use App\Services\ServiceOrderService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ServiceOrderController extends Controller
{
    public function index(Request $request)
    {
        $seller = Auth::user();
        
        $query = ServiceOrder::where('seller_id', $seller->id)
            ->with(['buyer', 'serviceVariant.service'])
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
                  ->orWhereHas('serviceVariant.service', function ($serviceQ) use ($search) {
                      $serviceQ->where('name', 'like', "%{$search}%");
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

        return view('seller.pages.service-orders.index', compact('orders'));
    }

    public function show(ServiceOrder $serviceOrder)
    {
        $seller = Auth::user();
        
        if ($serviceOrder->seller_id !== $seller->id) {
            abort(403);
        }

        $serviceOrder->load([
            'buyer',
            'seller',
            'serviceVariant.service',
            'disputes',
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

        // Tính deadline cho buyer xác nhận
        $serviceOrderService = new ServiceOrderService();
        $buyerConfirmDeadline = null;
        if ($serviceOrder->status === ServiceOrderStatus::SELLER_CONFIRMED) {
            $buyerConfirmDeadline = $serviceOrderService->getBuyerConfirmDeadline($serviceOrder);
        }

        // Tính deadline cho seller xác nhận hoàn thành / phản hồi khiếu nại
        $sellerConfirmDeadline = null;
        $completionHours = (int) Config::getConfig('service_order_completion_hours', 96);
        
        if ($serviceOrder->status === ServiceOrderStatus::PAID) {
            // Nếu có last_dispute_resolved_at → đơn quay về PAID sau dispute → tính từ đó
            // Nếu không → đơn mới → tính từ created_at
            $baseTime = $serviceOrder->last_dispute_resolved_at ?? $serviceOrder->created_at;
            $sellerConfirmDeadline = $baseTime->copy()->addHours($completionHours);
        } elseif ($serviceOrder->status === ServiceOrderStatus::DISPUTED) {
            // Khi có khiếu nại, tính từ last_dispute_created_at để seller có thời gian phản hồi
            $baseTime = $serviceOrder->last_dispute_created_at ?? $serviceOrder->created_at;
            $sellerConfirmDeadline = $baseTime->copy()->addHours($completionHours);
        }

        return view('seller.pages.service-orders.show', compact(
            'serviceOrder',
            'sellerSaleTransaction',
            'sellerEarnings',
            'totalRefunded',
            'expectedCommission',
            'expectedSellerAmount',
            'expectedRefundAmount',
            'commissionRate',
            'buyerConfirmDeadline',
            'sellerConfirmDeadline'
        ));
    }

    /**
     * Seller từ chối nhận đơn hàng (hoàn tiền cho buyer)
     */
    public function rejectOrder(ServiceOrder $serviceOrder)
    {
        $seller = Auth::user();
        
        if ($serviceOrder->seller_id !== $seller->id) {
            return response()->json([
                'success' => false,
                'message' => 'Bạn không có quyền thực hiện hành động này.'
            ], 403);
        }

        try {
            $serviceOrderService = new ServiceOrderService();
            $serviceOrderService->rejectOrder($serviceOrder, $seller->id);

            return response()->json([
                'success' => true,
                'message' => 'Đã từ chối đơn hàng. Tiền đã được hoàn lại cho người mua.'
            ]);
        } catch (\DomainException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Seller xác nhận đã hoàn thành dịch vụ (lần đầu)
     */
    public function confirmCompletion(ServiceOrder $serviceOrder)
    {
        $seller = Auth::user();
        
        if ($serviceOrder->seller_id !== $seller->id) {
            return response()->json([
                'success' => false,
                'message' => 'Bạn không có quyền thực hiện hành động này.'
            ], 403);
        }

        try {
            $serviceOrderService = new ServiceOrderService();
            $serviceOrderService->confirmCompletion($serviceOrder, $seller->id);

            return response()->json([
                'success' => true,
                'message' => 'Đã xác nhận hoàn thành dịch vụ. Đang chờ người mua xác nhận.'
            ]);
        } catch (\DomainException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Seller chấp nhận giải quyết khiếu nại
     */
    public function acceptDispute(Request $request, ServiceOrder $serviceOrder, \App\Models\ServiceDispute $dispute)
    {
        $seller = Auth::user();
        
        if ($serviceOrder->seller_id !== $seller->id) {
            return response()->json([
                'success' => false,
                'message' => 'Bạn không có quyền thực hiện hành động này.'
            ], 403);
        }

        if ($dispute->service_order_id !== $serviceOrder->id) {
            return response()->json([
                'success' => false,
                'message' => 'Khiếu nại không thuộc đơn hàng này.'
            ], 422);
        }

        if ($dispute->status !== \App\Enums\ServiceDisputeStatus::OPEN) {
            return response()->json([
                'success' => false,
                'message' => 'Khiếu nại không ở trạng thái có thể giải quyết.'
            ], 422);
        }

        $validated = $request->validate([
            'seller_note' => 'nullable|string|max:1000',
        ]);

        try {
            \Illuminate\Support\Facades\DB::transaction(function () use ($dispute, $serviceOrder, $seller, $validated) {
                $dispute->update([
                    'status' => \App\Enums\ServiceDisputeStatus::APPROVED,
                    'seller_note' => $validated['seller_note'] ?? null,
                    'resolved_at' => now(),
                    'resolved_by' => $seller->id,
                ]);

                // Reset deadline và chuyển status về SELLER_CONFIRMED
                $serviceOrderService = new ServiceOrderService();
                $serviceOrderService->resetDeadlineOnDisputeResolved($serviceOrder);
            });

            return response()->json([
                'success' => true,
                'message' => 'Đã chấp nhận giải quyết khiếu nại. Vui lòng điều chỉnh và báo lại khi hoàn thành.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Seller từ chối giải quyết khiếu nại (chuyển sang admin review)
     */
    public function rejectDispute(Request $request, ServiceOrder $serviceOrder, \App\Models\ServiceDispute $dispute)
    {
        $seller = Auth::user();
        
        if ($serviceOrder->seller_id !== $seller->id) {
            return response()->json([
                'success' => false,
                'message' => 'Bạn không có quyền thực hiện hành động này.'
            ], 403);
        }

        if ($dispute->service_order_id !== $serviceOrder->id) {
            return response()->json([
                'success' => false,
                'message' => 'Khiếu nại không thuộc đơn hàng này.'
            ], 422);
        }

        if ($dispute->status !== \App\Enums\ServiceDisputeStatus::OPEN) {
            return response()->json([
                'success' => false,
                'message' => 'Khiếu nại không ở trạng thái có thể từ chối.'
            ], 422);
        }

        $validated = $request->validate([
            'seller_note' => 'required|string|min:10|max:1000',
        ], [
            'seller_note.required' => 'Vui lòng nhập lý do từ chối.',
            'seller_note.min' => 'Lý do từ chối phải có ít nhất 10 ký tự.',
            'seller_note.max' => 'Lý do từ chối không được vượt quá 1000 ký tự.',
        ]);

        try {
            $dispute->update([
                'status' => \App\Enums\ServiceDisputeStatus::REVIEWING,
                'seller_note' => $validated['seller_note'],
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Đã từ chối giải quyết. Khiếu nại đã được chuyển cho Admin xem xét.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra: ' . $e->getMessage()
            ], 500);
        }
    }
}