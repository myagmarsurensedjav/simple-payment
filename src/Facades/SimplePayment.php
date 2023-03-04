<?php

namespace MyagmarsurenSedjav\SimplePayment\Facades;

use Illuminate\Support\Facades\Facade;
use MyagmarsurenSedjav\SimplePayment\CheckedPayment;
use MyagmarsurenSedjav\SimplePayment\Gateways\AbstractGateway;
use MyagmarsurenSedjav\SimplePayment\PendingPayment;
use MyagmarsurenSedjav\SimplePayment\SimplePaymentManager;

/**
 * @method static AbstractGateway driver($model, array $options = [])
 * @method static PendingPayment create($model, array $options = [])
 * @method static mixed handleBrowserReturn(CheckedPayment $checkedPayment)
 */
class SimplePayment extends Facade
{
    protected static function getFacadeAccessor()
    {
        return SimplePaymentManager::class;
    }
}
