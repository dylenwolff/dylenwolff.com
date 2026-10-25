<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AdminLoginCode extends Notification
{
    use Queueable;

    public function __construct(private readonly string $code) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Your Dylen Wolff Business Hub login code')
            ->greeting('Your login code is '.$this->code)
            ->line('Enter this six-digit code to sign in to the Dylen Wolff Business Hub.')
            ->line('The code expires in 10 minutes and can only be used once.')
            ->line('If you did not request this code, you can safely ignore this email.');
    }
}
