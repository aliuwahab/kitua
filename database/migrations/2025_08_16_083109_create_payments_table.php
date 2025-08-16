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
        Schema::create('payments', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('user_id')->constrained('users')->onDelete('cascade');
            
            // Polymorphic relationship - can be payment_request, group_payment_request, etc.
            $table->uuidMorphs('payable');
            
            $table->decimal('amount', 15, 2);
            $table->string('currency_code', 3);
            
            // Payment provider information
            $table->string('provider'); // e.g., 'paystack', 'flutterwave', 'momo', etc.
            $table->string('provider_reference')->nullable(); // Provider's transaction reference
            $table->string('provider_payment_method')->nullable(); // card, bank_transfer, mobile_money, etc.
            
            // Payment status
            $table->enum('status', [
                'pending',     // Payment initiated, waiting for provider response
                'processing',  // Payment is being processed by provider
                'completed',   // Payment successfully completed
                'failed',      // Payment failed
                'cancelled',   // Payment cancelled by user or system
                'refunded'     // Payment was refunded
            ])->default('pending');
            
            // Payment details
            $table->string('payment_method')->nullable(); // How user wants to pay (card, momo, bank, etc.)
            $table->string('phone_number')->nullable(); // For mobile money payments
            $table->string('account_number')->nullable(); // For bank transfers
            
            // Timestamps for payment lifecycle
            $table->timestamp('initiated_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamp('failed_at')->nullable();
            
            // Provider response and metadata
            $table->json('provider_response')->nullable(); // Full response from payment provider
            $table->json('metadata')->nullable(); // Additional data like user agent, IP, etc.
            
            // Failure information
            $table->string('failure_reason')->nullable();
            $table->text('failure_message')->nullable();
            
            $table->timestamps();
            $table->softDeletes();
            
            // Indexes for performance
            $table->index(['user_id', 'status']);
            // uuidMorphs already creates index for payable_type and payable_id
            $table->index(['provider', 'provider_reference']);
            $table->index(['status', 'created_at']);
            $table->index('phone_number');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
