<?php

namespace MyagmarsurenSedjav\SimplePayment\Drivers\Golomt;

use Illuminate\Support\Arr;
use MyagmarsurenSedjav\SimplePayment\CheckedPayment;
use MyagmarsurenSedjav\SimplePayment\Enums\PaymentStatus;

class GolomtCheckedPayment extends CheckedPayment
{
    public function status(): PaymentStatus
    {
        // https://dev.golomtbank.com:7443/docs/ecommerce_api_errors/
        // хуудас дахь заавраас хархад 000 -с бусад нь амжилтгүй гүйлгээнд тооцогдоно.
        if ($this->errorCode() === '000') {
            return PaymentStatus::Paid;
        }

        if (is_null($this->errorCode())) {
            return PaymentStatus::Pending;
        }

        return PaymentStatus::Failed;
    }

    public function errorMessage(): ?string
    {
        return Arr::get($this->driverResponse, 'errorDesc');
    }

    private function errorCode()
    {
        return Arr::get($this->driverResponse, 'errorCode');
    }
}
