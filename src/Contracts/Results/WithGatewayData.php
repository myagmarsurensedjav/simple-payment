<?php

namespace MyagmarsurenSedjav\SimplePayment\Contracts\Results;

interface WithGatewayData
{
    public function getGatewayData(): array;
}
