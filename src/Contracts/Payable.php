<?php

namespace Selmonal\LaravelSimplePayment\Contracts;

use Illuminate\Database\Eloquent\Relations\MorphMany;
use Selmonal\LaravelSimplePayment\Payment;

interface Payable
{
    public function createPayment(): Payment;

    public function payments(): MorphMany;

    public function getPaymentAmount(): float;

    public function getPaymentDescription(): string;

    public function whenPaid(Payment $payment): void;

    public function getUserId(): string;
}
