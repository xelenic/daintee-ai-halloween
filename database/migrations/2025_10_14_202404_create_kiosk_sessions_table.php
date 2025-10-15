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
        Schema::create('kiosk_sessions', function (Blueprint $table) {
            $table->id();
            $table->string('session_id')->unique();
            $table->string('phone_number')->nullable();
            $table->string('original_image_path')->nullable();
            $table->string('processed_image_path')->nullable();
            $table->enum('status', ['started', 'phone_collected', 'photo_captured', 'photo_confirmed', 'processing', 'completed', 'failed'])->default('started');
            $table->json('gemini_response')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kiosk_sessions');
    }
};
