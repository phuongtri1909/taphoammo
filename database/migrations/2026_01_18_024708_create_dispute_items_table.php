<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('dispute_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('dispute_id')->constrained()->onDelete('cascade');
            $table->foreignId('product_value_id')->constrained()->onDelete('cascade');
            $table->timestamps();
            
            $table->index('dispute_id');
            $table->index('product_value_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('dispute_items');
    }
};
