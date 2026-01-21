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
        Schema::create('auction_banners', function (Blueprint $table) {
            $table->id();
            $table->string('slug', 50)->unique();
            
            // Phiên đấu giá
            $table->foreignId('auction_id')->constrained()->onDelete('cascade');
            
            // Bid thắng
            $table->foreignId('auction_bid_id')->constrained()->onDelete('cascade');
            
            // Sản phẩm hoặc dịch vụ (polymorphic)
            $table->morphs('bannerable');
            
            // Vị trí banner
            $table->enum('position', ['left', 'right']);
            
            // Thời gian hiển thị
            $table->datetime('display_from');
            $table->datetime('display_until');
            
            // Thứ tự hiển thị
            $table->integer('display_order')->default(0);
            
            // Trạng thái
            $table->boolean('is_active')->default(true);
            
            $table->timestamps();
            
            // Indexes
            $table->index(['position', 'is_active', 'display_from', 'display_until'], 'auction_banners_display_idx');
            $table->index('display_order');
            // Note: morphs('bannerable') đã tự động tạo index cho bannerable_type và bannerable_id
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('auction_banners');
    }
};
