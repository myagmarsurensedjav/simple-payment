<?php

namespace MyagmarsurenSedjav\SimplePayment\Exceptions;

class InvalidPayment extends \Exception
{

    public static function notPaid(): static
    {
        return new static('The payment is not paid.');
    }
}
