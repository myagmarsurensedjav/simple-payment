<?php

namespace MyagmarsurenSedjav\SimplePayment\Contracts;

interface WithTransactionId
{
    public function getTransactionId(): string;
}
