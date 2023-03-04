<?php

namespace MyagmarsurenSedjav\SimplePayment\Gateways\Golomt;

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;

class GolomtClient
{
    public function __construct(private readonly array $config)
    {
    }

    public function createInvoice(array $data): array
    {
        $data['checksum'] = $this->generateChecksum($data['transactionId'].$data['amount'].$data['returnType'].$data['callback']);

        return $this->sendRequest('post', '/api/invoice', $data);
    }

    public function checkPayment(string $transactionId): array
    {
        $data = [
            'transactionId' => $transactionId,
            'checksum' => $this->generateChecksum($transactionId.$transactionId),
        ];

        return $this->sendRequest('post', '/api/inquiry', $data);
    }

    public function getBaseUrl(): string
    {
        return 'https://ecommerce.golomtbank.com';
    }

    private function sendRequest(string $method, string $path, array $data = []): array
    {
        $response = $this->makeRequest()->{$method}($this->getBaseUrl().$path, $data);

        return $response->throw()->json();
    }

    private function makeRequest(): PendingRequest
    {
        return Http::withHeaders($this->getAuthorizationHeaders());
    }

    private function getAuthorizationHeaders(): array
    {
        return [
            'Authorization' => 'Bearer '.$this->config['access_token'],
        ];
    }

    private function generateChecksum($value): string
    {
        return hash_hmac('sha256', $value, $this->config['hash_key']);
    }
}
