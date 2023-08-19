<?php

use MyagmarsurenSedjav\SimplePayment\CheckedPayment;
use MyagmarsurenSedjav\SimplePayment\Drivers\AbstractDriver;
use MyagmarsurenSedjav\SimplePayment\Facades\SimplePayment;
use MyagmarsurenSedjav\SimplePayment\Payment;

it('should verify the given payment', function () {
    $payment = Payment::factory()->create();

    SimplePayment::extend($payment->driver, fn () => mockWithPest(AbstractDriver::class)->expect(
        verify: fn ($p) => mockWithPest(CheckedPayment::class)->expect()
    ));

    $this->get(route('simple-payment.notification', $payment))
        ->assertOk()
        ->assertSee(['status' => 'ok']);
});
