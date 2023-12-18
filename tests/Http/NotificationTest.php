<?php

use MyagmarsurenSedjav\SimplePayment\CheckedPayment;
use MyagmarsurenSedjav\SimplePayment\Drivers\AbstractDriver;
use MyagmarsurenSedjav\SimplePayment\Facades\SimplePayment;
use MyagmarsurenSedjav\SimplePayment\Payment;
use function Pest\Laravel\{get, postJson};

/**
 * @param  Payment  $payment
 * @return void
 */
function mockCheckPayment(Payment $payment): void
{
    SimplePayment::extend($payment->driver, fn() => mockWithPest(AbstractDriver::class)->expect(
        verify: fn($p) => mockWithPest(CheckedPayment::class)->expect()
    ));
}

it('should verify the given payment', function () {
    $payment = Payment::factory()->create();

    mockCheckPayment($payment);

    get(route('simple-payment.notification', $payment))
        ->assertOk()
        ->assertSee(['status' => 'ok']);
});

it('handles invoice paid notification from pocket', function () {
    $payment = Payment::factory()->create([
        'transaction_id' => '123',
        'driver' => 'pocket'
    ]);

    mockCheckPayment($payment);

    $payload = [
        'invoiceId' => $payment->transaction_id,
    ];

    postJson(route('simple-payment.notification.pocket', $payment), $payload)->assertOk()
        ->assertSee(['status' => 'ok']);
});
