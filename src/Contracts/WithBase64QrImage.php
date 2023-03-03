<?php

namespace Selmonal\SimplePayment\Contracts;

interface WithBase64QrImage
{
    public function getBase64QrImage(): string;
}
