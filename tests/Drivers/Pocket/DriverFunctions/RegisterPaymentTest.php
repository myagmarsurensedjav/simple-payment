<?php

use MyagmarsurenSedjav\SimplePayment\Drivers\Pocket\PocketClient;
use MyagmarsurenSedjav\SimplePayment\Drivers\Pocket\PocketDriver;
use MyagmarsurenSedjav\SimplePayment\Drivers\Pocket\PocketPendingPayment;
use MyagmarsurenSedjav\SimplePayment\Payment;

beforeEach(function () {
    $this->client = mock(PocketClient::class);
    $this->client->shouldReceive('getDefaultTerminalId')->andReturn('123321');
    $this->driver = new PocketDriver('pocket', $this->client);
});

function validParams(Payment $payment, array $overrides = []): array
{
    return [
        'terminalId' => '123321',
        'amount' => $payment->amount,
        'info' => $payment->description,
        'orderNumber' => substr(md5(uuid_parse($payment->id)), 0, 24),
        'invoiceType' => 'PURCHASE',
        'channel' => 'ecommerce',
        ...$overrides,
    ];
}

it('registers a payment to pocket', function () {
    $payment = Payment::factory()->create();

    $this->client->shouldReceive('request')
        ->once()
        ->with('post', 'v2/invoicing/generate-invoice', validParams($payment))
        ->andReturn(['id' => 'INVOICE_ID']);

    $pendingPayment = $this->driver->register($payment, []);

    expect($pendingPayment)->toBeInstanceOf(PocketPendingPayment::class)
        ->and($pendingPayment->driverResponse)->toEqual([
            'id' => 'INVOICE_ID',
            '_data' => [
                'terminal_id' => '123321',
                'invoice_type' => 'PURCHASE',
                'channel' => 'ecommerce',
            ],
        ]);
});

it('throws an exception if the payment amount is less than 500', function () {
    $payment = Payment::factory()->create(['amount' => 499]);

    $this->client->shouldNotReceive('request');

    $this->driver->register($payment, []);
})->throws(InvalidArgumentException::class);

it('may accepts terminal_id as option', function () {
    $payment = Payment::factory()->create();

    $this->client->shouldReceive('request')
        ->once()
        ->with('post', 'v2/invoicing/generate-invoice', validParams($payment, [
            'terminalId' => 'CUSTOM_TERMINAL_ID',
        ]))
        ->andReturn(['id' => 'INVOICE_ID']);

    $pendingPayment = $this->driver->register($payment, [
        'terminal_id' => 'CUSTOM_TERMINAL_ID',
    ]);

    expect($pendingPayment)->toBeInstanceOf(PocketPendingPayment::class)
        ->and($pendingPayment->driverResponse)->toEqual([
            'id' => 'INVOICE_ID',
            '_data' => [
                'terminal_id' => 'CUSTOM_TERMINAL_ID',
                'invoice_type' => 'PURCHASE',
                'channel' => 'ecommerce',
            ],
        ]);
});

it('may accepts invoice_type as option', function (string $invoiceType) {
    $payment = Payment::factory()->create();

    $this->client->shouldReceive('request')
        ->once()
        ->with('post', 'v2/invoicing/generate-invoice', validParams($payment, [
            'invoiceType' => $invoiceType,
        ]))
        ->andReturn(['id' => 'INVOICE_ID']);

    $pendingPayment = $this->driver->register($payment, [
        'invoice_type' => $invoiceType,
    ]);

    expect($pendingPayment)->toBeInstanceOf(PocketPendingPayment::class)
        ->and($pendingPayment->driverResponse)->toEqual([
            'id' => 'INVOICE_ID',
            '_data' => [
                'terminal_id' => '123321',
                'invoice_type' => $invoiceType,
                'channel' => 'ecommerce',
            ],
        ]);
})->with([
    ['PURCHASE'],
    ['ZERO'],
]);

it('should throw an exception if the invoice_type is not valid', function (string $invoiceType) {
    $payment = Payment::factory()->create();

    $this->client->shouldNotReceive('request');

    $this->driver->register($payment, ['invoice_type' => $invoiceType]);
})->with([
    ['INVALID'],
    [''],
])->throws(InvalidArgumentException::class);

it('may accepts channel as option', function (string $channel) {
    $payment = Payment::factory()->create();

    $this->client->shouldReceive('request')
        ->once()
        ->with('post', 'v2/invoicing/generate-invoice', validParams($payment, [
            'channel' => $channel,
        ]))
        ->andReturn(['id' => 'INVOICE_ID']);

    $pendingPayment = $this->driver->register($payment, [
        'channel' => $channel,
    ]);

    expect($pendingPayment)->toBeInstanceOf(PocketPendingPayment::class)
        ->and($pendingPayment->driverResponse)->toEqual([
            'id' => 'INVOICE_ID',
            '_data' => [
                'terminal_id' => '123321',
                'invoice_type' => 'PURCHASE',
                'channel' => $channel,
            ],
        ]);
})->with([
    ['ecommerce'],
    ['pos'],
]);

it('should throw an exception if the channel is not valid', function (string $channel) {
    $payment = Payment::factory()->create();

    $this->client->shouldNotReceive('request');

    $this->driver->register($payment, ['channel' => $channel]);
})->with([
    ['INVALID'],
    [''],
])->throws(InvalidArgumentException::class);
