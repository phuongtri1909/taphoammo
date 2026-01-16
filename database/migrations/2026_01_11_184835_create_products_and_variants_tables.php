<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sub_category_id')->constrained()->onDelete('cascade');
            $table->foreignId('seller_id')->constrained('users')->onDelete('cascade');
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('image')->nullable();
            $table->text('description')->nullable();
            $table->longText('long_description')->nullable();
            $table->text('admin_note')->nullable();
            $table->string('status')->default('pending');
            $table->timestamps();
            $table->softDeletes();
            
            $table->index(['seller_id', 'status']);
            $table->index('sub_category_id');
        });

        Schema::create('product_variants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->decimal('price', 12, 2);
            $table->integer('stock_quantity')->default(0);
            $table->integer('sold_count')->default(0);
            $table->string('field_name')->comment('Field name do seller điều chỉnh: email, password, token...');
            $table->integer('order')->default(0);
            $table->string('status')->default('inactive');
            $table->timestamps();
            
            $table->index(['product_id', 'status']);
            $table->index('stock_quantity');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_variants');
        Schema::dropIfExists('products');
    }
};