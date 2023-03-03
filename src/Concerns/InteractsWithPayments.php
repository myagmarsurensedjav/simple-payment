<?php

namespace Selmonal\SimplePayment\Concerns;

use Illuminate\Database\Eloquent\Relations\MorphMany;
use Selmonal\SimplePayment\Actions\CreatePayment;
use Selmonal\SimplePayment\Payment;

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
