<?php

namespace App\Mail;

use App\Support\OutreachBodyFormatter;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class OutreachMessageMail extends Mailable
{
    use Queueable, SerializesModels;

    public string $messageSubject;

    public string $messageBody;

    public ?string $recipientName;

    public function __construct(string $messageSubject, string $messageBody, ?string $recipientName = null)
    {
        $this->messageSubject = $messageSubject;
        $this->messageBody = OutreachBodyFormatter::forDelivery($messageBody);
        $this->recipientName = $recipientName;
    }

    public function envelope(): Envelope
    {
        return new Envelope(subject: $this->messageSubject);
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.outreach-html',
            text: 'emails.outreach-text',
        );
    }
}
