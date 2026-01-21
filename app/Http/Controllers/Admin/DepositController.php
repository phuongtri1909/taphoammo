<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Deposit;
use App\Enums\DepositStatus;
use Illuminate\Http\Request;

class DepositController extends Controller
{
    /**
     * Display a listing of deposits
     */
    public function index(Request $request)
    {
        $query = Deposit::with(['user', 'bank']);

        if ($request->has('status')) {
            $statusFilter = $request->input('status');
            if ($statusFilter !== '' && $statusFilter !== null) {
                $query->where('status', $statusFilter);
            }
        } else {
            $query->where('status', DepositStatus::SUCCESS);
        }

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('slug', 'like', "%{$search}%")
                    ->orWhere('transaction_code', 'like', "%{$search}%")
                    ->orWhere('bank_account_number', 'like', "%{$search}%")
                    ->orWhereHas('user', function ($q) use ($search) {
                        $q->where('full_name', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%");
                    });
            });
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->input('date_from'));
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->input('date_to'));
        }

        $deposits = $query->orderByDesc('created_at')->paginate(15);

        $counts = [
            'pending' => Deposit::where('status', DepositStatus::PENDING)->count(),
            'success' => Deposit::where('status', DepositStatus::SUCCESS)->count(),
            'failed' => Deposit::where('status', DepositStatus::FAILED)->count(),
        ];

        return view('admin.pages.deposits.index', compact('deposits', 'counts'));
    }

    /**
     * Display the specified deposit
     */
    public function show(Deposit $deposit)
    {
        $deposit->load(['user', 'bank']);

        return view('admin.pages.deposits.show', compact('deposit'));
    }
}
