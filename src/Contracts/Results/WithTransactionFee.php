<?php

namespace MyagmarsurenSedjav\SimplePayment\Contracts\Results;

interface WithTransactionFee
{
    public function getTransactionFee(): float;
}
