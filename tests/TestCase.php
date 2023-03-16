<?php

namespace MyagmarsurenSedjav\SimplePayment\Tests;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Schema;
use MyagmarsurenSedjav\SimplePayment\SimplePaymentServiceProvider;
use MyagmarsurenSedjav\SimplePayment\Tests\Support\TestUser;
use Orchestra\Testbench\TestCase as Orchestra;

class TestCase extends Orchestra
{
    protected function setUp(): void
    {
        parent::setUp();

        Factory::guessFactoryNamesUsing(
            fn (string $modelName) => 'MyagmarsurenSedjav\\SimplePayment\\Database\\Factories\\'.class_basename($modelName).'Factory'
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

        $migration = include __DIR__.'/../database/migrations/create_payments_table.php.stub';
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
