<?php

namespace Selmonal\LaravelSimplePayment\Actions;

use Illuminate\Support\Str;
use Lorisleiva\Actions\Concerns\AsAction;
use Selmonal\LaravelSimplePayment\Contracts\Payable;
use Selmonal\LaravelSimplePayment\Exceptions\NothingToPay;
use Selmonal\LaravelSimplePayment\Gateways\Qpay\Client;
use Selmonal\LaravelSimplePayment\Payment;

class CreatePayment
{
    use AsAction;

    public function __construct(private Client $qpay)
    {
    }

    public function handle($gateway, Payable $payable): Payment
    {
        if ($payable->getPaymentAmount() <= 0) {
            throw new NothingToPay('Payment amount cannot be zero.');
        }

        $qpay = $this->qpay->createSimpleInvoice(
            $paymentId = (string) Str::uuid(),
            $payable->getPaymentAmount(),
            $payable->getPaymentDescription(),
            $payable->getUserId(),
            route('qpay.webhook', $paymentId)
        );

        /** @var Payment $payment */
        $payment = $payable->payments()->create([
            'id' => $paymentId,
            'user_id' => $payable->getUserId(),
            'amount' => $payable->getPaymentAmount(),
            'gateway_transaction_id' => $qpay['invoice_id'],
            'description' => $payable->getPaymentDescription(),
        ]);

        $payment->qpay = $qpay;

        return $payment;
    }
}
