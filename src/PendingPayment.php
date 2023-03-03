<?php

namespace Selmonal\SimplePayment;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Responsable;
use Selmonal\SimplePayment\Contracts\ShouldRedirect;
use Selmonal\SimplePayment\Contracts\ShouldRender;

abstract class PendingPayment implements Arrayable, Responsable
{
    public function __construct(public Payment $payment, public array $gatewayResponse = [])
    {
    }

    public static function new(Payment $payment, array $gatewayResponse = []): static
    {
        return new static($payment, $gatewayResponse);
    }

    public function toArray(): array
    {
        return [
            'handler' => $this instanceof ShouldRedirect ? 'redirect' : 'render',
            'redirect_url' => $this instanceof ShouldRedirect ? $this->getRedirectUrl() : null,
            'payment' => $this->payment,
            'gateway_response' => $this->gatewayResponse,
        ];
    }

    public function toResponse($request)
    {
        if ($request->wantsJson()) {
            return response()->json($this->toArray());
        }

        if ($this instanceof ShouldRedirect) {
            return redirect($this->getRedirectUrl());
        }

        if ($this instanceof ShouldRender) {
            return $this->render();
        }

        throw new \Exception('PendingPayment must implement ShouldRedirect or ShouldRender interface.');
    }
}
