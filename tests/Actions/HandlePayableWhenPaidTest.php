<?php

use MyagmarsurenSedjav\SimplePayment\Actions\HandlePayableWhenPaid;
use MyagmarsurenSedjav\SimplePayment\Contracts\Payable;
use MyagmarsurenSedjav\SimplePayment\Enums\PaymentStatus;
use MyagmarsurenSedjav\SimplePayment\Exceptions\InvalidPayable;
use MyagmarsurenSedjav\SimplePayment\Exceptions\InvalidPayment;
use MyagmarsurenSedjav\SimplePayment\Payment;

function handle(Payment $payment): void
{
    app(HandlePayableWhenPaid::class)($payment);
}

test('it should throw an exception when the payable is not set', function () {
    $payment = new Payment(['status' => PaymentStatus::Paid]);
    expect($payment)->payable->toBeNull();

    handle($payment);
})->expectException(InvalidPayable::class);

test('it should throw an exception if the status of the given payment is not paid', function () {
    $payment = new Payment(['status' => PaymentStatus::Failed]);
    handle($payment);
})->expectException(InvalidPayment::class);

test('it should call the whenPaid method of the payable', function () {
    $payment = new Payment(['status' => PaymentStatus::Paid]);

    $payableMock = mock(Payable::class);
    $payableMock->shouldReceive('whenPaid')->once()->with($payment);
    $payment->payable = $payableMock;

    handle($payment);
});
