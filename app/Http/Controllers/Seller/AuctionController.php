<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Models\Auction;
use App\Models\AuctionBid;
use App\Models\Product;
use App\Models\Service;
use App\Services\WalletService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class AuctionController extends Controller
{
    /**
     * Display list of active auctions
     */
    public function index(Request $request)
    {
        $user = Auth::user();

        // Get active auctions
        $query = Auction::where('status', 'active')
            ->where('start_time', '<=', now())
            ->where('end_time', '>=', now())
            ->orderBy('end_time', 'asc');

        if ($request->filled('position')) {
            $query->where('banner_position', $request->position);
        }

        $auctions = $query->get();

        // Get user's bids for these auctions
        $userBids = AuctionBid::where('seller_id', $user->id)
            ->whereIn('auction_id', $auctions->pluck('id'))
            ->get()
            ->keyBy('auction_id');

        return view('seller.pages.auctions.index', compact('auctions', 'userBids'));
    }

    /**
     * Show auction details and bidding form
     */
    public function show(Auction $auction)
    {
        $user = Auth::user();

        // Check if auction is active
        if (!$auction->isActive()) {
            return redirect()->route('seller.auctions.index')
                ->with('error', 'Phiên đấu giá không còn hoạt động.');
        }

        // Get user's products and services
        $products = Product::where('seller_id', $user->id)
            ->visibleToClient()
            ->get();

        $services = Service::where('seller_id', $user->id)
            ->visibleToClient()
            ->get();

        // Get current top bid
        $topBid = $auction->topBid();
        $currentHighestBid = $auction->current_highest_bid;

        // Get user's current bid for this auction
        $userBid = AuctionBid::where('auction_id', $auction->id)
            ->where('seller_id', $user->id)
            ->where('status', 'active')
            ->first();

        // Get top bids (for display)
        $topBids = $auction->activeBids()
            ->with(['seller', 'biddable'])
            ->limit(10)
            ->get();

        return view('seller.pages.auctions.show', compact(
            'auction',
            'products',
            'services',
            'topBid',
            'currentHighestBid',
            'userBid',
            'topBids'
        ));
    }

    /**
     * Place a bid
     */
    public function bid(Request $request, Auction $auction)
    {
        $user = Auth::user();

        // Validate auction is active
        if (!$auction->isActive()) {
            return response()->json([
                'success' => false,
                'error' => 'Phiên đấu giá không còn hoạt động.'
            ], 400);
        }

        $request->validate([
            'biddable_type' => 'required|in:App\Models\Product,App\Models\Service',
            'biddable_id' => 'required|integer',
            'bid_amount' => 'required|numeric|min:0',
        ], [
            'biddable_type.required' => 'Vui lòng chọn sản phẩm hoặc dịch vụ.',
            'biddable_id.required' => 'Vui lòng chọn sản phẩm hoặc dịch vụ.',
            'bid_amount.required' => 'Vui lòng nhập giá đấu.',
            'bid_amount.numeric' => 'Giá đấu phải là số.',
            'bid_amount.min' => 'Giá đấu phải lớn hơn 0.',
        ]);

        // Get current highest bid
        $currentHighestBid = $auction->current_highest_bid;

        // Validate bid amount is higher
        if ($request->bid_amount <= $currentHighestBid) {
            return response()->json([
                'success' => false,
                'error' => 'Giá đấu phải cao hơn giá hiện tại: ' . number_format($currentHighestBid, 0, ',', '.') . '₫'
            ], 400);
        }

        // Verify biddable belongs to user
        $biddable = null;
        if ($request->biddable_type === 'App\Models\Product') {
            $biddable = Product::where('id', $request->biddable_id)
                ->where('seller_id', $user->id)
                ->first();
        } else {
            $biddable = Service::where('id', $request->biddable_id)
                ->where('seller_id', $user->id)
                ->first();
        }

        if (!$biddable) {
            return response()->json([
                'success' => false,
                'error' => 'Sản phẩm/dịch vụ không tồn tại hoặc không thuộc về bạn.'
            ], 400);
        }

        // Check wallet balance (warning only, don't deduct yet)
        $walletService = new WalletService();
        $balance = $walletService->getBalance($user->id);

        if ($balance < $request->bid_amount) {
            return response()->json([
                'success' => false,
                'error' => 'Số dư ví không đủ. Số dư hiện tại: ' . number_format($balance, 0, ',', '.') . '₫'
            ], 400);
        }

        try {
            DB::beginTransaction();

            // Mark old bid as outbid if exists
            $oldBid = AuctionBid::where('auction_id', $auction->id)
                ->where('seller_id', $user->id)
                ->where('status', 'active')
                ->first();

            if ($oldBid) {
                $oldBid->markAsOutbid();
            }

            // Create new bid
            $bid = AuctionBid::create([
                'auction_id' => $auction->id,
                'seller_id' => $user->id,
                'biddable_type' => $request->biddable_type,
                'biddable_id' => $request->biddable_id,
                'bid_amount' => $request->bid_amount,
                'status' => 'active',
            ]);

            // Mark all other bids as outbid
            AuctionBid::where('auction_id', $auction->id)
                ->where('id', '!=', $bid->id)
                ->where('status', 'active')
                ->update(['status' => 'outbid']);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Đấu giá thành công!',
                'bid' => [
                    'id' => $bid->id,
                    'amount' => $bid->bid_amount,
                    'current_highest' => $bid->bid_amount,
                ]
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'error' => 'Có lỗi xảy ra: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get user's bidding history
     */
    public function history()
    {
        $user = Auth::user();

        $bids = AuctionBid::where('seller_id', $user->id)
            ->with(['auction', 'biddable'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('seller.pages.auctions.history', compact('bids'));
    }
}
