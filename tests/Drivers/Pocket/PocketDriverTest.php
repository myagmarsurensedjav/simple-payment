<?php

use MyagmarsurenSedjav\SimplePayment\CheckedPayment;
use MyagmarsurenSedjav\SimplePayment\Contracts\RouteConfig;
use MyagmarsurenSedjav\SimplePayment\Drivers\Pocket\PocketCheckedPayment;
use MyagmarsurenSedjav\SimplePayment\Drivers\Pocket\PocketClient;
use MyagmarsurenSedjav\SimplePayment\Drivers\Pocket\PocketDriver;
use MyagmarsurenSedjav\SimplePayment\Payment;

it('registers notification receiving url to pocket', function () {
    $client = mock(PocketClient::class);
    $client->shouldReceive('getConfig')->andReturn(['terminal_id' => '123']);

    $client->shouldReceive('request')
        ->once()
        ->with('POST', 'pg/config', ['fallBackUrl' => route(RouteConfig::ROUTE_NOTIFICATION_POCKET)])
        ->andReturn(['fallBackUrl' => route(RouteConfig::ROUTE_NOTIFICATION_POCKET)]);

    $driver = new PocketDriver('pocket', $client);

    $driver->registerNotificationConfig();
});

it('should throw an exception if failed to register notification config', function () {
    $client = mock(PocketClient::class);
    $client->shouldReceive('getConfig')->andReturn(['terminal_id' => '123']);

    $client->shouldReceive('request')
        ->once()
        ->with('POST', 'pg/config', ['fallBackUrl' => route(RouteConfig::ROUTE_NOTIFICATION_POCKET)])
        ->andReturn(['fallBackUrl' => '']);

    $driver = new PocketDriver('pocket', $client);

    $driver->registerNotificationConfig();
})->throws(\Exception::class, 'Failed to register notification config');

it('checks the given payment has paid on pocket', function () {
    $client = mock(PocketClient::class);
    $client->shouldReceive('getConfig')->andReturn(['terminal_id' => '123321']);

    $client->shouldReceive('request')
        ->once()
        ->with('POST', 'v2/invoicing/invoices/invoice-id', [
            'terminalId' => '123321',
            'invoiceId' => 'INVOICE_ID'
        ])
        ->andReturn(['state' => 'paid']);

    $driver = new PocketDriver('pocket', $client);

    $invoiceId = 'INVOICE_ID';
    $payment = Payment::factory()->create(['transaction_id' => $invoiceId]);

    $checkedPayment = $driver->check($payment);

    expect($checkedPayment)->toBeInstanceOf(CheckedPayment::class)
        ->and($checkedPayment->driverResponse)->toEqual(['state' => 'paid']);
});
