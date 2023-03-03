<?php

namespace Selmonal\SimplePayment\Commands;

use Illuminate\Console\Command;
use Selmonal\SimplePayment\Payment;

class ClearExpiredCommand extends Command
{
    public $signature = 'simple-payment:clear-expired';

    public $description = 'Clear expired payments that created more than 7 days ago.';

    public function handle()
    {
        $count = Payment::expired()
            ->where('created_at', '<', now()->subDays(7))
            ->delete();

        $this->info("{$count} payment(s) cleared.");
    }
}
