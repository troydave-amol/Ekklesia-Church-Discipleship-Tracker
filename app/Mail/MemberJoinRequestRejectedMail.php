<?php

namespace App\Mail;

use App\Models\MemberJoinRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class MemberJoinRequestRejectedMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public int $tries = 3;
    public int $backoff = 60;

    public function __construct(
        public readonly MemberJoinRequest $joinRequest,
        public readonly string $reapplyUrl,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Your church join request was not approved',
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.member_join_requests.rejected',
            with: [
                'memberName' => trim($this->joinRequest->first_name . ' ' . $this->joinRequest->last_name),
                'rejectionNote' => $this->joinRequest->rejection_note,
                'reapplyUrl' => $this->reapplyUrl,
            ],
        );
    }
}
