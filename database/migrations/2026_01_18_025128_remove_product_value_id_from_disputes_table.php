<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('disputes', function (Blueprint $table) {
            $table->dropForeign(['product_value_id']);
            $table->dropColumn('product_value_id');
        });
    }

    public function down(): void
    {
        Schema::table('disputes', function (Blueprint $table) {
            $table->foreignId('product_value_id')->nullable()->constrained()->onDelete('set null')->after('order_item_id');
        });
    }
};
