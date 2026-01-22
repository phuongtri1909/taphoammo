<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Order;
use App\Models\ServiceOrder;
use App\Models\Deposit;
use App\Models\Withdrawal;
use App\Models\WalletTransaction;
use App\Enums\WalletTransactionType;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::withCount('products')->with('wallet');

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('full_name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($request->filled('role')) {
            $query->where('role', $request->input('role'));
        }

        if ($request->filled('status')) {
            if ($request->input('status') === 'active') {
                $query->where('active', 1);
            } elseif ($request->input('status') === 'inactive') {
                $query->where('active', 0);
            } elseif ($request->input('status') === 'banned') {
                $query->where('is_seller_banned', true);
            }
        }

        $users = $query->latest()->paginate(20);

        return view('admin.pages.users.index', compact('users'));
    }

    public function show(User $user)
    {
        $user->load('wallet', 'sellerRegistration');

        $productOrders = Order::where('buyer_id', $user->id)
            ->with(['seller', 'items.productVariant.product'])
            ->latest()
            ->paginate(10, ['*'], 'product_orders');

        $serviceOrders = ServiceOrder::where('buyer_id', $user->id)
            ->with(['seller', 'serviceVariant.service'])
            ->latest()
            ->paginate(10, ['*'], 'service_orders');

        $totalSpent = Order::where('buyer_id', $user->id)->sum('total_amount')
            + ServiceOrder::where('buyer_id', $user->id)->sum('total_amount');

        $deposits = Deposit::where('user_id', $user->id)
            ->latest()
            ->paginate(10, ['*'], 'deposits');
        $totalDeposited = Deposit::where('user_id', $user->id)
            ->where('status', \App\Enums\DepositStatus::SUCCESS)
            ->sum('amount');

        $withdrawals = null;
        $totalWithdrawn = 0;
        if ($user->role === User::ROLE_SELLER) {
            $withdrawals = Withdrawal::where('user_id', $user->id)
                ->latest()
                ->paginate(10, ['*'], 'withdrawals');
            $totalWithdrawn = Withdrawal::where('user_id', $user->id)
                ->where('status', \App\Enums\WithdrawalStatus::COMPLETED)
                ->sum('amount');
        }

        $walletTransactions = null;
        if ($user->wallet) {
            $walletTransactions = WalletTransaction::where('wallet_id', $user->wallet->id)
                ->with(['order', 'serviceOrder', 'deposit', 'withdrawal', 'refund'])
                ->latest()
                ->paginate(20, ['*'], 'transactions');
        }

        $salesStats = null;
        if ($user->role === User::ROLE_SELLER) {
            $salesStats = [
                'total_products' => $user->products()->count(),
                'total_product_orders' => Order::where('seller_id', $user->id)->count(),
                'total_service_orders' => ServiceOrder::where('seller_id', $user->id)->count(),
                'total_product_sales' => Order::where('seller_id', $user->id)->sum('total_amount'),
                'total_service_sales' => ServiceOrder::where('seller_id', $user->id)->sum('total_amount'),
            ];
        }

        return view('admin.pages.users.show', compact(
            'user',
            'productOrders',
            'serviceOrders',
            'totalSpent',
            'deposits',
            'totalDeposited',
            'withdrawals',
            'totalWithdrawn',
            'walletTransactions',
            'salesStats'
        ));
    }
}
