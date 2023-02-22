<?php

namespace Selmonal\LaravelSimplePayment;

use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use Selmonal\LaravelSimplePayment\Commands\LaravelSimplePaymentCommand;

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
            ->hasViews()
            ->hasMigration('create_laravel-simple-payment_table')
            ->hasCommand(LaravelSimplePaymentCommand::class);
    }
}
