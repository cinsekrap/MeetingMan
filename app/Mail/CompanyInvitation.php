<?php

namespace App\Mail;

use App\Models\CompanyInvite;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class CompanyInvitation extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(
        public CompanyInvite $invite
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "You're invited to join {$this->invite->company->name} on MeetingMan",
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.company-invitation',
            with: [
                'invite' => $this->invite,
                'acceptUrl' => route('company.setup', ['invite' => $this->invite->token]),
            ],
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
