<?php

namespace MyagmarsurenSedjav\SimplePayment\Drivers\Qpay;

use Illuminate\View\View;
use MyagmarsurenSedjav\SimplePayment\Contracts\Results\ShouldRender;
use MyagmarsurenSedjav\SimplePayment\Contracts\Results\WithBase64QrImage;
use MyagmarsurenSedjav\SimplePayment\Contracts\Results\WithRedirectUrl;
use MyagmarsurenSedjav\SimplePayment\Contracts\Results\WithTransactionFee;
use MyagmarsurenSedjav\SimplePayment\Contracts\Results\WithTransactionId;
use MyagmarsurenSedjav\SimplePayment\PendingPayment;

class QpayPendingPayment extends PendingPayment implements ShouldRender, WithBase64QrImage, WithRedirectUrl, WithTransactionId, WithTransactionFee
{
    public function getBase64QrImage(): string
    {
        return $this->driverResponse['qr_image'];
    }

    public function getRedirectUrl(): string
    {
        return $this->driverResponse['qPay_shortUrl'];
    }

    public function render(): View
    {
        return view('simple-payment::qpay', [
            'payment' => $this->payment,
            'base64QrImage' => $this->getBase64QrImage(),
            'redirectUrl' => $this->getRedirectUrl(),
            'urls' => $this->driverResponse['urls'],
        ]);
    }

    public function getTransactionId(): string
    {
        return $this->driverResponse['invoice_id'];
    }

    public function getTransactionFee(): float
    {
        return $this->payment->amount * 0.01;
    }
}
