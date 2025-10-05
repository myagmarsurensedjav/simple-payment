<?php

namespace MyagmarsurenSedjav\SimplePayment;

use Illuminate\Support\Facades\Route;
use MyagmarsurenSedjav\SimplePayment\Contracts\RouteConfig;
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
            ->hasMigration('add_refund_columns_to_payments_table')
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

    private function registerRoutes(): void
    {
        Route::post('simple-payment/notification/pocket', [CallbackController::class, 'handlePocketNotification'])
            ->name(RouteConfig::ROUTE_NOTIFICATION_POCKET);

        Route::any('simple-payment/notification/{paymentId}', [CallbackController::class, 'handleNotification'])
            ->name(RouteConfig::ROUTE_NOTIFICATION);

        Route::any('simple-payment/{paymentId}', [CallbackController::class, 'handleReturn'])
            ->name(RouteConfig::ROUTE_RETURN);
    }
}
