<?php

namespace MyagmarsurenSedjav\SimplePayment\Actions;

use MyagmarsurenSedjav\SimplePayment\CheckedPayment;
use MyagmarsurenSedjav\SimplePayment\Drivers\AbstractDriver;
use MyagmarsurenSedjav\SimplePayment\Events\PaymentWasMade;
use MyagmarsurenSedjav\SimplePayment\Payment;

class VerifyPayment
{
    public function __invoke(AbstractDriver $driver, Payment $payment): CheckedPayment
    {
        $result = $driver->check($payment);

        $attributesShouldBeUpdated = [
            'status' => $result->status(),
            'error_message' => $result->errorMessage(),
            'verified_at' => now(),
            'verifies_count' => $payment->verifies_count + 1,
        ];

        if ($isSuccessful = $result->successful()) {
            $attributesShouldBeUpdated['paid_at'] = now();
        }

        $payment->update($attributesShouldBeUpdated);

        if ($isSuccessful) {
            app(HandlePayableWhenPaid::class)($payment);
            event(new PaymentWasMade($payment));
        }

        return $result;
    }
}
