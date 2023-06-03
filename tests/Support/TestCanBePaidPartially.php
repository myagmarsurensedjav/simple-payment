<?php

namespace MyagmarsurenSedjav\SimplePayment\Tests\Support;

use MyagmarsurenSedjav\SimplePayment\Contracts\CanBePaidPartially;

class TestCanBePaidPartially extends TestPayable implements CanBePaidPartially
{
    public function canBePaidPartially(): bool
    {
        return true;
    }
}
