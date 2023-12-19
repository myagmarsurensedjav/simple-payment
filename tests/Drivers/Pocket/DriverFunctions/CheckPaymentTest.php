<?php

use MyagmarsurenSedjav\SimplePayment\CheckedPayment;
use MyagmarsurenSedjav\SimplePayment\Drivers\Pocket\PocketClient;
use MyagmarsurenSedjav\SimplePayment\Drivers\Pocket\PocketDriver;
use MyagmarsurenSedjav\SimplePayment\Payment;

it('checks the given payment has paid on pocket', function () {
    $client = mock(PocketClient::class);

    $client->shouldReceive('request')
        ->once()
        ->with('POST', 'v2/invoicing/invoices/invoice-id', [
            'terminalId' => '123321',
            'invoiceId' => 'INVOICE_ID',
        ])
        ->andReturn(['state' => 'paid']);

    $invoiceId = 'INVOICE_ID';
    $payment = Payment::factory()->create([
        'transaction_id' => $invoiceId,
        'driver_data' => [
            'terminal_id' => '123321',
        ],
    ]);

    $checkedPayment = (new PocketDriver('pocket', $client))->check($payment);

    expect($checkedPayment)->toBeInstanceOf(CheckedPayment::class)
        ->and($checkedPayment->driverResponse)->toEqual(['state' => 'paid']);
});
