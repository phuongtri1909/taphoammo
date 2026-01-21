<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ServiceDispute;
use App\Models\ServiceOrder;
use App\Models\ServiceRefund;
use App\Enums\ServiceDisputeStatus;
use App\Enums\ServiceOrderStatus;
use App\Enums\ServiceRefundStatus;
use App\Services\WalletService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class ServiceDisputeController extends Controller
{
    /**
     * Admin danh sách service disputes
     */
    public function index(Request $request)
    {
        $query = ServiceDispute::with(['serviceOrder.buyer', 'serviceOrder.seller', 'serviceOrder.serviceVariant.service'])
            ->latest();

        if ($request->has('status') && $request->status) {
            try {
                $status = ServiceDisputeStatus::from($request->status);
                $query->where('status', $status);
            } catch (\ValueError $e) {
                // Ignore invalid status
            }
        }

        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('slug', 'like', "%{$search}%")
                  ->orWhere('reason', 'like', "%{$search}%")
                  ->orWhereHas('serviceOrder', function ($orderQ) use ($search) {
                      $orderQ->where('slug', 'like', "%{$search}%");
                  })
                  ->orWhereHas('serviceOrder.buyer', function ($buyerQ) use ($search) {
                      $buyerQ->where('full_name', 'like', "%{$search}%")
                             ->orWhere('email', 'like', "%{$search}%");
                  })
                  ->orWhereHas('serviceOrder.seller', function ($sellerQ) use ($search) {
                      $sellerQ->where('full_name', 'like', "%{$search}%")
                              ->orWhere('email', 'like', "%{$search}%");
                  });
            });
        }

        $disputes = $query->paginate(20);

        return view('admin.pages.service-disputes.index', compact('disputes'));
    }

    /**
     * Admin chi tiết service dispute
     */
    public function show(ServiceDispute $dispute)
    {
        $dispute->load([
            'serviceOrder.buyer',
            'serviceOrder.seller', 
            'serviceOrder.serviceVariant.service',
            'resolvedBy'
        ]);

        return view('admin.pages.service-disputes.show', compact('dispute'));
    }

    /**
     * Admin chấp nhận khiếu nại - hoàn tiền cho buyer
     */
    public function accept(Request $request, ServiceDispute $dispute)
    {
        if ($dispute->status !== ServiceDisputeStatus::REVIEWING) {
            return response()->json([
                'success' => false,
                'message' => 'Khiếu nại không ở trạng thái chờ xem xét'
            ], 400);
        }

        try {
            DB::transaction(function () use ($dispute, $request) {
                $dispute->update([
                    'status' => ServiceDisputeStatus::APPROVED,
                    'admin_note' => $request->input('admin_note'),
                    'resolved_by' => Auth::id(),
                    'resolved_at' => now(),
                ]);

                $serviceOrder = $dispute->serviceOrder;
                $serviceOrder->update([
                    'status' => ServiceOrderStatus::REFUNDED,
                ]);

                // Tạo ServiceRefund record
                ServiceRefund::create([
                    'service_order_id' => $serviceOrder->id,
                    'buyer_id' => $serviceOrder->buyer_id,
                    'total_amount' => $serviceOrder->total_amount,
                    'status' => ServiceRefundStatus::COMPLETED,
                    'processed_by' => Auth::id(),
                ]);

                // Hoàn tiền cho buyer
                WalletService::refundForServiceOrder($serviceOrder);
            });

            return response()->json([
                'success' => true,
                'message' => 'Đã chấp nhận khiếu nại và hoàn tiền cho người mua'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Admin từ chối khiếu nại - hoàn thành đơn, cộng tiền seller
     */
    public function reject(Request $request, ServiceDispute $dispute)
    {
        if ($dispute->status !== ServiceDisputeStatus::REVIEWING) {
            return response()->json([
                'success' => false,
                'message' => 'Khiếu nại không ở trạng thái chờ xem xét'
            ], 400);
        }

        try {
            DB::transaction(function () use ($dispute, $request) {
                $dispute->update([
                    'status' => ServiceDisputeStatus::REJECTED,
                    'admin_note' => $request->input('admin_note'),
                    'resolved_by' => Auth::id(),
                    'resolved_at' => now(),
                ]);

                $serviceOrder = $dispute->serviceOrder;
                $serviceOrder->update([
                    'status' => ServiceOrderStatus::COMPLETED,
                ]);

                // Cộng tiền cho seller
                WalletService::paySellerForServiceOrder($serviceOrder);
            });

            return response()->json([
                'success' => true,
                'message' => 'Đã từ chối khiếu nại và hoàn thành đơn hàng'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra: ' . $e->getMessage()
            ], 500);
        }
    }
}
