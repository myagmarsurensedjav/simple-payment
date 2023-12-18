<?php

use MyagmarsurenSedjav\SimplePayment\Drivers\Pocket\PocketCheckedPayment;
use MyagmarsurenSedjav\SimplePayment\Enums\PaymentStatus;
use MyagmarsurenSedjav\SimplePayment\Payment;

it('converts invoice state to payment status', function ($state, PaymentStatus $status) {
    $payment = Payment::factory()->make();
    $checkedPayment = new PocketCheckedPayment($payment, ['state' => $state]);

    expect($checkedPayment->status())->toEqual($status);
})->with([
    ['paid', PaymentStatus::Paid],
    ['pending', PaymentStatus::Pending],
    ['unsuccess', PaymentStatus::Failed],
]);

it('should return invoice description as error message if state is unsuccess', function () {
    $payment = Payment::factory()->make();
    $checkedPayment = new PocketCheckedPayment($payment, [
        'state' => 'unsuccess',
        'description' => 'Invoice description',
    ]);

    expect($checkedPayment->errorMessage())->toEqual('Invoice description');
});
