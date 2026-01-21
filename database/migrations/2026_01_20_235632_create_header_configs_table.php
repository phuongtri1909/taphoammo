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
        Schema::create('header_configs', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique()->comment('support_bar, promotional_banner');
            $table->string('label')->nullable()->comment('Tên hiển thị trong admin');
            $table->json('config_data')->nullable()->comment('Dữ liệu cấu hình dạng JSON');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('header_configs');
    }
};
