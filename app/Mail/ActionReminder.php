<?php

namespace App\Mail;

use App\Models\Action;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ActionReminder extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Action $action,
        public User $sender,
        public string $reminderType, // 'overdue' or 'due_soon'
    ) {}

    public function envelope(): Envelope
    {
        $subject = $this->reminderType === 'overdue'
            ? 'Quick check-in: Action item update'
            : 'Upcoming: Action item due soon';

        return new Envelope(
            subject: $subject,
            replyTo: $this->sender->email,
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.action-reminder',
            with: [
                'action' => $this->action,
                'sender' => $this->sender,
                'reminderType' => $this->reminderType,
                'person' => $this->action->meeting->person,
            ],
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
