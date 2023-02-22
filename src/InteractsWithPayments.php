<?php

namespace Selmonal\LaravelSimplePayment;

use Illuminate\Database\Eloquent\Relations\MorphMany;
use Selmonal\LaravelSimplePayment\Actions\CreatePayment;

trait InteractsWithPayments
{
    public function payments(): MorphMany
    {
        return $this->morphMany(Payment::class, 'payable');
    }

    public function createPayment(string $gateway = 'qpay'): Payment
    {
        return CreatePayment::run($gateway, $this);
    }
}
