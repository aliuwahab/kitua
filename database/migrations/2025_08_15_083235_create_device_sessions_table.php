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
        Schema::create('device_sessions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('user_id')->constrained()->onDelete('cascade');
            $table->string('device_id')->index(); // Unique device identifier
            $table->string('device_name')->nullable(); // User-friendly device name
            $table->string('device_type')->nullable(); // android, ios, web
            $table->string('device_fingerprint')->index(); // Combined device characteristics
            $table->string('app_version')->nullable();
            $table->string('os_version')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->string('user_agent')->nullable();
            $table->string('push_token')->nullable(); // For notifications
            $table->timestamp('first_login_at');
            $table->timestamp('last_activity_at');
            $table->boolean('is_trusted')->default(false);
            $table->boolean('is_active')->default(true);
            $table->timestamp('revoked_at')->nullable();
            $table->json('metadata')->nullable(); // Additional device info
            $table->timestamps();
            
            // Ensure unique device per user
            $table->unique(['user_id', 'device_fingerprint']);
            // Index for security queries
            $table->index(['device_fingerprint', 'is_active']);
            $table->index(['user_id', 'is_active']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('device_sessions');
    }
};
