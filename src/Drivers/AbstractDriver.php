<?php

namespace MyagmarsurenSedjav\SimplePayment\Drivers;

use MyagmarsurenSedjav\SimplePayment\Actions\CreatePayment;
use MyagmarsurenSedjav\SimplePayment\Actions\VerifyPayment;
use MyagmarsurenSedjav\SimplePayment\CheckedPayment;
use MyagmarsurenSedjav\SimplePayment\Contracts\Payable;
use MyagmarsurenSedjav\SimplePayment\Payment;
use MyagmarsurenSedjav\SimplePayment\PendingPayment;

abstract class AbstractDriver
{
    public function __construct(public readonly string $name, protected readonly array $config)
    {
    }

    abstract public function register(Payment $payment, array $options): PendingPayment;

    abstract public function check(Payment $payment): CheckedPayment;

    public function create(Payable $payable, array $options = []): PendingPayment
    {
        return app(CreatePayment::class)($this, $payable, $options);
    }

    public function verify(Payment $payment): CheckedPayment
    {
        return app(VerifyPayment::class)($this, $payment);
    }

    public function name(): string
    {
        return $this->name;
    }
}
