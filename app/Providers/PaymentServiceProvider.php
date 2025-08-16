<?php

namespace App\Providers;

use App\Services\Payment\PaymentProviderManager;
use Illuminate\Support\ServiceProvider;

class PaymentServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->singleton(PaymentProviderManager::class, function ($app) {
            return new PaymentProviderManager($app);
        });

        // Create an alias for easier access
        $this->app->alias(PaymentProviderManager::class, 'payment.manager');
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Publish config file
        $this->publishes([
            __DIR__.'/../../config/payment.php' => config_path('payment.php'),
        ], 'payment-config');

        // Merge config
        $this->mergeConfigFrom(
            __DIR__.'/../../config/payment.php', 'payment'
        );
    }
}
