<?php

namespace App\Http\Controllers\Admin;

use App\Models\Withdrawal;
use App\Enums\WithdrawalStatus;
use App\Services\WithdrawalService;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class WithdrawalController extends Controller
{
    protected WithdrawalService $withdrawalService;

    public function __construct(WithdrawalService $withdrawalService)
    {
        $this->withdrawalService = $withdrawalService;
    }

    public function index(Request $request)
    {
        $query = Withdrawal::with(['user', 'processedBy']);

        if (!$request->filled('status')) {
            $query->where('status', '!=', WithdrawalStatus::PENDING_OTP);
        } else {
            $query->where('status', $request->input('status'));
        }

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('slug', 'like', "%{$search}%")
                    ->orWhere('bank_account_number', 'like', "%{$search}%")
                    ->orWhere('bank_account_name', 'like', "%{$search}%")
                    ->orWhereHas('user', function ($q) use ($search) {
                        $q->where('full_name', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%");
                    });
            });
        }

        $withdrawals = $query->orderByDesc('created_at')->paginate(15);

        $counts = [
            'pending' => Withdrawal::where('status', WithdrawalStatus::PENDING)->count(),
            'processing' => Withdrawal::where('status', WithdrawalStatus::PROCESSING)->count(),
        ];

        return view('admin.pages.withdrawals.index', compact('withdrawals', 'counts'));
    }

    public function show(Withdrawal $withdrawal)
    {
        $withdrawal->load(['user', 'processedBy']);

        return view('admin.pages.withdrawals.show', compact('withdrawal'));
    }

    public function process(Withdrawal $withdrawal)
    {
        try {
            $this->withdrawalService->markProcessing($withdrawal, Auth::id());

            return response()->json([
                'success' => true,
                'message' => 'Đã đánh dấu đang xử lý'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }

    public function complete(Request $request, Withdrawal $withdrawal)
    {
        $request->validate([
            'admin_note' => 'nullable|string|max:1000',
        ]);

        try {
            $this->withdrawalService->complete(
                $withdrawal,
                Auth::id(),
                $request->input('admin_note')
            );

            return response()->json([
                'success' => true,
                'message' => 'Đã hoàn thành rút tiền'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }

    public function reject(Request $request, Withdrawal $withdrawal)
    {
        $request->validate([
            'admin_note' => 'required|string|max:1000',
        ], [
            'admin_note.required' => 'Vui lòng nhập lý do từ chối',
        ]);

        try {
            $this->withdrawalService->reject(
                $withdrawal,
                Auth::id(),
                $request->input('admin_note')
            );

            return response()->json([
                'success' => true,
                'message' => 'Đã từ chối và hoàn tiền về ví người dùng'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }
}
