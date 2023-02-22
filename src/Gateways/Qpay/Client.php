<?php

namespace Selmonal\LaravelSimplePayment\Gateways\Qpay;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class Client
{
    public function createSimpleInvoice($invoiceId, $amount, $description, $userId, $callbackUrl): array
    {
        if ($this->env('fake')) {
            return json_decode(file_get_contents(__DIR__.'/result.json'), true);
        }

        return $this->request('post', $this->path('/invoice'), [
            'invoice_code' => config('simple-payment.qpay.invoice_code'),
            'sender_invoice_no' => $invoiceId,
            'invoice_receiver_code' => (string) $userId,
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
                ->withBasicAuth(
                    config('simple-payment.qpay.username'),
                    config('simple-payment.qpay.password'),
                )
                ->post($this->path('/auth/token'))
                ->throw()
                ->json()['access_token'];
        });
    }

    public function env($env = null): mixed
    {
        return is_null($env)
            ? config('simple-payment.qpay.env')
            : config('simple-payment.qpay.env') == $env;
    }
}
