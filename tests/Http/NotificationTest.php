<?php

use Selmonal\SimplePayment\CheckedPayment;
use Selmonal\SimplePayment\Facades\SimplePayment;
use Selmonal\SimplePayment\Gateways\AbstractGateway;
use Selmonal\SimplePayment\Payment;

it('should verify the given payment', function () {
    $payment = Payment::factory()->create();

    SimplePayment::extend($payment->gateway, fn () => mock(AbstractGateway::class)->expect(
        verify: fn ($p) => mock(CheckedPayment::class)->expect()
    ));

    $this->get(route('simple-payment.notification', $payment))
        ->assertOk()
        ->assertSee(['status' => 'ok']);
});
