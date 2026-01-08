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
            $table->timestamp('last_activation_email_sent_at')->nullable()->after('key_active');
            $table->timestamp('last_reset_password_email_sent_at')->nullable()->after('reset_password_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['last_activation_email_sent_at', 'last_reset_password_email_sent_at']);
        });
    }
};
