<?php

namespace MyagmarsurenSedjav\SimplePayment\Drivers\Qpay;

use Illuminate\Support\Arr;
use MyagmarsurenSedjav\SimplePayment\CheckedPayment;
use MyagmarsurenSedjav\SimplePayment\Enums\PaymentStatus;

class QpayCheckedPayment extends CheckedPayment
{
    private function isPaidOnQpay(): bool
    {
        return Arr::get($this->driverResponse, 'count') > 0
            && Arr::get($this->driverResponse, 'paid_amount') > 0;
    }

    public function status(): PaymentStatus
    {
        if ($this->isPaidOnQpay()) {
            return PaymentStatus::Paid;
        }

        return PaymentStatus::Failed;
    }

    public function errorMessage(): string|null
    {
        if ($this->isPaidOnQpay()) {
            return null;
        }

        return 'Payment not found on Qpay';
    }
}
