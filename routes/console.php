<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use App\Models\OutreachEmail;
use App\Models\OutreachDeliveryMetric;
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

Artisan::command('outreach:mark-bounced {email} {--reason=} {--alternate=} {--category=server : reputation, invalid or server}', function (string $email) {
    $outreach = OutreachEmail::query()
        ->where('recipient_email', $email)
        ->latest('id')
        ->firstOrFail();
    $reason = trim((string) $this->option('reason')) ?: 'The receiving mail server rejected delivery.';
    $alternate = trim((string) $this->option('alternate'));
    $category = (string) $this->option('category');

    if (! in_array($category, ['reputation', 'invalid', 'server'], true)) {
        $this->error('Category must be reputation, invalid or server.');

        return self::FAILURE;
    }

    $wasBounced = $outreach->status === 'bounced';

    DB::transaction(function () use ($outreach, $reason, $alternate, $category, $wasBounced): void {
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

        if (! $wasBounced) {
            $metric = OutreachDeliveryMetric::current();
            $metric->accepted = max(0, $metric->accepted - 1);
            $metric->bounced++;
            $metric->{$category === 'reputation' ? 'reputation_blocked' : ($category === 'invalid' ? 'invalid_recipient' : 'server_rejected')}++;
            $metric->measured_at = now();
            $metric->save();
        }
    });

    $this->info("Marked outreach {$outreach->id} to {$email} as bounced.");
})->purpose('Record a delivery failure, clear its follow-up and preserve an alternate contact');

Artisan::command('outreach:reconcile-metrics {total} {accepted} {bounced} {delayed} {reputation} {invalid} {server}', function () {
    $values = collect(['total', 'accepted', 'bounced', 'delayed', 'reputation', 'invalid', 'server'])
        ->mapWithKeys(fn (string $key) => [$key => max(0, (int) $this->argument($key))]);

    if ($values['accepted'] + $values['bounced'] + $values['delayed'] !== $values['total']) {
        $this->error('Accepted, bounced and delayed counts must add up to the total.');

        return self::FAILURE;
    }

    if ($values['reputation'] + $values['invalid'] + $values['server'] !== $values['bounced']) {
        $this->error('The three rejection categories must add up to the bounced count.');

        return self::FAILURE;
    }

    OutreachDeliveryMetric::current()->update([
        'total_sent' => $values['total'],
        'accepted' => $values['accepted'],
        'bounced' => $values['bounced'],
        'delayed' => $values['delayed'],
        'reputation_blocked' => $values['reputation'],
        'invalid_recipient' => $values['invalid'],
        'server_rejected' => $values['server'],
        'measured_at' => now(),
    ]);

    $this->info('Outreach delivery metrics reconciled.');
})->purpose('Set verified aggregate delivery figures from the mailbox');
