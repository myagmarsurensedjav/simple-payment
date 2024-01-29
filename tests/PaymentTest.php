<?php

use MyagmarsurenSedjav\SimplePayment\Enums\PaymentStatus;
use MyagmarsurenSedjav\SimplePayment\Payment;

it('is expired when expires_at is in the past', function () {
    Payment::factory()->create([
        'status' => PaymentStatus::Pending,
        'expires_at' => now()->subDay(),
    ]);

    expect(Payment::expired()->count())->toEqual(1);
});

it('is not expired when expires_at is in the future', function () {
    Payment::factory()->create([
        'status' => PaymentStatus::Pending,
        'expires_at' => now()->addDay(),
    ]);

    expect(Payment::expired()->count())->toEqual(0);
});

it('is not expired when status is not pending', function () {
    Payment::factory()->create([
        'status' => PaymentStatus::Paid,
        'expires_at' => now()->subDay(),
    ]);

    expect(Payment::expired()->count())->toEqual(0);
});
