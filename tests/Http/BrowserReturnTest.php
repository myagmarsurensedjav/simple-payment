<?php

use MyagmarsurenSedjav\SimplePayment\CheckedPayment;
use MyagmarsurenSedjav\SimplePayment\Drivers\AbstractDriver;
use MyagmarsurenSedjav\SimplePayment\Enums\PaymentStatus;
use MyagmarsurenSedjav\SimplePayment\Facades\SimplePayment;
use MyagmarsurenSedjav\SimplePayment\Payment;

it('should verify and render the result', function () {
    $payment = Payment::factory()->create();

    SimplePayment::extend($payment->driver, fn () => mockWithPest(AbstractDriver::class)->expect(
        verify: fn ($p) => new class($p) extends CheckedPayment
        {
            public function status(): PaymentStatus
            {
                return PaymentStatus::Paid;
            }

            public function errorMessage(): ?string
            {
                return '';
            }
        }
    ));

    $this->get(route('simple-payment.return', $payment))
        ->assertViewIs('simple-payment::return');
});

it('should return the result for the global filter of the simple manager', function () {
    $payment = Payment::factory()->create();

    SimplePayment::extend($payment->driver, fn () => mockWithPest(AbstractDriver::class)->expect(
        verify: fn ($p) => new class($p) extends CheckedPayment
        {
            public function status(): PaymentStatus
            {
                return PaymentStatus::Failed;
            }

            public function errorMessage(): ?string
            {
                return 'Error';
            }
        }
    ));

    SimplePayment::onBrowserReturn(function () {
        return response()->json(['status' => 'ok']);
    });

    $this->get(route('simple-payment.return', $payment))
        ->assertOk()
        ->assertJson(['status' => 'ok']);
});
