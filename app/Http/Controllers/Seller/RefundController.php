<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Models\Dispute;
use App\Models\Refund;
use App\Models\ProductValue;
use App\Enums\DisputeStatus;
use App\Enums\RefundStatus;
use App\Enums\ProductValueStatus;
use App\Services\DisputeService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RefundController extends Controller
{
    public function index(Request $request)
    {
        $seller = Auth::user();
        
        $disputeQuery = Dispute::where('seller_id', $seller->id)
            ->with(['order', 'orderItem.productVariant.product', 'buyer', 'items.productValue'])
            ->latest();
        
        if ($request->has('dispute_status') && $request->dispute_status) {
            $disputeQuery->where('status', $request->dispute_status);
        }

        if ($request->filled('dispute_date_from')) {
            $disputeQuery->whereDate('created_at', '>=', $request->dispute_date_from);
        }
        if ($request->filled('dispute_date_to')) {
            $disputeQuery->whereDate('created_at', '<=', $request->dispute_date_to);
        }

        if ($request->filled('dispute_search')) {
            $search = $request->dispute_search;
            $disputeQuery->where(function($q) use ($search) {
                $q->whereHas('order', function($q2) use ($search) {
                    $q2->where('slug', 'like', "%{$search}%");
                })
                ->orWhereHas('buyer', function($q2) use ($search) {
                    $q2->where('full_name', 'like', "%{$search}%")
                       ->orWhere('email', 'like', "%{$search}%");
                })
                ->orWhereHas('orderItem.productVariant.product', function($q2) use ($search) {
                    $q2->where('name', 'like', "%{$search}%");
                });
            });
        }

        if ($request->filled('dispute_amount_min')) {
            $disputeQuery->whereHas('order', function($q) use ($request) {
                $q->where('total_amount', '>=', $request->dispute_amount_min);
            });
        }
        if ($request->filled('dispute_amount_max')) {
            $disputeQuery->whereHas('order', function($q) use ($request) {
                $q->where('total_amount', '<=', $request->dispute_amount_max);
            });
        }
        
        $disputes = $disputeQuery->paginate(20);

        $refundQuery = Refund::whereHas('order', function($q) use ($seller) {
                $q->where('seller_id', $seller->id);
            })
            ->with(['order', 'buyer', 'items.productValue'])
            ->latest();

        if ($request->has('refund_status') && $request->refund_status) {
            $refundQuery->where('status', $request->refund_status);
        }

        if ($request->filled('refund_date_from')) {
            $refundQuery->whereDate('created_at', '>=', $request->refund_date_from);
        }
        if ($request->filled('refund_date_to')) {
            $refundQuery->whereDate('created_at', '<=', $request->refund_date_to);
        }

        if ($request->filled('refund_amount_min')) {
            $refundQuery->where('total_amount', '>=', $request->refund_amount_min);
        }
        if ($request->filled('refund_amount_max')) {
            $refundQuery->where('total_amount', '<=', $request->refund_amount_max);
        }

        $refunds = $refundQuery->paginate(20);

        return view('seller.pages.refunds.index', compact('disputes', 'refunds'));
    }

    public function showDispute(Dispute $dispute)
    {
        if ($dispute->seller_id !== Auth::id()) {
            abort(403);
        }

        $dispute->load([
            'order.items.productVariant.product',
            'orderItem.productVariant.product',
            'buyer',
            'items.productValue'
        ]);

        $disputedProductValues = $dispute->productValues;

        return view('seller.pages.refunds.dispute-detail', compact('dispute', 'disputedProductValues'));
    }

    public function acceptDispute(Request $request, Dispute $dispute)
    {
        if ($dispute->seller_id !== Auth::id()) {
            abort(403);
        }

        if ($dispute->status !== DisputeStatus::OPEN) {
            return response()->json([
                'success' => false,
                'message' => 'Khiếu nại này đã được xử lý hoặc không thể chấp nhận.'
            ], 422);
        }

        try {
            $refund = DisputeService::approveDispute($dispute, Auth::id());

            return response()->json([
                'success' => true,
                'message' => 'Đã chấp nhận khiếu nại và xử lý hoàn tiền thành công. Số tiền hoàn: ' . number_format($refund->total_amount, 0, ',', '.') . 'đ'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra: ' . $e->getMessage()
            ], 500);
        }
    }

    public function showRefund(Refund $refund)
    {
        $seller = Auth::user();

        if ($refund->order->seller_id !== $seller->id) {
            abort(403);
        }

        $refund->load([
            'order.items.productVariant.product',
            'buyer',
            'items.productValue'
        ]);

        $refundValueIds = $refund->items->pluck('product_value_id')->toArray();
        $dispute = Dispute::where('order_id', $refund->order_id)
            ->whereHas('items', function($q) use ($refundValueIds) {
                $q->whereIn('product_value_id', $refundValueIds);
            })
            ->with(['items.productValue'])
            ->latest()
            ->first();

        return view('seller.pages.refunds.refund-detail', compact('refund', 'dispute'));
    }

    public function rejectDispute(Request $request, Dispute $dispute)
    {
        if ($dispute->seller_id !== Auth::id()) {
            abort(403);
        }

        if ($dispute->status !== DisputeStatus::OPEN) {
            return response()->json([
                'success' => false,
                'message' => 'Khiếu nại này đã được xử lý.'
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
            DisputeService::sellerRejectDispute($dispute, $validated['seller_note']);

            return response()->json([
                'success' => true,
                'message' => 'Đã từ chối khiếu nại. Admin sẽ xem xét và đưa ra quyết định cuối cùng.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra: ' . $e->getMessage()
            ], 500);
        }
    }
}
