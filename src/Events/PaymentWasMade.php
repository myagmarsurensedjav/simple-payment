<?php

namespace Selmonal\SimplePayment\Events;

use Selmonal\SimplePayment\Payment;

class PaymentWasMade
{
    public function __construct(public Payment $payment)
    {
    }
}
