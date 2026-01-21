<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FeaturedHistory;
use App\Models\Product;
use App\Models\Service;
use Illuminate\Http\Request;

class FeaturedHistoryController extends Controller
{
    public function index(Request $request)
    {
        $query = FeaturedHistory::with(['seller', 'featurable'])
            ->orderByDesc('created_at');

        if ($request->filled('type') && in_array($request->type, ['product', 'service'])) {
            $query->byType($request->type);
        }

        if ($request->filled('seller')) {
            $query->whereHas('seller', function ($q) use ($request) {
                $q->where('full_name', 'like', '%' . $request->seller . '%')
                  ->orWhere('email', 'like', '%' . $request->seller . '%');
            });
        }

        if ($request->filled('search')) {
            $searchTerm = $request->search;
            $query->where(function ($q) use ($searchTerm) {
                $q->whereHasMorph('featurable', [Product::class, Service::class], function ($morphQuery) use ($searchTerm) {
                    $morphQuery->where('name', 'like', '%' . $searchTerm . '%');
                })
                ->orWhere('slug', 'like', '%' . $searchTerm . '%');
            });
        }

        if ($request->filled('status')) {
            if ($request->status === 'active') {
                $query->active();
            } elseif ($request->status === 'expired') {
                $query->where('featured_until', '<=', now());
            }
        }

        if ($request->filled('from_date')) {
            $query->whereDate('created_at', '>=', $request->from_date);
        }
        if ($request->filled('to_date')) {
            $query->whereDate('created_at', '<=', $request->to_date);
        }

        $featuredHistories = $query->paginate(15)->withQueryString();

        $totalRevenue = FeaturedHistory::sum('amount');
        $totalActive = FeaturedHistory::active()->count();
        $totalProduct = FeaturedHistory::byType('product')->count();
        $totalService = FeaturedHistory::byType('service')->count();

        return view('admin.pages.featured-histories.index', compact(
            'featuredHistories',
            'totalRevenue',
            'totalActive',
            'totalProduct',
            'totalService'
        ));
    }

    public function show(FeaturedHistory $featuredHistory)
    {
        $featuredHistory->load(['seller', 'featurable']);

        return view('admin.pages.featured-histories.show', compact('featuredHistory'));
    }
}
