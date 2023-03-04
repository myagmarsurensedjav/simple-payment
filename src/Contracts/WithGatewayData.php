<?php

namespace MyagmarsurenSedjav\SimplePayment\Contracts;

interface WithGatewayData
{
    public function getGatewayData(): array;
}
