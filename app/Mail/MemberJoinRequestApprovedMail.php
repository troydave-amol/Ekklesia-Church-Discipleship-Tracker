<?php

namespace App\Mail;

use App\Models\MemberJoinRequest;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class MemberJoinRequestApprovedMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public int $tries = 3;
    public int $backoff = 60;

    public function __construct(
        public readonly MemberJoinRequest $joinRequest,
        public readonly User $member,
        public readonly string $loginUrl,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Your church join request was approved',
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.member_join_requests.approved',
            with: [
                'memberName' => $this->member->fullName(),
                'roleLabel' => $this->joinRequest->requested_role === 'program_leader' ? 'Program Leader' : 'Disciple',
                'loginUrl' => $this->loginUrl,
                'email' => $this->member->email,
            ],
        );
    }
}
