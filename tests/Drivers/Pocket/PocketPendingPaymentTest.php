<?php

use MyagmarsurenSedjav\SimplePayment\Contracts\Results\WithDriverData;
use MyagmarsurenSedjav\SimplePayment\Drivers\Pocket\PocketPendingPayment;
use MyagmarsurenSedjav\SimplePayment\Payment;

it('normalizes driver response pending payment', function () {
    $payment = Payment::factory()->make();

    $pendingPayment = new PocketPendingPayment($payment, [
        'id' => 178387,
        'qr' => 'H4XXXXXXXXXX',
        'orderNumber' => 'ORDER_NUMBER',
        'deeplink' => 'pckt://payment?qr=H4XXXXXXXXXX',
        '_data' => [
            'terminal_id' => '123321',
            'invoice_type' => 'PURCHASE',
            'channel' => 'ecommerce',
        ],
    ]);

    expect($pendingPayment)
        ->toBeInstanceOf(WithDriverData::class)
        ->and($pendingPayment->getDriverData())->toEqual([
            'terminal_id' => '123321',
            'invoice_type' => 'PURCHASE',
            'channel' => 'ecommerce',
        ])
        ->and($pendingPayment->getUrls())->toEqual([
            [
                'icon' => 'https://pocket.mn/_nuxt/img/pocket-logo-black.1295461.png',
                'label' => 'Pocket',
                'url' => 'pckt://payment?qr=H4XXXXXXXXXX',
            ],
        ])
        ->and($pendingPayment->getTransactionId())->toEqual('178387')
        ->and($pendingPayment->getBase64QrImage())->toBeString();
});
