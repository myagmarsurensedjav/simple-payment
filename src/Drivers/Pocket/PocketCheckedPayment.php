<?php

namespace MyagmarsurenSedjav\SimplePayment\Drivers\Pocket;

use MyagmarsurenSedjav\SimplePayment\CheckedPayment;
use MyagmarsurenSedjav\SimplePayment\Enums\PaymentStatus;

class PocketCheckedPayment extends CheckedPayment
{
    public function status(): PaymentStatus
    {
        return match ($this->driverResponse['state']) {
            'pending' => PaymentStatus::Pending,
            'paid' => PaymentStatus::Paid,
            'unsuccess' => PaymentStatus::Failed,
            default => PaymentStatus::Pending,
        };
    }

    public function errorMessage(): ?string
    {
        return match ($this->driverResponse['state']) {
            'unsuccess' => $this->driverResponse['description'],
            default => null,
        };
    }
}
