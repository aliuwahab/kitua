<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // For SQLite, we need to recreate the table with the new enum values
        // since SQLite doesn't support ALTER COLUMN for enum changes
        
        // First, create a temporary table with the new enum values
        Schema::create('users_temp', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('mobile_number')->nullable()->unique()->index();
            $table->string('email')->nullable()->unique();
            $table->string('first_name');
            $table->string('surname');
            $table->string('other_names')->nullable();
            $table->string('pin', 60)->nullable();
            $table->string('password')->nullable();
            $table->timestamp('mobile_verified_at')->nullable();
            $table->timestamp('email_verified_at')->nullable();
            $table->enum('user_type', ['customer', 'admin'])->default('customer'); // Updated enum values
            $table->boolean('is_active')->default(true);
            $table->foreignUuid('country_id')->constrained('countries')->onDelete('cascade');
            $table->rememberToken();
            $table->timestamps();
        });
        
        // Copy data from old table to new table, mapping old values to new values
        DB::statement("
            INSERT INTO users_temp 
            SELECT 
                id, mobile_number, email, first_name, surname, other_names, 
                pin, password, mobile_verified_at, email_verified_at,
                CASE 
                    WHEN user_type = 'mobile' THEN 'customer'
                    WHEN user_type = 'admin' THEN 'admin'
                    ELSE 'customer'
                END as user_type,
                is_active, country_id, remember_token, created_at, updated_at
            FROM users
        ");
        
        // Drop the old table
        Schema::dropIfExists('users');
        
        // Rename the temporary table to the original name
        Schema::rename('users_temp', 'users');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Reverse the process: recreate table with old enum values
        Schema::create('users_temp', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('mobile_number')->nullable()->unique()->index();
            $table->string('email')->nullable()->unique();
            $table->string('first_name');
            $table->string('surname');
            $table->string('other_names')->nullable();
            $table->string('pin', 60)->nullable();
            $table->string('password')->nullable();
            $table->timestamp('mobile_verified_at')->nullable();
            $table->timestamp('email_verified_at')->nullable();
            $table->enum('user_type', ['mobile', 'admin'])->default('mobile'); // Original enum values
            $table->boolean('is_active')->default(true);
            $table->foreignUuid('country_id')->constrained('countries')->onDelete('cascade');
            $table->rememberToken();
            $table->timestamps();
        });
        
        // Copy data back, mapping new values to old values
        DB::statement("
            INSERT INTO users_temp 
            SELECT 
                id, mobile_number, email, first_name, surname, other_names, 
                pin, password, mobile_verified_at, email_verified_at,
                CASE 
                    WHEN user_type = 'customer' THEN 'mobile'
                    WHEN user_type = 'admin' THEN 'admin'
                    ELSE 'mobile'
                END as user_type,
                is_active, country_id, remember_token, created_at, updated_at
            FROM users
        ");
        
        Schema::dropIfExists('users');
        Schema::rename('users_temp', 'users');
    }
};
