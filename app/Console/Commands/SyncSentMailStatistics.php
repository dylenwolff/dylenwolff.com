<?php

namespace App\Console\Commands;

use App\Support\SentMailStatistics;
use Illuminate\Console\Command;

class SyncSentMailStatistics extends Command
{
    protected $signature = 'mail:sync-sent-statistics';

    protected $description = 'Synchronize Sent mailbox counts for the CRM dashboard';

    public function handle(): int
    {
        $snapshot = SentMailStatistics::sync();
        $this->info("Synchronized {$snapshot['total']} sent messages.");

        return self::SUCCESS;
    }
}
