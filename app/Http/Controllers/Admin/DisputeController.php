<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Dispute;
use App\Enums\DisputeStatus;
use App\Services\DisputeService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DisputeController extends Controller
{
    public function index(Request $request)
    {
        $query = Dispute::with(['order', 'orderItem.productVariant.product', 'buyer', 'seller', 'resolvedBy'])
            ->latest();

        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        } else {
            $query->where('status', DisputeStatus::REVIEWING);
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
                })
                ->orWhereHas('seller', function ($sellerQ) use ($search) {
                    $sellerQ->where('full_name', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%");
                });
            });
        }

        $disputes = $query->paginate(20);

        return view('admin.pages.disputes.index', compact('disputes'));
    }

    public function show(Dispute $dispute)
    {
        if ($dispute->status === DisputeStatus::OPEN) {
            abort(403, 'Khiếu nại này đang chờ seller xử lý. Admin chỉ có thể xem sau khi seller từ chối.');
        }

        $dispute->load([
            'order.items.productVariant.product',
            'orderItem.productVariant.product',
            'buyer',
            'seller',
            'items.productValue',
            'resolvedBy'
        ]);

        $disputedProductValues = $dispute->productValues;

        return view('admin.pages.disputes.show', compact('dispute', 'disputedProductValues'));
    }

    public function approve(Request $request, Dispute $dispute)
    {
        if ($dispute->status !== DisputeStatus::REVIEWING) {
            if ($dispute->status === DisputeStatus::OPEN) {
                return response()->json([
                    'success' => false,
                    'message' => 'Khiếu nại này đang chờ seller xử lý. Admin chỉ có thể xử lý sau khi seller từ chối.'
                ], 422);
            }
            return response()->json([
                'success' => false,
                'message' => 'Tranh chấp này đã được xử lý.'
            ], 422);
        }

        $validated = $request->validate([
            'admin_note' => 'nullable|string|max:1000',
        ], [
            'admin_note.max' => 'Ghi chú không được vượt quá 1000 ký tự.',
        ]);

        try {
            $refund = DisputeService::approveDispute(
                $dispute, 
                Auth::id(), 
                $validated['admin_note'] ?? null
            );

            return response()->json([
                'success' => true,
                'message' => 'Đã phê duyệt tranh chấp và xử lý hoàn tiền thành công. Số tiền hoàn: ' . number_format($refund->total_amount, 0, ',', '.') . 'đ'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra: ' . $e->getMessage()
            ], 500);
        }
    }

    public function reject(Request $request, Dispute $dispute)
    {
        if ($dispute->status !== DisputeStatus::REVIEWING) {
            if ($dispute->status === DisputeStatus::OPEN) {
                return response()->json([
                    'success' => false,
                    'message' => 'Khiếu nại này đang chờ seller xử lý. Admin chỉ có thể xử lý sau khi seller từ chối.'
                ], 422);
            }
            return response()->json([
                'success' => false,
                'message' => 'Tranh chấp này đã được xử lý.'
            ], 422);
        }

        $validated = $request->validate([
            'admin_note' => 'required|string|min:10|max:1000',
        ], [
            'admin_note.required' => 'Vui lòng nhập lý do từ chối.',
            'admin_note.min' => 'Lý do từ chối phải có ít nhất 10 ký tự.',
            'admin_note.max' => 'Lý do từ chối không được vượt quá 1000 ký tự.',
        ]);

        try {
            DisputeService::rejectDispute($dispute, Auth::id(), $validated['admin_note']);

            return response()->json([
                'success' => true,
                'message' => 'Đã từ chối tranh chấp.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra: ' . $e->getMessage()
            ], 500);
        }
    }
}
