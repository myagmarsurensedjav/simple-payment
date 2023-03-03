<?php

namespace Selmonal\SimplePayment\Contracts;

interface WithTransactionId
{
    public function getTransactionId(): string;
}
