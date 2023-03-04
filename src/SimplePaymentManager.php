<?php

namespace MyagmarsurenSedjav\SimplePayment;

use Closure;
use Illuminate\Support\Manager;
use MyagmarsurenSedjav\SimplePayment\Gateways\AbstractGateway;
use MyagmarsurenSedjav\SimplePayment\Gateways\Golomt\GolomtGateway;
use MyagmarsurenSedjav\SimplePayment\Gateways\Qpay\QpayGateway;

class SimplePaymentManager extends Manager
{
    private Closure $browserReturnHandler;

    public function getDefaultDriver()
    {
        return $this->config->get('simple-payment.default');
    }

    public function createGolomtDriver(): AbstractGateway
    {
        return new GolomtGateway(
            name: 'golomt',
            config: $this->config->get('simple-payment.gateways.golomt'),
            isSocialPay: false
        );
    }

    public function createSocialPayDriver(): AbstractGateway
    {
        return new GolomtGateway(
            name: 'socialpay',
            config: $this->config->get('simple-payment.gateways.golomt'),
            isSocialPay: true
        );
    }

    public function createQpayDriver(): AbstractGateway
    {
        return new QpayGateway(
            name: 'qpay',
            config: $this->config->get('simple-payment.gateways.qpay')
        );
    }

    public function onBrowserReturn(Closure $handler)
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
}
