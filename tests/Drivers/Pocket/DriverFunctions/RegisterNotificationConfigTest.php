<?php

use MyagmarsurenSedjav\SimplePayment\Contracts\RouteConfig;
use MyagmarsurenSedjav\SimplePayment\Drivers\Pocket\PocketClient;
use MyagmarsurenSedjav\SimplePayment\Drivers\Pocket\PocketDriver;

it('registers notification receiving url to pocket', function () {
    $client = mock(PocketClient::class);

    $client->shouldReceive('request')
        ->once()
        ->with('POST', 'pg/config', ['fallBackUrl' => route(RouteConfig::ROUTE_NOTIFICATION_POCKET)])
        ->andReturn(['fallBackUrl' => route(RouteConfig::ROUTE_NOTIFICATION_POCKET)]);

    $driver = new PocketDriver('pocket', $client);

    $driver->registerNotificationConfig();
});

it('should throw an exception if failed to register notification config', function () {
    $client = mock(PocketClient::class);

    $client->shouldReceive('request')
        ->once()
        ->with('POST', 'pg/config', ['fallBackUrl' => route(RouteConfig::ROUTE_NOTIFICATION_POCKET)])
        ->andReturn(['fallBackUrl' => '']);

    $driver = new PocketDriver('pocket', $client);

    $driver->registerNotificationConfig();
})->throws(\Exception::class, 'Failed to register notification config');
