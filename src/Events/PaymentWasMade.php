<?php

namespace MyagmarsurenSedjav\SimplePayment\Events;

use MyagmarsurenSedjav\SimplePayment\Payment;

class PaymentWasMade
{
    public function __construct(public Payment $payment) {}
}
