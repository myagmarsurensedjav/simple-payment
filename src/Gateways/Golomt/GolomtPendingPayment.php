<?php

namespace Selmonal\SimplePayment\Gateways\Golomt;

use Carbon\Carbon;
use Selmonal\SimplePayment\Contracts\ShouldRedirect;
use Selmonal\SimplePayment\Contracts\WithExpiresAt;
use Selmonal\SimplePayment\Contracts\WithTransactionId;
use Selmonal\SimplePayment\PendingPayment;

class GolomtPendingPayment extends PendingPayment implements ShouldRedirect, WithTransactionId, WithExpiresAt
{
    private string $terminal = 'payment';

    public function getRedirectUrl(): string
    {
        return 'https://ecommerce.golomtbank.com/'.$this->terminal.'/mn/'.$this->getTransactionId();
    }

    public function asSocialPay(): static
    {
        $this->terminal = 'socialpay';

        return $this;
    }

    public function getTransactionId(): string
    {
        return $this->gatewayResponse['invoice'];
    }

    public function getExpiresAt(): Carbon
    {
        return now()->addMinutes(10);
    }
}
