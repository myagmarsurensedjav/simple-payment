<?php

namespace MyagmarsurenSedjav\SimplePayment\Drivers\Qpay;

use MyagmarsurenSedjav\SimplePayment\CheckedPayment;
use MyagmarsurenSedjav\SimplePayment\Drivers\AbstractDriver;
use MyagmarsurenSedjav\SimplePayment\Payment;
use MyagmarsurenSedjav\SimplePayment\PendingPayment;

class QpayDriver extends AbstractDriver
{
    private QpayClient $client;

    public function __construct(string $name, array $config)
    {
        parent::__construct($name, $config);

        $this->client = new QpayClient($config);
    }

    public function register(Payment $payment, array $options): PendingPayment
    {
        $response = $this->client->createSimpleInvoice(
            invoiceId: $payment->id,
            amount: $payment->amount,
            description: $payment->description,
            userId: $payment->user_id,
            callbackUrl: route('simple-payment.notification', $payment->id)
        );

        return new QpayPendingPayment($payment, $response);
    }

    public function check(Payment $payment): CheckedPayment
    {
        $checkedPayment = $this->client->checkPayment($payment->transaction_id);

        return new QpayCheckedPayment($payment, $checkedPayment);
    }
}
