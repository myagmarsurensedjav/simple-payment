<?php

namespace Selmonal\LaravelSimplePayment\Commands;

use Illuminate\Console\Command;

class LaravelSimplePaymentCommand extends Command
{
    public $signature = 'laravel-simple-payment';

    public $description = 'My command';

    public function handle(): int
    {
        $this->comment('All done');

        return self::SUCCESS;
    }
}
