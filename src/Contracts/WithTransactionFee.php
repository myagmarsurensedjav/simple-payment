<?php

namespace Selmonal\SimplePayment\Contracts;

interface WithTransactionFee
{
    public function getTransactionFee(): float;
}
