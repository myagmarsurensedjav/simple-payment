<?php

namespace MyagmarsurenSedjav\SimplePayment\Contracts\Results;

interface WithRedirectUrl
{
    public function getRedirectUrl(): string;
}
