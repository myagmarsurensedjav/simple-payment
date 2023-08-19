<?php

namespace MyagmarsurenSedjav\SimplePayment;

use Closure;
use Illuminate\Support\Manager;
use MyagmarsurenSedjav\SimplePayment\Drivers\AbstractDriver;
use MyagmarsurenSedjav\SimplePayment\Drivers\Golomt\GolomtDriver;
use MyagmarsurenSedjav\SimplePayment\Drivers\Qpay\QpayDriver;

class SimplePaymentManager extends Manager
{
    private Closure $browserReturnHandler;

    public function getDefaultDriver()
    {
        return $this->config->get('simple-payment.default');
    }

    public function createGolomtDriver(): AbstractDriver
    {
        return new GolomtDriver(
            name: 'golomt',
            config: $this->config->get('simple-payment.drivers.golomt'),
            isSocialPay: false
        );
    }

    public function createSocialPayDriver(): AbstractDriver
    {
        return new GolomtDriver(
            name: 'socialpay',
            config: $this->config->get('simple-payment.drivers.golomt'),
            isSocialPay: true
        );
    }

    public function createQpayDriver(): AbstractDriver
    {
        return new QpayDriver(
            name: 'qpay',
            config: $this->config->get('simple-payment.drivers.qpay')
        );
    }

    public function onBrowserReturn(Closure $handler): void
    {
        $this->browserReturnHandler = $handler;
    }

    public function handleBrowserReturn(CheckedPayment $checkedPayment): mixed
    {
        if (isset($this->browserReturnHandler)) {
            return ($this->browserReturnHandler)($checkedPayment);
        }

        return null;
    }

    public function userModel(): string
    {
        return config('simple-payment.user_model');
    }

    public function paymentModel(): string
    {
        return config('simple-payment.payment_model');
    }
}
