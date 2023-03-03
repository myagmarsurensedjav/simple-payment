<?php

namespace Selmonal\SimplePayment\Contracts;

interface WithRedirectUrl
{
    public function getRedirectUrl(): string;
}
