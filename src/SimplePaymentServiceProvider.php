<?php

namespace Selmonal\SimplePayment;

use Illuminate\Support\Facades\Route;
use Selmonal\SimplePayment\Http\Controllers\CallbackController;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class SimplePaymentServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        /*
         * This class is a Package Service Provider
         *
         * More info: https://github.com/spatie/laravel-package-tools
         */
        $package
            ->name('simple-payment')
            ->hasConfigFile()
            ->hasViews()
            ->hasTranslations()
            ->hasMigration('create_simple-payment_table')
            ->hasCommands([
                Commands\ClearExpiredCommand::class,
            ]);

        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');
    }

    public function boot()
    {
        parent::boot();

        $this->registerRoutes();
    }

    private function registerRoutes()
    {
        Route::any('simple-payment/notification/{paymentId}', [CallbackController::class, 'handleNotification'])
            ->name('simple-payment.notification');

        Route::any('simple-payment/{paymentId}', [CallbackController::class, 'handleReturn'])
            ->name('simple-payment.return');
    }
}
