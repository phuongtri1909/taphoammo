<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('auction_bids', function (Blueprint $table) {
            $table->id();
            $table->string('slug', 50)->unique();
            
            // Phiên đấu giá
            $table->foreignId('auction_id')->constrained()->onDelete('cascade');
            
            // Người đấu giá (seller)
            $table->foreignId('seller_id')->constrained('users')->onDelete('cascade');
            
            // Sản phẩm hoặc dịch vụ đấu giá (polymorphic)
            $table->morphs('biddable');
            
            // Giá đấu
            $table->decimal('bid_amount', 15, 2);
            
            // Thứ hạng tại thời điểm đấu giá
            $table->integer('rank_at_bid')->nullable();
            
            // Trạng thái
            $table->enum('status', ['active', 'outbid', 'won', 'invalid'])->default('active');
            
            $table->timestamps();
            
            // Indexes
            $table->index(['auction_id', 'status'], 'auction_bids_auction_status_idx');
            $table->index(['auction_id', 'bid_amount'], 'auction_bids_auction_amount_idx');
            $table->index('seller_id');
            // Note: morphs('biddable') đã tự động tạo index cho biddable_type và biddable_id
            
            // Unique: Mỗi seller chỉ có 1 bid active trong 1 auction
            $table->unique(['auction_id', 'seller_id', 'status'], 'unique_active_bid_per_seller');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('auction_bids');
    }
};
