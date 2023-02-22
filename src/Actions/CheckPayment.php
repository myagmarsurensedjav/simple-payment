<?php

namespace Selmonal\LaravelSimplePayment\Actions;

use Illuminate\Support\Arr;
use Lorisleiva\Actions\Concerns\AsAction;
use Selmonal\LaravelSimplePayment\Gateways\Qpay\Client;
use Selmonal\LaravelSimplePayment\Payment;
use Selmonal\LaravelSimplePayment\PaymentStatus;

class CheckPayment
{
    use AsAction;

    public function __construct(private Client $qpay)
    {
    }

    public function handle(Payment $payment): void
    {
        $result = $this->qpay->checkPayment($payment->gateway_transaction_id);

        if (! $this->paymentHasPaidOnQpay($result)) {
            $payment->update([
                'status' => PaymentStatus::Failed,
                'error_message' => $message = Arr::get($result, 'message'),
            ]);

            throw new \Exception('Төлбөр төлөлт амжилтгүй. '.$message);
        }

        $payment->update([
            'status' => PaymentStatus::Complete,
        ]);

        $payment->payable->whenPaid($payment);
    }

    private function paymentHasPaidOnQpay(array $result): bool
    {
        return Arr::get($result, 'count') > 0 && Arr::get($result, 'paid_amount') > 0;
    }
}
