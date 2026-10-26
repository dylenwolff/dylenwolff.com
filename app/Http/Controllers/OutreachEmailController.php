<?php

namespace App\Http\Controllers;

use App\Models\OutreachEmail;
use App\Services\OutreachEmailSender;
use Illuminate\Http\RedirectResponse;
use Throwable;

class OutreachEmailController extends Controller
{
    public function send(OutreachEmail $outreachEmail, OutreachEmailSender $sender): RedirectResponse
    {
        try {
            $archived = $sender->send($outreachEmail);
        } catch (Throwable $exception) {
            report($exception);

            return back()->with('error', $exception->getMessage());
        }

        if (! $archived) {
            return back()->with('warning', 'The email was sent and recorded, but its Roundcube Sent copy could not be saved.');
        }

        return back()->with('success', 'Email sent, recorded in the activity history and scheduled for follow-up.');
    }
}
