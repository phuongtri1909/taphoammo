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
        Schema::create('featured_histories', function (Blueprint $table) {
            $table->id();
            $table->string('slug', 50)->unique();
            $table->foreignId('seller_id')->constrained('users')->onDelete('cascade');
            
            $table->morphs('featurable'); // featurable_type, featurable_id
            
            $table->decimal('amount', 15, 0)->comment('Số tiền đã thanh toán');
            $table->integer('hours')->comment('Số giờ đề xuất');
            
            $table->timestamp('featured_from')->comment('Thời gian bắt đầu đề xuất');
            $table->timestamp('featured_until')->comment('Thời gian kết thúc đề xuất');
            
            $table->text('note')->nullable()->comment('Ghi chú của seller');
            
            $table->timestamps();
            
            $table->index('seller_id');
            $table->index('featured_until');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('featured_histories');
    }
};
