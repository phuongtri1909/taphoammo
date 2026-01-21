<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Models\ServiceDispute;
use App\Enums\ServiceDisputeStatus;
use App\Enums\ServiceOrderStatus;
use App\Services\ServiceOrderService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ServiceDisputeController extends Controller
{
    /**
     * Danh sách khiếu nại dịch vụ của seller
     */
    public function index(Request $request)
    {
        $seller = Auth::user();
        
        $query = ServiceDispute::where('seller_id', $seller->id)
            ->with(['serviceOrder.buyer', 'serviceOrder.serviceVariant.service'])
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
                  });
            });
        }

        $disputes = $query->paginate(20);

        return view('seller.pages.service-disputes.index', compact('disputes'));
    }

    /**
     * Chi tiết khiếu nại
     */
    public function show(ServiceDispute $dispute)
    {
        $seller = Auth::user();
        
        if ($dispute->seller_id !== $seller->id) {
            abort(403);
        }

        $dispute->load([
            'serviceOrder.buyer',
            'serviceOrder.seller', 
            'serviceOrder.serviceVariant.service',
            'resolvedBy'
        ]);

        return view('seller.pages.service-disputes.show', compact('dispute'));
    }

    /**
     * Seller chấp nhận giải quyết khiếu nại
     */
    public function accept(Request $request, ServiceDispute $dispute)
    {
        $seller = Auth::user();
        
        if ($dispute->seller_id !== $seller->id) {
            return response()->json([
                'success' => false,
                'message' => 'Bạn không có quyền thực hiện hành động này.'
            ], 403);
        }

        if ($dispute->status !== ServiceDisputeStatus::OPEN) {
            return response()->json([
                'success' => false,
                'message' => 'Khiếu nại không ở trạng thái có thể giải quyết.'
            ], 422);
        }

        $validated = $request->validate([
            'seller_note' => 'nullable|string|max:1000',
        ]);

        try {
            DB::transaction(function () use ($dispute, $seller, $validated) {
                $dispute->update([
                    'status' => ServiceDisputeStatus::APPROVED,
                    'seller_note' => $validated['seller_note'] ?? null,
                    'resolved_at' => now(),
                    'resolved_by' => $seller->id,
                ]);

                // Reset deadline và chuyển status về SELLER_CONFIRMED
                $serviceOrderService = new ServiceOrderService();
                $serviceOrderService->resetDeadlineOnDisputeResolved($dispute->serviceOrder);
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
    public function reject(Request $request, ServiceDispute $dispute)
    {
        $seller = Auth::user();
        
        if ($dispute->seller_id !== $seller->id) {
            return response()->json([
                'success' => false,
                'message' => 'Bạn không có quyền thực hiện hành động này.'
            ], 403);
        }

        if ($dispute->status !== ServiceDisputeStatus::OPEN) {
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
                'status' => ServiceDisputeStatus::REVIEWING,
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
