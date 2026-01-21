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
        Schema::create('service_refunds', function (Blueprint $table) {
            $table->id();
            $table->foreignId('service_order_id')->constrained()->onDelete('cascade');
            $table->foreignId('buyer_id')->constrained('users')->onDelete('cascade');
            $table->string('slug')->unique();
            $table->decimal('total_amount', 12, 2);
            $table->string('status')->default('pending');
            $table->foreignId('processed_by')->nullable()->constrained('users')->onDelete('set null')->comment('Admin ID');
            $table->timestamps();
            
            $table->index(['service_order_id', 'status']);
            $table->index('buyer_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('service_refunds');
    }
};
