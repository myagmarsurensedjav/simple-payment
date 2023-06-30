<?php

namespace MyagmarsurenSedjav\SimplePayment\Tests\Support;

use MyagmarsurenSedjav\SimplePayment\Payment;

class TestPayment extends Payment
{
    protected array $additional = ['foo'];

    public static function use(): string
    {
        config()->set('simple-payment.payment_model', self::class);

        return self::class;
    }
}
