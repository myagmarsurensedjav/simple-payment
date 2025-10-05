<?php

namespace MyagmarsurenSedjav\SimplePayment\Actions;

use MyagmarsurenSedjav\SimplePayment\Enums\PaymentStatus;
use MyagmarsurenSedjav\SimplePayment\Events\PaymentWasRefunded;
use MyagmarsurenSedjav\SimplePayment\Payment;

class RefundPayment
{
    public function __invoke(Payment $payment, ?string $reason = null): Payment
    {
        // Only allow refunding paid payments
        if ($payment->status !== PaymentStatus::Paid) {
            throw new \InvalidArgumentException('Only paid payments can be refunded');
        }

        $attributesToUpdate = [
            'status' => PaymentStatus::Refunded,
            'refunded_at' => now(),
        ];

        if ($reason) {
            $attributesToUpdate['refund_reason'] = $reason;
        }

        $payment->update($attributesToUpdate);

        // Fire refund event for any additional processing
        event(new PaymentWasRefunded($payment));

        return $payment;
    }
}
