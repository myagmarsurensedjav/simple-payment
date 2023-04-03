<?php

namespace MyagmarsurenSedjav\SimplePayment\Contracts\Results;

use Carbon\Carbon;

interface WithExpiresAt
{
    public function getExpiresAt(): Carbon;
}
