<?php

namespace MyagmarsurenSedjav\SimplePayment\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use MyagmarsurenSedjav\SimplePayment\Payment;

class PaymentWasRefunded
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(public Payment $payment)
    {
        //
    }
}
