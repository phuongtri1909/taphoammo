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
        Schema::create('service_disputes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('service_order_id')->constrained()->onDelete('cascade');
            $table->foreignId('buyer_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('seller_id')->constrained('users')->onDelete('cascade');
            $table->string('slug')->unique();
            $table->text('reason');
            $table->text('seller_note')->nullable();
            $table->text('admin_note')->nullable();
            $table->json('evidence')->nullable()->comment('JSON chứa bằng chứng');
            $table->json('evidence_files')->nullable()->comment('JSON chứa paths của files đã upload');
            $table->string('status')->default('open');
            $table->timestamp('resolved_at')->nullable();
            $table->foreignId('resolved_by')->nullable()->constrained('users')->onDelete('set null')->comment('Admin ID');
            $table->timestamps();
            
            $table->index(['service_order_id', 'status']);
            $table->index('buyer_id');
            $table->index('seller_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('service_disputes');
    }
};
