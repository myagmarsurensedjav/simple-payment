<?php

namespace MyagmarsurenSedjav\SimplePayment\Contracts;

use MyagmarsurenSedjav\SimplePayment\Payment;

interface Payable
{
    public function getPaymentAmount(): float;

    public function getPaymentDescription(): string;

    public function whenPaid(Payment $payment): void;

    public function getUserId(): int|string|null;

    public function getMorphClass();

    public function getKey();
}
