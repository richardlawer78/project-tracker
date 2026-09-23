<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class UserInvitation extends Notification
{
    use Queueable;

    public function __construct(
        public string $temporaryPassword
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Your Project Tracker Account')
            ->greeting('Welcome to Project Tracker!')
            ->line('An administrator has created an account for you.')
            ->line('Email: ' . $notifiable->email)
            ->line('Temporary password: ' . $this->temporaryPassword)
            ->line('Please log in and change your password immediately.')
            ->action('Log in to Project Tracker', config('app.url'))
            ->line('Your temporary password expires in 24 hours.');
    }
}