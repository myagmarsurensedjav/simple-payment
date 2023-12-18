<?php

namespace MyagmarsurenSedjav\SimplePayment\Drivers\Pocket;

use Illuminate\Support\Facades\Http;

class PocketClient
{
    public function __construct(private array $config)
    {
    }

    public function request(string $method, string $path, array $params = [])
    {
        return Http::withToken($this->getTokenWithCache())
            ->acceptJson()
            ->{$method}($this->getApiUrl($path), $params)
            ->throw()
            ->json();
    }

    private function getToken(): string
    {
        $params = [
            'grant_type' => 'client_credentials',
            'client_id' => $this->config['client_id'],
            'client_secret' => $this->config['client_secret'],
        ];

        return Http::asForm()
            ->post($this->getAuthBaseUrl('auth/realms/invescore/protocol/openid-connect/token'), $params)
            ->throw()
            ->json('access_token');
    }

    private function getApiUrl(string $path): string
    {
        return $this->getBaseUrl().'/'.$path;
    }

    private function getAuthBaseUrl(string $path): string
    {
        return 'https://sso.invescore.mn/'.$path;
    }

    private function getTokenWithCache(): string
    {
        // Generate token cache name from client_id with sha1
        $cacheName = 'pocket_token_'.sha1($this->config['client_id']);

        return cache()->remember(
            $cacheName,
            60 * 60 * 24, // 1 day
            fn() => $this->getToken()
        );
    }

    private function getBaseUrl(): string
    {
        return 'https://service.invescore.mn/merchant';
    }

    public function getConfig(): array
    {
        return $this->config;
    }
}
