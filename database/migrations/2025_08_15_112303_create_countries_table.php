<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('countries', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name')->unique();
            $table->string('code', 2)->unique(); // ISO 3166-1 alpha-2 country code
            $table->string('currency_code', 3); // ISO 4217 currency code
            $table->string('currency_symbol', 10);
            $table->string('currency_name');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            $table->index('code');
            $table->index('currency_code');
        });

        // Seed initial country data
        $countries = [
            [
                'id' => Str::uuid(),
                'name' => 'Ghana',
                'code' => 'GH',
                'currency_code' => 'GHS',
                'currency_symbol' => 'GH₵',
                'currency_name' => 'Ghana Cedi',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => Str::uuid(),
                'name' => 'Nigeria',
                'code' => 'NG',
                'currency_code' => 'NGN',
                'currency_symbol' => '₦',
                'currency_name' => 'Nigerian Naira',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => Str::uuid(),
                'name' => 'Kenya',
                'code' => 'KE',
                'currency_code' => 'KES',
                'currency_symbol' => 'KSh',
                'currency_name' => 'Kenyan Shilling',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => Str::uuid(),
                'name' => 'South Africa',
                'code' => 'ZA',
                'currency_code' => 'ZAR',
                'currency_symbol' => 'R',
                'currency_name' => 'South African Rand',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => Str::uuid(),
                'name' => 'Uganda',
                'code' => 'UG',
                'currency_code' => 'UGX',
                'currency_symbol' => 'USh',
                'currency_name' => 'Ugandan Shilling',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => Str::uuid(),
                'name' => 'Tanzania',
                'code' => 'TZ',
                'currency_code' => 'TZS',
                'currency_symbol' => 'TSh',
                'currency_name' => 'Tanzanian Shilling',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => Str::uuid(),
                'name' => 'Rwanda',
                'code' => 'RW',
                'currency_code' => 'RWF',
                'currency_symbol' => 'FRw',
                'currency_name' => 'Rwandan Franc',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => Str::uuid(),
                'name' => 'Senegal',
                'code' => 'SN',
                'currency_code' => 'XOF',
                'currency_symbol' => 'CFA',
                'currency_name' => 'West African CFA Franc',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => Str::uuid(),
                'name' => 'Côte d\'Ivoire',
                'code' => 'CI',
                'currency_code' => 'XOF',
                'currency_symbol' => 'CFA',
                'currency_name' => 'West African CFA Franc',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => Str::uuid(),
                'name' => 'Morocco',
                'code' => 'MA',
                'currency_code' => 'MAD',
                'currency_symbol' => 'DH',
                'currency_name' => 'Moroccan Dirham',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => Str::uuid(),
                'name' => 'Egypt',
                'code' => 'EG',
                'currency_code' => 'EGP',
                'currency_symbol' => 'E£',
                'currency_name' => 'Egyptian Pound',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => Str::uuid(),
                'name' => 'United States',
                'code' => 'US',
                'currency_code' => 'USD',
                'currency_symbol' => '$',
                'currency_name' => 'US Dollar',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => Str::uuid(),
                'name' => 'United Kingdom',
                'code' => 'GB',
                'currency_code' => 'GBP',
                'currency_symbol' => '£',
                'currency_name' => 'British Pound',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => Str::uuid(),
                'name' => 'Canada',
                'code' => 'CA',
                'currency_code' => 'CAD',
                'currency_symbol' => 'C$',
                'currency_name' => 'Canadian Dollar',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('countries')->insert($countries);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('countries');
    }
};
