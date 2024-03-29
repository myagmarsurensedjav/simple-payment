<?php

namespace MyagmarsurenSedjav\SimplePayment\Drivers\Golomt;

use Carbon\Carbon;
use MyagmarsurenSedjav\SimplePayment\Contracts\Results\ShouldRedirect;
use MyagmarsurenSedjav\SimplePayment\Contracts\Results\WithExpiresAt;
use MyagmarsurenSedjav\SimplePayment\Contracts\Results\WithTransactionId;
use MyagmarsurenSedjav\SimplePayment\PendingPayment;

class GolomtPendingPayment extends PendingPayment implements ShouldRedirect, WithExpiresAt, WithTransactionId
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
        return $this->driverResponse['invoice'];
    }

    public function getExpiresAt(): Carbon
    {
        return now()->addMinutes(10);
    }
}
