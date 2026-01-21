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
        Schema::create('reviews', function (Blueprint $table) {
            $table->id();
            $table->string('slug', 50)->unique();
            
            // Polymorphic: reviewable can be Product or Service
            $table->morphs('reviewable');
            
            // User who made the review
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            
            // Order reference (product order or service order)
            $table->string('order_type'); // 'product' or 'service'
            $table->unsignedBigInteger('order_id');
            
            // Review content
            $table->tinyInteger('rating')->unsigned(); // 1-5 stars
            $table->text('content')->nullable();
            
            // Status
            $table->boolean('is_visible')->default(true);
            
            $table->timestamps();
            
            // Indexes
            $table->index(['order_type', 'order_id']);
            $table->index(['reviewable_type', 'reviewable_id', 'is_visible']);
            $table->index('user_id');
            
            // Prevent duplicate reviews for the same order
            $table->unique(['order_type', 'order_id', 'user_id', 'reviewable_id'], 'unique_review_per_order');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reviews');
    }
};
