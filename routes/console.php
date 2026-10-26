<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use App\Models\OutreachEmail;
use App\Services\OutreachEmailSender;

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
