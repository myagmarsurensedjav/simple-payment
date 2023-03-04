<?php

namespace MyagmarsurenSedjav\SimplePayment\Contracts;

interface WithTransactionFee
{
    public function getTransactionFee(): float;
}
