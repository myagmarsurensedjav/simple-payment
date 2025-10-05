<?php

namespace MyagmarsurenSedjav\SimplePayment\Drivers\Pocket;

use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;
use Illuminate\View\View;
use MyagmarsurenSedjav\SimplePayment\Contracts\Results\ShouldRender;
use MyagmarsurenSedjav\SimplePayment\Contracts\Results\WithBase64QrImage;
use MyagmarsurenSedjav\SimplePayment\Contracts\Results\WithDriverData;
use MyagmarsurenSedjav\SimplePayment\Contracts\Results\WithTransactionId;
use MyagmarsurenSedjav\SimplePayment\Contracts\Results\WithUrls;
use MyagmarsurenSedjav\SimplePayment\PendingPayment;

class PocketPendingPayment extends PendingPayment implements ShouldRender, WithBase64QrImage, WithDriverData, WithTransactionId, WithUrls
{
    public function getBase64QrImage(): string
    {
        $writer = new PngWriter;

        $qrCode = QrCode::create($this->driverResponse['qr']);

        return base64_encode($writer->write($qrCode)->getString());
    }

    public function getTransactionId(): string
    {
        return (string) $this->driverResponse['id'];
    }

    public function getUrls(): array
    {
        return [
            [
                'icon' => 'https://pocket.mn/_nuxt/img/pocket-logo-black.1295461.png',
                'label' => 'Pocket',
                'url' => $this->driverResponse['deeplink'],
            ],
        ];
    }

    public function render(): View
    {
        return view('simple-payment::render', ['pendingPayment' => $this]);
    }

    public function getDriverData(): array
    {
        return $this->driverResponse['_data'];
    }
}
