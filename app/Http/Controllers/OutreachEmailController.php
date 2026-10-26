<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use App\Models\CrmTask;
use App\Models\OutreachEmail;
use App\Services\SentMailArchiver;
use Illuminate\Http\RedirectResponse;
use Illuminate\Mail\Message;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Symfony\Component\Mailer\SentMessage;
use Throwable;

class OutreachEmailController extends Controller
{
    public function send(OutreachEmail $outreachEmail, SentMailArchiver $archiver): RedirectResponse
    {
        $claimed = OutreachEmail::query()
            ->whereKey($outreachEmail->getKey())
            ->whereNull('sent_at')
            ->whereIn('status', ['ready', 'approved'])
            ->update(['status' => 'sending']);

        if ($claimed !== 1) {
            return back()->with('error', 'This email is not ready to send or has already been sent.');
        }

        $outreachEmail->refresh();

        try {
            $sentMessage = Mail::raw($outreachEmail->body, function (Message $message) use ($outreachEmail): void {
                $message->to($outreachEmail->recipient_email, $outreachEmail->recipient_name)
                    ->subject($outreachEmail->subject);
            });
        } catch (Throwable $exception) {
            report($exception);
            $outreachEmail->update(['status' => 'approved']);

            return back()->with('error', 'The email could not be sent. Nothing was marked as sent.');
        }

        $sentAt = now();
        $followUpAt = $outreachEmail->follow_up_at ?? $sentAt->copy()->addDays(4);

        DB::transaction(function () use ($outreachEmail, $sentMessage, $sentAt, $followUpAt): void {
            $outreachEmail->update([
                'status' => 'sent',
                'sent_at' => $sentAt,
                'follow_up_at' => $followUpAt,
                'external_message_id' => $sentMessage instanceof SentMessage ? $sentMessage->getMessageId() : null,
            ]);

            $outreachEmail->lead?->update([
                'status' => 'contacted',
                'next_follow_up_at' => $followUpAt,
            ]);

            Activity::create([
                'contact_id' => $outreachEmail->contact_id,
                'lead_id' => $outreachEmail->lead_id,
                'type' => 'email',
                'subject' => $outreachEmail->subject,
                'details' => 'Outreach email sent to '.$outreachEmail->recipient_email.'.',
                'occurred_at' => $sentAt,
            ]);

            CrmTask::firstOrCreate(
                [
                    'contact_id' => $outreachEmail->contact_id,
                    'lead_id' => $outreachEmail->lead_id,
                    'title' => 'Follow up: '.$outreachEmail->subject,
                ],
                [
                    'description' => 'Follow up on the outreach email sent to '.$outreachEmail->recipient_email.'.',
                    'status' => 'todo',
                    'priority' => 'normal',
                    'due_at' => $followUpAt,
                ],
            );
        });

        if ($sentMessage instanceof SentMessage) {
            try {
                $archiver->archive($sentMessage);
            } catch (Throwable $exception) {
                report($exception);

                return back()->with('warning', 'The email was sent and recorded, but its Roundcube Sent copy could not be saved.');
            }
        }

        return back()->with('success', 'Email sent, recorded in the activity history and scheduled for follow-up.');
    }
}
