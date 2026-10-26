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
