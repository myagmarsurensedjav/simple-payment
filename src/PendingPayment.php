<?php

namespace MyagmarsurenSedjav\SimplePayment;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Responsable;
use MyagmarsurenSedjav\SimplePayment\Contracts\Results\ShouldRedirect;
use MyagmarsurenSedjav\SimplePayment\Contracts\Results\ShouldRender;
use MyagmarsurenSedjav\SimplePayment\Contracts\Results\WithBase64QrImage;
use MyagmarsurenSedjav\SimplePayment\Contracts\Results\WithUrls;

abstract class PendingPayment implements Arrayable, Responsable
{
    public function __construct(public Payment $payment, public array $driverResponse = []) {}

    public function toArray(): array
    {
        return [
            'handler' => $this instanceof ShouldRedirect ? 'redirect' : 'render',
            'redirect_url' => $this instanceof ShouldRedirect ? $this->getRedirectUrl() : null,
            'qr_image' => $this instanceof WithBase64QrImage ? $this->getBase64QrImage() : null,
            'urls' => $this instanceof WithUrls ? $this->getUrls() : null,
            'payment' => $this->payment,
            'driver_response' => $this->driverResponse,
        ];
    }

    public function toResponse($request): mixed
    {
        if ($request->wantsJson()) {
            return response()->json($this->toArray());
        }

        if ($this instanceof ShouldRedirect) {
            return response()->redirectTo($this->getRedirectUrl());
        }

        if ($this instanceof ShouldRender) {
            return response($this->render());
        }

        throw new \Exception('PendingPayment must implement ShouldRedirect or ShouldRender interface.');
    }
}
