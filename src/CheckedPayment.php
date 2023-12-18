<?php

namespace MyagmarsurenSedjav\SimplePayment;

use Illuminate\Contracts\Support\Arrayable;
use MyagmarsurenSedjav\SimplePayment\Enums\PaymentStatus;

abstract class CheckedPayment implements Arrayable
{
    public function __construct(public Payment $payment, public array $driverResponse = [])
    {
    }

    abstract public function status(): PaymentStatus;

    abstract public function errorMessage(): ?string;

    public function successful(): bool
    {
        return $this->status() === PaymentStatus::Paid;
    }

    public function toArray(): array
    {
        return [
            'status' => $this->status(),
            'error_message' => $this->errorMessage(),
            'payment' => $this->payment->toArray(),
            'driver_response' => $this->driverResponse,
        ];
    }
}
