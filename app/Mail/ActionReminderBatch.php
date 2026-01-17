<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Collection;

class ActionReminderBatch extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Collection $overdueActions,
        public Collection $dueSoonActions,
        public User $sender,
        public string $personName,
    ) {}

    public function envelope(): Envelope
    {
        $subject = $this->overdueActions->isNotEmpty()
            ? 'Quick check-in: Action items update'
            : 'Upcoming: Action items due soon';

        return new Envelope(
            subject: $subject,
            replyTo: $this->sender->email,
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.action-reminder-batch',
            with: [
                'overdueActions' => $this->overdueActions,
                'dueSoonActions' => $this->dueSoonActions,
                'sender' => $this->sender,
                'personName' => $this->personName,
            ],
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
