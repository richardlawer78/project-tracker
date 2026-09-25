<?php

namespace App\Notifications;

use App\Models\Project;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ProjectInvitation extends Notification
{
    use Queueable;

    public function __construct(
        public Project $project,
        public string $invitedBy,
        public string $role
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $role = ucwords(str_replace('-', ' ', $this->role));

        $mail = (new MailMessage)
            ->subject('You have been added to "' . $this->project->name . '"')
            ->greeting('Hello ' . $notifiable->name . '!')
            ->line($this->invitedBy . ' has added you to the project "' . $this->project->name . '" as ' . $role . '.');

        if ($this->project->description) {
            $mail->line('About the project: ' . \Illuminate\Support\Str::limit($this->project->description, 160));
        }

        if ($this->project->start_date || $this->project->end_date) {
            $mail->line(
                'Timeline: '
                . ($this->project->start_date?->format('M j, Y') ?? 'TBD')
                . ' - '
                . ($this->project->end_date?->format('M j, Y') ?? 'TBD')
            );
        }

        return $mail
            ->action('View the project', route('projects.show', $this->project))
            ->line('If you are not signed in yet, you will be asked to log in first.');
    }
}