<?php

namespace Selmonal\SimplePayment\Gateways\Qpay;

use Illuminate\Support\Arr;
use Selmonal\SimplePayment\CheckedPayment;
use Selmonal\SimplePayment\Enums\PaymentStatus;

class QpayCheckedPayment extends CheckedPayment
{
    private function isPaidOnQpay(): bool
    {
        return Arr::get($this->gatewayResponse, 'count') > 0
            && Arr::get($this->gatewayResponse, 'paid_amount') > 0;
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
