<?php

namespace Selmonal\SimplePayment\Contracts;

use Selmonal\SimplePayment\Payment;

interface Payable
{
    public function getPaymentAmount(): float;

    public function getPaymentDescription(): string;

    public function whenPaid(Payment $payment): void;

    public function getUserId(): int|string|null;
}
