<?php

namespace App\Console\Commands;

use App\Models\OutreachEmail;
use App\Services\SentMailArchiver;
use Illuminate\Console\Command;

class ArchiveSentOutreachCopies extends Command
{
    protected $signature = 'mail:archive-outreach-copies';

    protected $description = 'Archive delivered outreach messages in the Roundcube Sent folder';

    public function handle(SentMailArchiver $archiver): int
    {
        $count = 0;
        OutreachEmail::query()->whereNotNull('sent_at')->whereNull('sent_copy_archived_at')->orderBy('id')->each(function (OutreachEmail $email) use ($archiver, &$count): void {
            $archiver->archiveRecordToMaildir($email);
            $email->update(['sent_copy_archived_at' => now()]);
            $count++;
        });

        $this->info("Archived {$count} outreach messages.");

        return self::SUCCESS;
    }
}
