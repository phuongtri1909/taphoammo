<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\SellerRegistration;
use App\Enums\SellerRegistrationStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class SellerController extends Controller
{

    public function index(Request $request)
    {
        $query = User::where('role', User::ROLE_SELLER)
            ->with(['sellerRegistration', 'products'])
            ->withCount('products');

        if ($request->has('status')) {
            if ($request->status === 'banned') {
                $query->where('is_seller_banned', true);
            } elseif ($request->status === 'active') {
                $query->where('is_seller_banned', false);
            }
        }

        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('full_name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $sellers = $query->orderByDesc('created_at')->paginate(20);
        
        $pendingCount = SellerRegistration::pending()->count();

        return view('admin.pages.sellers.index', compact('sellers', 'pendingCount'));
    }

    public function show(Request $request, User $seller)
    {
        if ($seller->role !== User::ROLE_SELLER) {
            abort(404);
        }

        $seller->load('sellerRegistration');

        $products = $seller->products()
            ->withSum('variants', 'sold_count')
            ->withSum('variants', 'stock_quantity')
            ->latest()
            ->paginate(10, ['*'], 'products_page');

        $stats = [
            'total_products' => $seller->products()->count(),
            'total_sold' => $seller->products()->withSum('variants', 'sold_count')->get()->sum('variants_sum_sold_count') ?? 0,
            'total_stock' => $seller->products()->withSum('variants', 'stock_quantity')->get()->sum('variants_sum_stock_quantity') ?? 0,
        ];

        return view('admin.pages.sellers.show', compact('seller', 'stats', 'products'));
    }

    public function ban(Request $request, User $seller)
    {
        if ($seller->role !== User::ROLE_SELLER) {
            return response()->json([
                'success' => false,
                'message' => 'Người dùng này không phải là seller.'
            ], 400);
        }

        if ($seller->isSellerBanned()) {
            return response()->json([
                'success' => false,
                'message' => 'Seller này đã bị khóa rồi.'
            ], 400);
        }

        $request->validate([
            'reason' => 'required|string|max:500',
        ], [
            'reason.required' => 'Vui lòng nhập lý do khóa.',
            'reason.max' => 'Lý do không được quá 500 ký tự.',
        ]);

        $seller->banSeller(Auth::user(), $request->reason);

        return response()->json([
            'success' => true,
            'message' => 'Đã khóa seller thành công.',
        ]);
    }

    public function unban(User $seller)
    {
        if ($seller->role !== User::ROLE_SELLER) {
            return response()->json([
                'success' => false,
                'message' => 'Người dùng này không phải là seller.'
            ], 400);
        }

        if (!$seller->isSellerBanned()) {
            return response()->json([
                'success' => false,
                'message' => 'Seller này chưa bị khóa.'
            ], 400);
        }

        $seller->unbanSeller();

        return response()->json([
            'success' => true,
            'message' => 'Đã mở khóa seller thành công.',
        ]);
    }

    public function pendingRegistrations()
    {
        $registrations = SellerRegistration::pending()
            ->with('user')
            ->orderByDesc('created_at')
            ->paginate(20);

        $pendingCount = $registrations->total();

        return view('admin.pages.sellers.registrations', compact('registrations', 'pendingCount'));
    }

    public function reviewRegistration(SellerRegistration $registration)
    {
        $registration->load('user');

        return view('admin.pages.sellers.review-registration', compact('registration'));
    }

    public function approveRegistration(Request $request, SellerRegistration $registration)
    {
        if (!$registration->isPending()) {
            return response()->json([
                'success' => false,
                'message' => 'Đơn đăng ký này đã được xử lý.'
            ], 400);
        }

        $registration->approve(Auth::user(), $request->note);

        return response()->json([
            'success' => true,
            'message' => 'Đã duyệt đơn đăng ký seller thành công.',
        ]);
    }

    public function rejectRegistration(Request $request, SellerRegistration $registration)
    {
        if (!$registration->isPending()) {
            return response()->json([
                'success' => false,
                'message' => 'Đơn đăng ký này đã được xử lý.'
            ], 400);
        }

        $request->validate([
            'note' => 'required|string|max:500',
        ], [
            'note.required' => 'Vui lòng nhập lý do từ chối.',
        ]);

        $registration->reject(Auth::user(), $request->note);

        return response()->json([
            'success' => true,
            'message' => 'Đã từ chối đơn đăng ký.',
        ]);
    }
}


