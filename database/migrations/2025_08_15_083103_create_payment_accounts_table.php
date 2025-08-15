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
        Schema::create('payment_accounts', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('user_id')->constrained()->onDelete('cascade');
            $table->enum('account_type', ['momo', 'bank'])->default('momo');
            $table->string('account_number')->index(); // Mobile number for momo, account number for bank
            $table->string('account_name')->nullable(); // Account holder name
            $table->string('provider')->nullable(); // MTN, Vodafone, Airtel for momo; Bank name for bank accounts
            $table->string('provider_code')->nullable(); // USSD codes or bank codes
            $table->boolean('is_primary')->default(false);
            $table->boolean('is_verified')->default(false);
            $table->timestamp('verified_at')->nullable();
            $table->json('metadata')->nullable(); // Additional account info
            $table->timestamps();
            
            // Ensure only one primary account per user
            $table->unique(['user_id', 'is_primary']);
            // Ensure unique account number per user and type
            $table->unique(['user_id', 'account_number', 'account_type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_accounts');
    }
};
