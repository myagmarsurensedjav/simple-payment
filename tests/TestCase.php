<?php

namespace Selmonal\SimplePayment\Tests;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Schema;
use Orchestra\Testbench\TestCase as Orchestra;
use Selmonal\SimplePayment\SimplePaymentServiceProvider;
use Selmonal\SimplePayment\Tests\Support\TestUser;

class TestCase extends Orchestra
{
    protected function setUp(): void
    {
        parent::setUp();

        Factory::guessFactoryNamesUsing(
            fn (string $modelName) => 'Selmonal\\SimplePayment\\Database\\Factories\\'.class_basename($modelName).'Factory'
        );
    }

    protected function getPackageProviders($app)
    {
        return [
            SimplePaymentServiceProvider::class,
        ];
    }

    public function getEnvironmentSetUp($app)
    {
        config()->set('database.default', 'testing');
        config()->set('simple-payment.user_model', TestUser::class);

        $migration = include __DIR__.'/../database/migrations/2023_02_22_104541_create_payments_table.php';
        $migration->up();

        Schema::create('test_payables', function ($table) {
            $table->id();
            $table->string('description');
            $table->float('amount');
            $table->string('user_id')->nullable();
            $table->timestamps();
        });
    }
}
