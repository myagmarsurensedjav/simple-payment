<?php

namespace Selmonal\LaravelSimplePayment;

use Illuminate\Support\Facades\Route;
use Selmonal\LaravelSimplePayment\Http\Controllers\QpayWebhookController;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class LaravelSimplePaymentServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        /*
         * This class is a Package Service Provider
         *
         * More info: https://github.com/spatie/laravel-package-tools
         */
        $package
            ->name('laravel-simple-payment')
            ->hasConfigFile()
            ->hasMigration('create_laravel-simple-payment_table');

        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
    }

    public function boot()
    {
        parent::boot();

        Route::get('payments/qpay/webhook/{paymentId}', [QpayWebhookController::class, 'handle'])->name('qpay.webhook');
    }
}
