<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class OutreachMessageMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public string $messageSubject,
        public string $messageBody,
        public ?string $recipientName = null,
    ) {}

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
