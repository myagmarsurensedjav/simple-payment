<?php

namespace MyagmarsurenSedjav\SimplePayment\Actions;

use MyagmarsurenSedjav\SimplePayment\CheckedPayment;
use MyagmarsurenSedjav\SimplePayment\Events\PaymentWasMade;
use MyagmarsurenSedjav\SimplePayment\Gateways\AbstractGateway;
use MyagmarsurenSedjav\SimplePayment\Payment;

class VerifyPayment
{
    public function __invoke(AbstractGateway $gateway, Payment $payment): CheckedPayment
    {
        $result = $gateway->check($payment);

        $attributesShouldBeUpdated = [
            'status' => $result->status(),
            'error_message' => $result->errorMessage(),
            'verified_at' => now(),
            'verifies_count' => $payment->verifies_count + 1,
        ];

        if ($result->successful()) {
            $attributesShouldBeUpdated['paid_at'] = now();
        }

        $payment->update($attributesShouldBeUpdated);

        if ($result->successful()) {
            $payment->payable->whenPaid($payment);
            event(new PaymentWasMade($payment));
        }

        return $result;
    }
}
