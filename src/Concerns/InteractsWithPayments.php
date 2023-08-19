<?php

namespace MyagmarsurenSedjav\SimplePayment\Concerns;

use Illuminate\Database\Eloquent\Relations\MorphMany;
use MyagmarsurenSedjav\SimplePayment\Actions\CreatePayment;
use MyagmarsurenSedjav\SimplePayment\Facades\SimplePayment;
use MyagmarsurenSedjav\SimplePayment\Payment;

trait InteractsWithPayments
{
    public function payments(): MorphMany
    {
        return $this->morphMany(SimplePayment::paymentModel(), 'payable');
    }

    public function createPayment(string $driver): Payment
    {
        return app(CreatePayment::class)($driver, $this);
    }
}
