<?php

namespace MyagmarsurenSedjav\SimplePayment;

use Illuminate\Support\Facades\Route;
use MyagmarsurenSedjav\SimplePayment\Http\Controllers\CallbackController;
use Spatie\LaravelPackageTools\Commands\InstallCommand;
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
            ->hasMigration('create_payments_table')
            ->hasCommands([
                Commands\ClearExpiredCommand::class,
            ])
            ->hasInstallCommand(function (InstallCommand $command) {
                $command
                    ->publishConfigFile()
                    ->publishMigrations()
                    ->askToRunMigrations()
                    ->askToStarRepoOnGitHub('myagmarsurensedjav/simple-payment');
            });
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
