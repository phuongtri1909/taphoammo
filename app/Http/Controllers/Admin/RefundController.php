<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Refund;
use App\Enums\RefundStatus;
use App\Services\RefundService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class RefundController extends Controller
{
    protected $refundService;

    public function __construct(RefundService $refundService)
    {
        $this->refundService = $refundService;
    }

    public function index(Request $request)
    {
        $query = Refund::with(['order', 'buyer', 'items.productValue', 'processedBy'])
            ->latest();

        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }

        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->whereHas('order', function ($orderQ) use ($search) {
                    $orderQ->where('slug', 'like', "%{$search}%");
                })
                ->orWhereHas('buyer', function ($buyerQ) use ($search) {
                    $buyerQ->where('full_name', 'like', "%{$search}%")
                           ->orWhere('email', 'like', "%{$search}%");
                });
            });
        }

        $refunds = $query->paginate(20);

        return view('admin.pages.refunds.index', compact('refunds'));
    }

    public function show(Refund $refund)
    {
        $refund->load([
            'order.items.productVariant.product',
            'order.seller',
            'buyer',
            'items.productValue.orderItem.productVariant',
            'processedBy'
        ]);

        $refundValueIds = $refund->items->pluck('product_value_id')->toArray();
        $dispute = \App\Models\Dispute::where('order_id', $refund->order_id)
            ->whereHas('items', function($q) use ($refundValueIds) {
                $q->whereIn('product_value_id', $refundValueIds);
            })
            ->latest()
            ->first();

        return view('admin.pages.refunds.show', compact('refund', 'dispute'));
    }

    public function approve(Request $request, Refund $refund)
    {
        if ($refund->status !== RefundStatus::PENDING) {
            return response()->json([
                'success' => false,
                'message' => 'Hoàn trả này đã được xử lý.'
            ], 422);
        }

        try {
            DB::transaction(function () use ($refund) {
                $productValueIds = $refund->items->pluck('product_value_id')->toArray();

                $this->refundService->refund($productValueIds, Auth::id());
            });

            return response()->json([
                'success' => true,
                'message' => 'Đã phê duyệt và xử lý hoàn trả thành công.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra: ' . $e->getMessage()
            ], 500);
        }
    }

    public function reject(Request $request, Refund $refund)
    {
        if ($refund->status !== RefundStatus::PENDING) {
            return response()->json([
                'success' => false,
                'message' => 'Hoàn trả này đã được xử lý.'
            ], 422);
        }

        $validated = $request->validate([
            'admin_note' => 'required|string|min:10|max:500',
        ], [
            'admin_note.required' => 'Vui lòng nhập lý do từ chối.',
            'admin_note.min' => 'Lý do từ chối phải có ít nhất 10 ký tự.',
            'admin_note.max' => 'Lý do từ chối không được vượt quá 500 ký tự.',
        ]);

        try {
            $refundValueIds = $refund->items->pluck('product_value_id')->toArray();
            $dispute = \App\Models\Dispute::where('order_id', $refund->order_id)
                ->whereHas('items', function($q) use ($refundValueIds) {
                    $q->whereIn('product_value_id', $refundValueIds);
                })
                ->latest()
                ->first();

            if ($dispute) {
                $dispute->update([
                    'admin_note' => $validated['admin_note'],
                ]);
            }

            $refund->update([
                'status' => RefundStatus::REJECTED,
                'processed_by' => Auth::id(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Đã từ chối hoàn trả.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra: ' . $e->getMessage()
            ], 500);
        }
    }
}
