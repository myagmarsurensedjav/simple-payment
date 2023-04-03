<?php

namespace MyagmarsurenSedjav\SimplePayment\Actions;

use MyagmarsurenSedjav\SimplePayment\Enums\PaymentStatus;
use MyagmarsurenSedjav\SimplePayment\Exceptions\InvalidPayable;
use MyagmarsurenSedjav\SimplePayment\Exceptions\InvalidPayment;
use MyagmarsurenSedjav\SimplePayment\Payment;

class HandlePayableWhenPaid
{
    /**
     * @throws InvalidPayment
     * @throws InvalidPayable
     */
    public function __invoke(Payment $payment): void
    {
        $this->guardAgainstIncompletePayment($payment);
        $this->guardAgainstInvalidPayable($payment);

        $payment->payable->whenPaid($payment);
    }

    private function guardAgainstIncompletePayment(Payment $payment): void
    {
        if ($payment->status !== PaymentStatus::Paid) {
            throw InvalidPayment::notPaid();
        }
    }

    private function guardAgainstInvalidPayable(Payment $payment): void
    {
        if (! $payment->payable) {
            throw InvalidPayable::notSet();
        }
    }
}
