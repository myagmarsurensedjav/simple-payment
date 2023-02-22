<?php

namespace Selmonal\LaravelSimplePayment\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Selmonal\LaravelSimplePayment\LaravelSimplePayment
 */
class LaravelSimplePayment extends Facade
{
    protected static function getFacadeAccessor()
    {
        return \Selmonal\LaravelSimplePayment\LaravelSimplePayment::class;
    }
}
