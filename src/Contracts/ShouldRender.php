<?php

namespace MyagmarsurenSedjav\SimplePayment\Contracts;

use Illuminate\View\View;

interface ShouldRender
{
    public function render(): View;
}
