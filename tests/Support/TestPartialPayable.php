<?php

namespace MyagmarsurenSedjav\SimplePayment\Tests\Support;

use MyagmarsurenSedjav\SimplePayment\Contracts\PartialPayable;

class TestPartialPayable extends TestPayable implements PartialPayable
{
    protected $table = 'test_payables';
}
