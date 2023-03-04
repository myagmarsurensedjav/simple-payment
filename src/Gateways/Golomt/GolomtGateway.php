<?php

namespace MyagmarsurenSedjav\SimplePayment\Gateways\Golomt;

use MyagmarsurenSedjav\SimplePayment\CheckedPayment;
use MyagmarsurenSedjav\SimplePayment\Gateways\AbstractGateway;
use MyagmarsurenSedjav\SimplePayment\Payment;
use MyagmarsurenSedjav\SimplePayment\PendingPayment;

class GolomtGateway extends AbstractGateway
{
    private GolomtClient $client;

    public function __construct(string $name, array $config, private readonly bool $isSocialPay = false)
    {
        parent::__construct($name, $config);

        $this->client = new GolomtClient($config);
    }

    public function register(Payment $payment, array $options): PendingPayment
    {
        $data = $this->client->createInvoice([
            'amount' => (string) $payment->amount,
            'callback' => route('simple-payment.return', $payment->id),
            'getToken' => 'N',
            'returnType' => 'POST',
            'transactionId' => $payment->id,
        ]);

        $pendingPayment = new GolomtPendingPayment($payment, $data);

        if ($this->isSocialPay) {
            $pendingPayment->asSocialPay();
        }

        return $pendingPayment;
    }

    public function check(Payment $payment): CheckedPayment
    {
        $data = $this->client->checkPayment($payment->id);

        return new GolomtCheckedPayment($payment, $data);
    }
}
