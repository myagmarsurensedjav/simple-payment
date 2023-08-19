<?php

namespace MyagmarsurenSedjav\SimplePayment\Drivers\Golomt;

use MyagmarsurenSedjav\SimplePayment\CheckedPayment;
use MyagmarsurenSedjav\SimplePayment\Drivers\AbstractDriver;
use MyagmarsurenSedjav\SimplePayment\Payment;
use MyagmarsurenSedjav\SimplePayment\PendingPayment;

class GolomtDriver extends AbstractDriver
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
