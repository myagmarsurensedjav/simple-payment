<?php

namespace MyagmarsurenSedjav\SimplePayment\Drivers\Pocket;

use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Arr;
use InvalidArgumentException;
use MyagmarsurenSedjav\SimplePayment\CheckedPayment;
use MyagmarsurenSedjav\SimplePayment\Contracts\RouteConfig;
use MyagmarsurenSedjav\SimplePayment\Drivers\AbstractDriver;
use MyagmarsurenSedjav\SimplePayment\Payment;
use MyagmarsurenSedjav\SimplePayment\PendingPayment;

class PocketDriver extends AbstractDriver
{
    public function __construct(string $name, private readonly PocketClient $client)
    {
        parent::__construct($name, []);
    }

    public function register(Payment $payment, array $options): PendingPayment
    {
        $terminalId = Arr::get($options, 'terminal_id', $this->client->getDefaultTerminalId());
        $invoiceType = Arr::get($options, 'invoice_type', 'PURCHASE');
        $channel = Arr::get($options, 'channel', 'ecommerce');

        if ($payment->amount < 500) {
            throw new InvalidArgumentException('Payment amount must be greater than 500');
        }

        if (! in_array($invoiceType, ['PURCHASE', 'ZERO'])) {
            throw new InvalidArgumentException('Invalid invoice type. Must be PURCHASE or ZERO');
        }

        if (! in_array($channel, ['ecommerce', 'pos'])) {
            throw new InvalidArgumentException('Invalid channel. Must be ecommerce or pos');
        }

        $result = $this->client->request('post', 'v2/invoicing/generate-invoice', [
            'terminalId' => $terminalId,
            'amount' => $payment->amount,
            'info' => $payment->description,
            'orderNumber' => substr(md5(uuid_parse($payment->id)), 0, 24),
            'invoiceType' => $invoiceType,
            'channel' => $channel,
        ]);

        return new PocketPendingPayment($payment, [
            ...$result,
            '_data' => [
                'terminal_id' => $terminalId,
                'invoice_type' => $invoiceType,
                'channel' => $channel,
            ],
        ]);
    }

    public function check(Payment $payment): CheckedPayment
    {
        $response = $this->client->request('POST', 'v2/invoicing/invoices/invoice-id', [
            'terminalId' => $payment->driver_data['terminal_id'],
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
        $url = route(RouteConfig::ROUTE_NOTIFICATION_POCKET);

        $result = $this->client->request('POST', 'pg/config', ['fallBackUrl' => $url]);

        if ($result['fallBackUrl'] !== $url) {
            throw new \Exception('Failed to register notification config');
        }
    }
}
