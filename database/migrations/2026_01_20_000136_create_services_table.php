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
        Schema::create('services', function (Blueprint $table) {
            $table->id();
            $table->foreignId('service_sub_category_id')->constrained('service_sub_categories')->onDelete('cascade');
            $table->foreignId('seller_id')->constrained('users')->onDelete('cascade');
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('image')->nullable();
            $table->text('description')->nullable();
            $table->longText('long_description')->nullable();
            $table->text('admin_note')->nullable();
            $table->string('status')->default('pending');
            $table->timestamp('featured_until')->nullable();
            $table->timestamps();
            $table->softDeletes();
            
            $table->index(['seller_id', 'status']);
            $table->index('service_sub_category_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('services');
    }
};
