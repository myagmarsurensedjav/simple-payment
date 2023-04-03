<?php

namespace MyagmarsurenSedjav\SimplePayment\Contracts\Results;

use Illuminate\View\View;

interface ShouldRender
{
    public function render(): View;
}
