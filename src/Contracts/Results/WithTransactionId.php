<?php

namespace MyagmarsurenSedjav\SimplePayment\Contracts\Results;

interface WithTransactionId
{
    public function getTransactionId(): string;
}
