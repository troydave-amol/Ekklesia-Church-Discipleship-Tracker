<?php

namespace App\Mail;

use App\Models\TenantSignup;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class TenantRejectedMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    /**
     * Number of times the queued job may be attempted.
     */
    public int $tries = 3;

    /**
     * Seconds to wait before retrying a failed attempt.
     */
    public int $backoff = 60;

    public function __construct(
        public readonly TenantSignup $signup
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Your Ekklesia Registration Update',
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.tenant.rejected',
            with: [
                'churchName'    => $this->signup->church_name,
                'pastorName'    => $this->signup->pastor_name,
                'rejectionNote' => $this->signup->rejection_note,
            ],
        );
    }
}
