<?php

namespace MyagmarsurenSedjav\SimplePayment\Exceptions;

final class InvalidPayable extends \Exception
{
    public static function notSet(): static
    {
        return new static('The payable is not set.');
    }
}
