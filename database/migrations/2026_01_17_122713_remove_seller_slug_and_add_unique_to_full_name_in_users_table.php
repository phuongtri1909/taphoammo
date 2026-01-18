<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('seller_slug');
        });

        $duplicates = DB::table('users')
            ->select('full_name', DB::raw('COUNT(*) as count'))
            ->groupBy('full_name')
            ->having('count', '>', 1)
            ->get();

        if ($duplicates->count() > 0) {
            throw new \Exception('Tên người bán không được trùng lặp.');
        }

        Schema::table('users', function (Blueprint $table) {
            $table->unique('full_name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropUnique(['full_name']);
            
            $table->string('seller_slug')->nullable()->unique()->after('full_name');
        });
    }
};
