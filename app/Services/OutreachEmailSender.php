<?php

namespace App\Services;

use App\Models\Activity;
use App\Models\CrmTask;
use App\Models\OutreachEmail;
use App\Models\OutreachDeliveryMetric;
use Illuminate\Mail\Message;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use RuntimeException;
use Symfony\Component\Mailer\SentMessage;
use Throwable;

class OutreachEmailSender
{
    public function __construct(private readonly SentMailArchiver $archiver)
    {
    }

    /**
     * Send an approved queue item and record its CRM history.
     *
     * @return bool Whether the message was also archived in the Sent mailbox.
     */
    public function send(OutreachEmail $outreachEmail): bool
    {
        $claimed = OutreachEmail::query()
            ->whereKey($outreachEmail->getKey())
            ->whereNull('sent_at')
            ->whereIn('status', ['ready', 'approved'])
            ->update(['status' => 'sending']);

        if ($claimed !== 1) {
            throw new RuntimeException('This email is not ready to send or has already been sent.');
        }

        $outreachEmail->refresh();

        try {
            $sentMessage = Mail::raw($outreachEmail->body, function (Message $message) use ($outreachEmail): void {
                $message->to($outreachEmail->recipient_email, $outreachEmail->recipient_name)
                    ->subject($outreachEmail->subject);
            });
        } catch (Throwable $exception) {
            $outreachEmail->update(['status' => 'approved']);
            throw $exception;
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

            $metric = OutreachDeliveryMetric::current();
            $metric->increment('total_sent');
            $metric->increment('accepted');
            $metric->update(['measured_at' => $sentAt]);
        });

        if (! $sentMessage instanceof SentMessage) {
            return true;
        }

        try {
            $this->archiver->archive($sentMessage);
        } catch (Throwable $exception) {
            report($exception);

            return false;
        }

        return true;
    }
}
