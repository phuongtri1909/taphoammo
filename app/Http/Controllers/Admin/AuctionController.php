<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Auction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AuctionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Auction::with(['creator', 'winner'])
            ->orderBy('created_at', 'desc');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $auctions = $query->paginate(20);

        return view('admin.pages.auctions.index', compact('auctions'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.pages.auctions.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:2000',
            'start_time' => 'required|date|after:now',
            'end_time' => 'required|date|after:start_time',
            'starting_price' => 'required|numeric|min:0',
            'banner_duration_days' => 'required|integer|min:1|max:365',
            'banner_position' => 'required|in:left,right',
        ], [
            'title.required' => 'Tiêu đề phiên đấu giá là bắt buộc.',
            'start_time.required' => 'Thời gian bắt đầu là bắt buộc.',
            'start_time.after' => 'Thời gian bắt đầu phải sau thời điểm hiện tại.',
            'end_time.required' => 'Thời gian kết thúc là bắt buộc.',
            'end_time.after' => 'Thời gian kết thúc phải sau thời gian bắt đầu.',
            'starting_price.required' => 'Giá khởi điểm là bắt buộc.',
            'starting_price.min' => 'Giá khởi điểm phải lớn hơn 0.',
            'banner_duration_days.required' => 'Thời gian hạ banner là bắt buộc.',
            'banner_duration_days.min' => 'Thời gian hạ banner tối thiểu là 1 ngày.',
            'banner_duration_days.max' => 'Thời gian hạ banner tối đa là 365 ngày.',
            'banner_position.required' => 'Vị trí banner là bắt buộc.',
        ]);

        try {
            $auction = Auction::create([
                'title' => $request->title,
                'description' => $request->description,
                'start_time' => $request->start_time,
                'end_time' => $request->end_time,
                'starting_price' => $request->starting_price,
                'banner_duration_days' => $request->banner_duration_days,
                'banner_position' => $request->banner_position,
                'status' => 'pending',
                'created_by' => Auth::id(),
            ]);

            return redirect()->route('admin.auctions.index')
                ->with('success', 'Phiên đấu giá đã được tạo thành công!');
        } catch (\Exception $e) {
            return back()->withInput()
                ->with('error', 'Có lỗi xảy ra khi tạo phiên đấu giá: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Auction $auction)
    {
        $auction->load([
            'creator',
            'winner',
            'bids.seller',
            'bids.biddable',
            'banners.bid.seller',
            'banners.bannerable'
        ]);

        // Get top bids
        $topBids = $auction->activeBids()
            ->with(['seller', 'biddable'])
            ->get();

        return view('admin.pages.auctions.show', compact('auction', 'topBids'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Auction $auction)
    {
        // Only allow editing if auction hasn't started
        if ($auction->status !== 'pending' || now()->gte($auction->start_time)) {
            return redirect()->route('admin.auctions.index')
                ->with('error', 'Không thể chỉnh sửa phiên đấu giá đã bắt đầu hoặc đã kết thúc.');
        }

        return view('admin.pages.auctions.edit', compact('auction'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Auction $auction)
    {
        // Only allow editing if auction hasn't started
        if ($auction->status !== 'pending' || now()->gte($auction->start_time)) {
            return redirect()->route('admin.auctions.index')
                ->with('error', 'Không thể chỉnh sửa phiên đấu giá đã bắt đầu hoặc đã kết thúc.');
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:2000',
            'start_time' => 'required|date|after:now',
            'end_time' => 'required|date|after:start_time',
            'starting_price' => 'required|numeric|min:0',
            'banner_duration_days' => 'required|integer|min:1|max:365',
            'banner_position' => 'required|in:left,right',
        ], [
            'title.required' => 'Tiêu đề phiên đấu giá là bắt buộc.',
            'start_time.required' => 'Thời gian bắt đầu là bắt buộc.',
            'start_time.after' => 'Thời gian bắt đầu phải sau thời điểm hiện tại.',
            'end_time.required' => 'Thời gian kết thúc là bắt buộc.',
            'end_time.after' => 'Thời gian kết thúc phải sau thời gian bắt đầu.',
            'starting_price.required' => 'Giá khởi điểm là bắt buộc.',
            'starting_price.min' => 'Giá khởi điểm phải lớn hơn 0.',
            'banner_duration_days.required' => 'Thời gian hạ banner là bắt buộc.',
            'banner_duration_days.min' => 'Thời gian hạ banner tối thiểu là 1 ngày.',
            'banner_duration_days.max' => 'Thời gian hạ banner tối đa là 365 ngày.',
            'banner_position.required' => 'Vị trí banner là bắt buộc.',
        ]);

        try {
            $auction->update([
                'title' => $request->title,
                'description' => $request->description,
                'start_time' => $request->start_time,
                'end_time' => $request->end_time,
                'starting_price' => $request->starting_price,
                'banner_duration_days' => $request->banner_duration_days,
                'banner_position' => $request->banner_position,
            ]);

            return redirect()->route('admin.auctions.index')
                ->with('success', 'Phiên đấu giá đã được cập nhật thành công!');
        } catch (\Exception $e) {
            return back()->withInput()
                ->with('error', 'Có lỗi xảy ra khi cập nhật phiên đấu giá: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Auction $auction)
    {
        // Only allow deleting if auction hasn't started
        if ($auction->status !== 'pending' || now()->gte($auction->start_time)) {
            return redirect()->route('admin.auctions.index')
                ->with('error', 'Không thể xóa phiên đấu giá đã bắt đầu hoặc đã kết thúc.');
        }

        try {
            $auction->delete();

            return redirect()->route('admin.auctions.index')
                ->with('success', 'Phiên đấu giá đã được xóa thành công!');
        } catch (\Exception $e) {
            return back()
                ->with('error', 'Có lỗi xảy ra khi xóa phiên đấu giá: ' . $e->getMessage());
        }
    }

    /**
     * Start an auction manually
     */
    public function start(Auction $auction)
    {
        if ($auction->status !== 'pending') {
            return back()->with('error', 'Chỉ có thể bắt đầu phiên đấu giá ở trạng thái pending.');
        }

        $auction->update(['status' => 'active']);

        return back()->with('success', 'Phiên đấu giá đã được bắt đầu!');
    }

    /**
     * Cancel an auction
     */
    public function cancel(Auction $auction)
    {
        if (in_array($auction->status, ['ended', 'cancelled'])) {
            return back()->with('error', 'Không thể hủy phiên đấu giá đã kết thúc hoặc đã hủy.');
        }

        $auction->update(['status' => 'cancelled']);

        return back()->with('success', 'Phiên đấu giá đã được hủy!');
    }
}
