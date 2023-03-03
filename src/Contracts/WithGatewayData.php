<?php

namespace Selmonal\SimplePayment\Contracts;

interface WithGatewayData
{
    public function getGatewayData(): array;
}
