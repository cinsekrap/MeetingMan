<?php

namespace App\Mail;

use App\Models\Meeting;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Collection;

class MeetingSummary extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Meeting $meeting,
        public User $sender,
        public Collection $overdueActions,
        public Collection $dueSoonActions,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: '1:1 Meeting Summary - ' . $this->meeting->meeting_date->format('j F Y'),
            replyTo: $this->sender->email,
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.meeting-summary',
            with: [
                'meeting' => $this->meeting,
                'sender' => $this->sender,
                'overdueActions' => $this->overdueActions,
                'dueSoonActions' => $this->dueSoonActions,
            ],
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
