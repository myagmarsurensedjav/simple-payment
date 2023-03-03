<?php

namespace Selmonal\SimplePayment\Gateways;

use Selmonal\SimplePayment\Actions\CreatePayment;
use Selmonal\SimplePayment\Actions\VerifyPayment;
use Selmonal\SimplePayment\CheckedPayment;
use Selmonal\SimplePayment\Contracts\Payable;
use Selmonal\SimplePayment\Payment;
use Selmonal\SimplePayment\PendingPayment;

abstract class AbstractGateway
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
