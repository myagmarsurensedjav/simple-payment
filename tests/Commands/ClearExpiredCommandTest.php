<?php

use Selmonal\SimplePayment\Enums\PaymentStatus;
use Selmonal\SimplePayment\Payment;

it('can clear expired payments', function () {
    $payment = Payment::factory()->create([
        'status' => PaymentStatus::Pending,
        'expires_at' => now()->subDay(),
        'created_at' => now()->subDays(8),
    ]);

    $this->artisan('simple-payment:clear-expired')
        ->expectsOutput('1 payment(s) cleared.')
        ->assertExitCode(0);

    $this->assertDatabaseMissing('payments', [
        'id' => $payment->id,
    ]);
});
