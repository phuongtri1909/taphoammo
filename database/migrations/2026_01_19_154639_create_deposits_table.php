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
        Schema::create('deposits', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('bank_id')->constrained()->onDelete('cascade');
            $table->string('transaction_code')->unique();
            $table->decimal('amount', 10, 2);
            $table->decimal('amount_received', 10, 2)->nullable()->comment('Số tiền nhận được');
            $table->string('status')->default('pending')->comment('pending, success, failed, cancelled');
            $table->text('note')->nullable()->comment('Ghi chú');
            $table->timestamp('processed_at')->nullable()->comment('Thời gian xử lý');
            $table->json('casso_response')->nullable()->comment('Response từ Casso API');
            $table->string('casso_transaction_id')->nullable()->comment('ID giao dịch từ Casso');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('deposits');
    }
};
