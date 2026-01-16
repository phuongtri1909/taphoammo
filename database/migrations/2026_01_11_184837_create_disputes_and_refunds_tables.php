<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('disputes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->onDelete('cascade');
            $table->foreignId('order_item_id')->constrained()->onDelete('cascade');
            $table->foreignId('product_value_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('buyer_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('seller_id')->constrained('users')->onDelete('cascade');
            $table->text('reason');
            $table->json('evidence')->nullable()->comment('JSON chứa bằng chứng');
            $table->string('status')->default('open');
            $table->timestamp('resolved_at')->nullable();
            $table->foreignId('resolved_by')->nullable()->constrained('users')->onDelete('set null')->comment('Admin ID');
            $table->timestamps();
            
            $table->index(['order_id', 'status']);
            $table->index('buyer_id');
            $table->index('seller_id');
        });

        Schema::create('refunds', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->onDelete('cascade');
            $table->foreignId('buyer_id')->constrained('users')->onDelete('cascade');
            $table->decimal('total_amount', 12, 2);
            $table->string('status')->default('pending');
            $table->foreignId('processed_by')->nullable()->constrained('users')->onDelete('set null')->comment('Admin ID');
            $table->timestamps();
            
            $table->index(['order_id', 'status']);
            $table->index('buyer_id');
        });

        Schema::create('refund_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('refund_id')->constrained()->onDelete('cascade');
            $table->foreignId('product_value_id')->constrained()->onDelete('cascade');
            $table->decimal('amount', 12, 2);
            $table->timestamps();
            
            $table->index('refund_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('refund_items');
        Schema::dropIfExists('refunds');
        Schema::dropIfExists('disputes');
    }
};