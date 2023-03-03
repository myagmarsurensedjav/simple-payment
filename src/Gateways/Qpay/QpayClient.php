<?php

namespace Selmonal\SimplePayment\Gateways\Qpay;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class QpayClient
{
    public function __construct(private array $config)
    {
    }

    public function createSimpleInvoice(string $invoiceId, $amount, $description, $userId, $callbackUrl): array
    {
        if ($this->env('fake')) {
            return json_decode(file_get_contents(__DIR__.'/result.json'), true);
        }

        return $this->request('post', $this->path('/invoice'), [
            'invoice_code' => $this->config['invoice_code'],
            'sender_invoice_no' => $invoiceId,
            'invoice_receiver_code' => $userId ?: (string) Str::uuid(),
            'invoice_description' => $description,
            'sender_branch_code' => '1',
            'amount' => $amount,
            'callback_url' => $callbackUrl,
        ]);
    }

    public function checkPayment(string $invoiceId): array
    {
        if ($this->env('fake')) {
            return [
                'count' => 1,
                'paid_amount' => 5000,
            ];
        }

        return $this->request('post', $this->path('/payment/check'), [
            'object_type' => 'INVOICE',
            'object_id' => $invoiceId,
            'offset' => [
                'page_number' => 1,
                'page_limit' => 100,
            ],
        ]);
    }

    private function request(string $method, string $uri, array $data = []): array
    {
        return Http::withToken($this->getAccessToken())
            ->retry(3, 200)
            ->$method($uri, $data)
            ->throw()
            ->json();
    }

    private function path(string $path): string
    {
        return $this->getBaseUrl().$path;
    }

    private function getBaseUrl(): string
    {
        if ($this->env() == 'sandbox') {
            return 'https://merchant-sandbox.qpay.mn/v2';
        }

        return 'https://merchant.qpay.mn/v2';
    }

    public function getAccessToken(bool $clearAccessTokenCache = false): string
    {
        $cacheKey = 'qpay.access_token';

        if ($clearAccessTokenCache) {
            Cache::forget($cacheKey);
        }

        return Cache::remember($cacheKey, now()->addHours(23), function () {
            logger()->info('Get new access token for qpay-v1.');

            return Http::asJson()
                ->retry(3, 1000)
                ->withBasicAuth($this->config['username'], $this->config['password'])
                ->post($this->path('/auth/token'))
                ->throw()
                ->json()['access_token'];
        });
    }

    public function env($env = null): mixed
    {
        return is_null($env)
            ? $this->config['env']
            : $this->config['env'] == $env;
    }
}
