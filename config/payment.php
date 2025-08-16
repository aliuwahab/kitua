<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Default Payment Provider
    |--------------------------------------------------------------------------
    |
    | This option controls the default payment provider that will be used when
    | no specific provider is requested.
    |
    */
    'default_provider' => env('PAYMENT_DEFAULT_PROVIDER', 'dummy'),

    /*
    |--------------------------------------------------------------------------
    | Service Fee Rate
    |--------------------------------------------------------------------------
    |
    | This is Kitua's service fee rate that applies to all payments across
    | all providers. The value should be a decimal (e.g., 0.01 for 1%).
    |
    */
    'service_fee_rate' => env('PAYMENT_SERVICE_FEE_RATE', 0.01), // 1%

    /*
    |--------------------------------------------------------------------------
    | Payment Providers
    |--------------------------------------------------------------------------
    |
    | Here you may configure the payment providers for your application.
    | Each provider should have its own configuration array with the
    | necessary credentials and settings.
    |
    */
    'providers' => [
        'dummy' => [
            'enabled' => env('DUMMY_PAYMENT_ENABLED', true),
            'name' => 'Dummy Payment Provider',
            'description' => 'Test payment provider for development',
            'service_fee_rate' => env('DUMMY_SERVICE_FEE_RATE'), // Override global rate if needed
        ],

        'mtn_momo' => [
            'enabled' => env('MTN_MOMO_ENABLED', false),
            'name' => 'MTN Mobile Money',
            'description' => 'MTN Mobile Money payment provider',
            'base_url' => env('MTN_MOMO_BASE_URL', 'https://sandbox.momodeveloper.mtn.com'),
            'api_user' => env('MTN_MOMO_API_USER'),
            'api_key' => env('MTN_MOMO_API_KEY'),
            'subscription_key' => env('MTN_MOMO_SUBSCRIPTION_KEY'),
            'environment' => env('MTN_MOMO_ENVIRONMENT', 'sandbox'),
            'service_fee_rate' => env('MTN_MOMO_SERVICE_FEE_RATE'),
        ],

        'paystack' => [
            'enabled' => env('PAYSTACK_ENABLED', false),
            'name' => 'Paystack',
            'description' => 'Paystack payment gateway',
            'base_url' => env('PAYSTACK_BASE_URL', 'https://api.paystack.co'),
            'public_key' => env('PAYSTACK_PUBLIC_KEY'),
            'secret_key' => env('PAYSTACK_SECRET_KEY'),
            'service_fee_rate' => env('PAYSTACK_SERVICE_FEE_RATE'),
        ],

        'flutterwave' => [
            'enabled' => env('FLUTTERWAVE_ENABLED', false),
            'name' => 'Flutterwave',
            'description' => 'Flutterwave payment gateway',
            'base_url' => env('FLUTTERWAVE_BASE_URL', 'https://api.flutterwave.com/v3'),
            'public_key' => env('FLUTTERWAVE_PUBLIC_KEY'),
            'secret_key' => env('FLUTTERWAVE_SECRET_KEY'),
            'encryption_key' => env('FLUTTERWAVE_ENCRYPTION_KEY'),
            'service_fee_rate' => env('FLUTTERWAVE_SERVICE_FEE_RATE'),
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Webhook Settings
    |--------------------------------------------------------------------------
    |
    | Configure webhook settings for payment providers.
    |
    */
    'webhooks' => [
        'enabled' => env('PAYMENT_WEBHOOKS_ENABLED', true),
        'verify_signature' => env('PAYMENT_WEBHOOK_VERIFY_SIGNATURE', true),
    ],

    /*
    |--------------------------------------------------------------------------
    | Currency Settings
    |--------------------------------------------------------------------------
    |
    | Configure supported currencies and their display formats.
    |
    */
    'currencies' => [
        'default' => env('DEFAULT_CURRENCY', 'GHS'),
        'supported' => [
            'GHS' => ['name' => 'Ghana Cedi', 'symbol' => '₵'],
            'NGN' => ['name' => 'Nigerian Naira', 'symbol' => '₦'],
            'KES' => ['name' => 'Kenyan Shilling', 'symbol' => 'KSh'],
            'UGX' => ['name' => 'Ugandan Shilling', 'symbol' => 'USh'],
            'TZS' => ['name' => 'Tanzanian Shilling', 'symbol' => 'TSh'],
            'ZMW' => ['name' => 'Zambian Kwacha', 'symbol' => 'ZK'],
            'USD' => ['name' => 'US Dollar', 'symbol' => '$'],
            'EUR' => ['name' => 'Euro', 'symbol' => '€'],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Payment Limits
    |--------------------------------------------------------------------------
    |
    | Configure minimum and maximum payment amounts per currency.
    |
    */
    'limits' => [
        'min_amount' => [
            'GHS' => 1.00,
            'NGN' => 100.00,
            'KES' => 10.00,
            'UGX' => 1000.00,
            'USD' => 0.50,
            'EUR' => 0.50,
        ],
        'max_amount' => [
            'GHS' => 50000.00,
            'NGN' => 5000000.00,
            'KES' => 500000.00,
            'UGX' => 50000000.00,
            'USD' => 25000.00,
            'EUR' => 25000.00,
        ],
    ],
];
