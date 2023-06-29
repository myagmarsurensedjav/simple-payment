<?php

namespace MyagmarsurenSedjav\SimplePayment\Exceptions;

final class InvalidPayment extends \Exception
{
    public static function notPaid(): static
    {
        return new self('The payment is not paid.');
    }
}
