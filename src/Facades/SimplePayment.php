<?php

namespace Selmonal\SimplePayment\Facades;

use Illuminate\Support\Facades\Facade;
use Selmonal\SimplePayment\CheckedPayment;
use Selmonal\SimplePayment\Gateways\AbstractGateway;
use Selmonal\SimplePayment\PendingPayment;
use Selmonal\SimplePayment\SimplePaymentManager;

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
