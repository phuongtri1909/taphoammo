<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use App\Models\ManualWalletAdjustment;
use App\Enums\ManualAdjustmentType;
use App\Services\ManualWalletAdjustmentService;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class ManualWalletAdjustmentController extends Controller
{
    protected ManualWalletAdjustmentService $adjustmentService;

    public function __construct(ManualWalletAdjustmentService $adjustmentService)
    {
        $this->adjustmentService = $adjustmentService;
    }

    public function index(Request $request)
    {
        $query = ManualWalletAdjustment::with(['user', 'processedBy']);

        if ($request->filled('user_search')) {
            $search = $request->input('user_search');
            $query->whereHas('user', function ($q) use ($search) {
                $q->where('full_name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($request->filled('type')) {
            $query->where('adjustment_type', $request->input('type'));
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->input('date_from'));
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->input('date_to'));
        }

        $adjustments = $query->orderByDesc('created_at')->paginate(15);

        return view('admin.pages.manual-wallet-adjustments.index', compact('adjustments'));
    }

    public function create()
    {
        return view('admin.pages.manual-wallet-adjustments.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_email' => 'required|email',
            'type' => 'required|in:add,subtract',
            'amount' => 'required|numeric|min:0.01',
            'reason' => 'required|string|max:500',
            'admin_note' => 'nullable|string|max:1000',
        ], [
            'user_email.required' => 'Vui lòng chọn người dùng',
            'user_email.email' => 'Email không hợp lệ',
            'type.required' => 'Vui lòng chọn loại điều chỉnh',
            'type.in' => 'Loại điều chỉnh không hợp lệ',
            'amount.required' => 'Vui lòng nhập số tiền',
            'amount.numeric' => 'Số tiền phải là số',
            'amount.min' => 'Số tiền phải lớn hơn 0',
            'reason.required' => 'Vui lòng nhập lý do',
            'reason.max' => 'Lý do không được vượt quá 500 ký tự',
            'admin_note.max' => 'Ghi chú không được vượt quá 1000 ký tự',
        ]);

        try {
            $user = User::where('email', $validated['user_email'])->first();
            
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Không tìm thấy người dùng với email này'
                ], 400);
            }

            $type = $validated['type'] === 'add'
                ? ManualAdjustmentType::ADD
                : ManualAdjustmentType::SUBTRACT;

            $adjustment = $this->adjustmentService->createAdjustment(
                $user->id,
                $type,
                $validated['amount'],
                $validated['reason'],
                $validated['admin_note'] ?? null,
                Auth::id()
            );

            return response()->json([
                'success' => true,
                'message' => $type === ManualAdjustmentType::ADD
                    ? 'Đã cộng tiền thành công'
                    : 'Đã trừ tiền thành công',
                'redirect' => route('admin.manual-wallet-adjustments.show', $adjustment->slug)
            ]);

        } catch (\DomainException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        } catch (\Exception $e) {
            Log::error('lỗi khi thực hiện điều chỉnh ví tiền', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Đã có lỗi xảy ra. Vui lòng thử lại sau.'
            ], 500);
        }
    }

    public function show(ManualWalletAdjustment $manualWalletAdjustment)
    {
        $manualWalletAdjustment->load(['user', 'processedBy', 'walletTransaction']);

        return view('admin.pages.manual-wallet-adjustments.show', [
            'adjustment' => $manualWalletAdjustment
        ]);
    }

    public function searchUsers(Request $request)
    {
        $search = $request->input('q', '');
        
        if (strlen($search) < 2) {
            return response()->json([]);
        }

        $users = User::where('full_name', 'like', "%{$search}%")
            ->orWhere('email', 'like', "%{$search}%")
            ->select('id', 'full_name', 'email')
            ->limit(20)
            ->get()
            ->map(function ($user) {
                return [
                    'id' => $user->id,
                    'full_name' => $user->full_name,
                    'email' => $user->email,
                    'display' => "{$user->full_name} ({$user->email})"
                ];
            });

        return response()->json($users);
    }
}
