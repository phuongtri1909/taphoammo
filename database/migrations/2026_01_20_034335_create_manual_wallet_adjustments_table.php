<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('manual_wallet_adjustments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('slug')->unique();
            $table->string('adjustment_type');
            $table->decimal('amount', 15, 2);
            $table->text('reason')->nullable();
            $table->text('admin_note')->nullable();
            $table->foreignId('processed_by')->constrained('users')->onDelete('restrict');
            $table->timestamp('processed_at');
            $table->timestamps();
            $table->softDeletes();

            $table->index('user_id');
            $table->index('processed_by');
            $table->index('adjustment_type');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('manual_wallet_adjustments');
    }
};
