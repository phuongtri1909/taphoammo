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
        Schema::create('auctions', function (Blueprint $table) {
            $table->id();
            $table->string('slug', 50)->unique();
            $table->string('title');
            $table->text('description')->nullable();
            
            // Thời gian
            $table->datetime('start_time');
            $table->datetime('end_time');
            
            // Giá khởi điểm
            $table->decimal('starting_price', 15, 2);
            
            // Thời gian hạ banner (số ngày)
            $table->integer('banner_duration_days');
            
            // Vị trí banner (left, right)
            $table->enum('banner_position', ['left', 'right'])->default('left');
            
            // Trạng thái
            $table->enum('status', ['pending', 'active', 'ended', 'cancelled'])->default('pending');
            
            // Người tạo (admin)
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            
            // Thông tin người thắng
            $table->foreignId('winner_id')->nullable()->constrained('users')->onDelete('set null');
            $table->decimal('winning_price', 15, 2)->nullable();
            $table->datetime('ended_at')->nullable();
            
            $table->timestamps();
            
            // Indexes
            $table->index('status');
            $table->index(['start_time', 'end_time'], 'auctions_time_idx');
            $table->index('banner_position');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('auctions');
    }
};
