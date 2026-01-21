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
        Schema::create('terms_of_service', function (Blueprint $table) {
            $table->id();
            $table->string('title')->comment('Tiêu đề điều khoản');
            $table->text('content')->comment('Nội dung đầy đủ điều khoản');
            $table->text('summary')->nullable()->comment('Tóm tắt điều khoản hiển thị trong popup');
            $table->boolean('is_active')->default(true)->comment('Trạng thái kích hoạt');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('terms_of_service');
    }
};
