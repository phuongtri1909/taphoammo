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
        Schema::create('google_settings', function (Blueprint $table) {
            $table->id();
            $table->string('google_client_id');
            $table->text('google_client_secret');
            $table->string('google_redirect')->default('auth.google.callback');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('google_settings');
    }
};
