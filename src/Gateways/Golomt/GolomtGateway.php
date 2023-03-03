<?php

namespace Selmonal\SimplePayment\Gateways\Golomt;

use Selmonal\SimplePayment\CheckedPayment;
use Selmonal\SimplePayment\Gateways\AbstractGateway;
use Selmonal\SimplePayment\Payment;
use Selmonal\SimplePayment\PendingPayment;

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

        $pendingPayment = GolomtPendingPayment::new($payment, $data);

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
