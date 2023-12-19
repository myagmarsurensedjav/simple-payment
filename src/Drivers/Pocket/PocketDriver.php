<?php

namespace MyagmarsurenSedjav\SimplePayment\Drivers\Pocket;

use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Str;
use MyagmarsurenSedjav\SimplePayment\CheckedPayment;
use MyagmarsurenSedjav\SimplePayment\Drivers\AbstractDriver;
use MyagmarsurenSedjav\SimplePayment\Payment;
use MyagmarsurenSedjav\SimplePayment\PendingPayment;

class PocketDriver extends AbstractDriver
{
    public function __construct(string $name, private readonly PocketClient $client)
    {
        parent::__construct($name, $client->getConfig());
    }

    public function register(Payment $payment, array $options): PendingPayment
    {
        $result = $this->client->request('post', 'v2/invoicing/generate-invoice', [
            'terminalId' => (int) $this->config['terminal_id'],
            'amount' => $payment->amount,
            'info' => $payment->description,
            'orderNumber' => Str::limit($payment->id, 24, ''),
            'invoiceType' => 'PURCHASE',
            'channel' => 'ecommerce',
        ]);

        return new PocketPendingPayment($payment, $result);
    }

    public function check(Payment $payment): CheckedPayment
    {
        $response = $this->client->request('POST', 'v2/invoicing/invoices/invoice-id', [
            'terminalId' => (int) $this->config['terminal_id'],
            'invoiceId' => $payment->transaction_id,
        ]);

        return new PocketCheckedPayment($payment, $response);
    }

    public function getFirstBranch(): array
    {
        return $this->getBranches()->first();
    }

    public function getBranches(): LengthAwarePaginator
    {
        $result = $this->client->request('GET', 'payment-gateway/branch/list');

        return new LengthAwarePaginator(
            $result['content'],
            $result['totalElements'],
            $result['size'],
            $result['number']
        );
    }

    public function getFirstTerminalFromBranch($branchId): array
    {
        return $this->getTerminals($branchId)->first();
    }

    public function getTerminals(string $branchId): LengthAwarePaginator
    {
        $result = $this->client->request('GET', 'payment-gateway/terminal/list/'.$branchId);

        return new LengthAwarePaginator(
            $result['content'],
            $result['totalElements'],
            $result['size'],
            $result['number']
        );
    }

    public function registerNotificationConfig(): void
    {
        $url = url('/simple-payment/notification/pocket');

        $result = $this->client->request('POST', 'pg/config', ['fallBackUrl' => $url]);

        if ($result['fallBackUrl'] !== $url) {
            throw new \Exception('Failed to register notification config');
        }
    }
}
