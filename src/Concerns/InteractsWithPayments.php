<?php

namespace MyagmarsurenSedjav\SimplePayment\Concerns;

use Illuminate\Database\Eloquent\Relations\MorphMany;
use MyagmarsurenSedjav\SimplePayment\Actions\CreatePayment;
use MyagmarsurenSedjav\SimplePayment\Payment;

trait InteractsWithPayments
{
    public function payments(): MorphMany
    {
        return $this->morphMany(Payment::class, 'payable');
    }

    public function createPayment(string $gateway): Payment
    {
        return app(CreatePayment::class)($gateway, $this);
    }
}
