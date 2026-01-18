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
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('is_seller_banned')->default(false)->after('role');
            $table->text('seller_ban_reason')->nullable()->after('is_seller_banned');
            $table->timestamp('seller_banned_at')->nullable()->after('seller_ban_reason');
            $table->foreignId('seller_banned_by')->nullable()->after('seller_banned_at')->constrained('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['seller_banned_by']);
            $table->dropColumn([
                'is_seller_banned',
                'seller_ban_reason',
                'seller_banned_at',
                'seller_banned_by',
            ]);
        });
    }
};
