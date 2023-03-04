<?php

namespace MyagmarsurenSedjav\SimplePayment\Contracts;

use Carbon\Carbon;

interface WithExpiresAt
{
    public function getExpiresAt(): Carbon;
}
