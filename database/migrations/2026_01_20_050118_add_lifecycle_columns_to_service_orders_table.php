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
        Schema::table('service_orders', function (Blueprint $table) {
            $table->timestamp('seller_confirmed_at')->nullable()->after('status')->comment('Ngày seller xác nhận đã hoàn thành lần đầu');
            $table->timestamp('seller_reconfirmed_at')->nullable()->after('seller_confirmed_at')->comment('Ngày seller báo lại đã hoàn thành sau khi giải quyết tranh chấp');
            $table->timestamp('last_dispute_resolved_at')->nullable()->after('seller_reconfirmed_at')->comment('Ngày giải quyết tranh chấp cuối cùng');
            $table->timestamp('last_dispute_created_at')->nullable()->after('last_dispute_resolved_at')->comment('Ngày tạo khiếu nại cuối cùng');
            
            $table->index('seller_confirmed_at');
            $table->index('seller_reconfirmed_at');
            $table->index('last_dispute_resolved_at');
            $table->index('last_dispute_created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('service_orders', function (Blueprint $table) {
            $table->dropIndex(['seller_confirmed_at']);
            $table->dropIndex(['seller_reconfirmed_at']);
            $table->dropIndex(['last_dispute_resolved_at']);
            $table->dropIndex(['last_dispute_created_at']);
            
            $table->dropColumn([
                'seller_confirmed_at',
                'seller_reconfirmed_at',
                'last_dispute_resolved_at',
                'last_dispute_created_at'
            ]);
        });
    }
};
