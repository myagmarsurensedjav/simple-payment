<?php

namespace MyagmarsurenSedjav\SimplePayment\Contracts\Results;

interface WithBase64QrImage
{
    public function getBase64QrImage(): string;
}
