<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use App\Models\OutreachEmail;
use App\Services\OutreachEmailSender;
use App\Models\Activity;
use App\Models\CrmTask;
use Illuminate\Support\Facades\DB;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('outreach:send {id}', function (int $id, OutreachEmailSender $sender) {
    $email = OutreachEmail::query()->findOrFail($id);
    $archived = $sender->send($email);

    $this->info('Sent outreach email '.$email->id.' to '.$email->recipient_email.'.');

    if (! $archived) {
        $this->warn('The email was sent, but its Roundcube Sent copy was not saved.');
    }
})->purpose('Send one approved outreach email and record its CRM follow-up');

Artisan::command('outreach:send-approved {--country= : Two-letter organization country code} {--limit=10}', function (OutreachEmailSender $sender) {
    $query = OutreachEmail::query()
        ->with('organization')
        ->where('status', 'approved')
        ->whereNull('sent_at')
        ->orderBy('id');

    if ($country = $this->option('country')) {
        $query->whereHas('organization', fn ($organization) => $organization->where('country_code', strtoupper((string) $country)));
    }

    $emails = $query->limit(max(1, (int) $this->option('limit')))->get();

    if ($emails->isEmpty()) {
        $this->warn('No approved outreach emails matched the filters.');

        return;
    }

    foreach ($emails as $email) {
        try {
            $archived = $sender->send($email);
            $this->info("Sent {$email->id} to {$email->recipient_email}".($archived ? '' : ' (Sent copy unavailable)'));
        } catch (Throwable $exception) {
            report($exception);
            $this->error("Failed {$email->id} for {$email->recipient_email}: {$exception->getMessage()}");
        }
    }
})->purpose('Send approved outreach emails using optional country and batch-size filters');

Artisan::command('outreach:mark-bounced {email} {--reason=} {--alternate=}', function (string $email) {
    $outreach = OutreachEmail::query()
        ->where('recipient_email', $email)
        ->latest('id')
        ->firstOrFail();
    $reason = trim((string) $this->option('reason')) ?: 'The receiving mail server rejected delivery.';
    $alternate = trim((string) $this->option('alternate'));

    DB::transaction(function () use ($outreach, $reason, $alternate): void {
        $outreach->update([
            'status' => 'bounced',
            'follow_up_at' => null,
            'notes' => trim(implode("\n", array_filter([$outreach->notes, $reason]))),
        ]);

        $outreach->lead?->update([
            'status' => 'new',
            'next_follow_up_at' => null,
        ]);

        CrmTask::query()
            ->where('lead_id', $outreach->lead_id)
            ->where('title', 'Follow up: '.$outreach->subject)
            ->where('status', 'todo')
            ->delete();

        Activity::create([
            'contact_id' => $outreach->contact_id,
            'lead_id' => $outreach->lead_id,
            'type' => 'email',
            'subject' => 'Delivery failed: '.$outreach->subject,
            'details' => $reason,
            'occurred_at' => now(),
        ]);

        if ($alternate !== '' && $outreach->organization) {
            $organization = $outreach->organization;
            $organization->update([
                'email' => $alternate,
                'notes' => trim(implode("\n", array_filter([
                    $organization->notes,
                    'Verified alternate contact: '.$alternate.'. Original delivery failed on '.now()->toDateString().'.',
                ]))),
            ]);
        }
    });

    $this->info("Marked outreach {$outreach->id} to {$email} as bounced.");
})->purpose('Record a delivery failure, clear its follow-up and preserve an alternate contact');
