<?php

namespace MyagmarsurenSedjav\SimplePayment\Commands;

use Illuminate\Console\Command;
use MyagmarsurenSedjav\SimplePayment\Facades\SimplePayment;

class ClearExpiredCommand extends Command
{
    public $signature = 'simple-payment:clear-expired';

    public $description = 'Clear expired payments that created more than 7 days ago.';

    public function handle()
    {
        $count = SimplePayment::paymentModel()::expired()
            ->where('created_at', '<', now()->subDays(7))
            ->delete();

        $this->info("{$count} payment(s) cleared.");
    }
}
