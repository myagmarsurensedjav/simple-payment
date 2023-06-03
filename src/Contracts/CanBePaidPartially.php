<?php

namespace MyagmarsurenSedjav\SimplePayment\Contracts;

interface CanBePaidPartially
{
    public function canBePaidPartially(): bool;
}
