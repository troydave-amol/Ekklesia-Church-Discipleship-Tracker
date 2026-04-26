<?php

namespace App\Mail;

use App\Models\Tenant;
use App\Services\TenantProvisionService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class TenantWelcomeMail extends Mailable implements ShouldQueue
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
        public readonly Tenant $tenant,
        private readonly string $plainPassword = '',
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Your Ekklesia Workspace is Ready!',
        );
    }

    public function content(): Content
    {
        $loginUrl = app(TenantProvisionService::class)->buildTenantUrl($this->tenant->subdomain, 'login');

        return new Content(
            markdown: 'emails.tenant.welcome',
            with: [
                'tenantName'    => $this->tenant->name,
                'subdomain'     => $this->tenant->subdomain,
                'loginUrl'      => $loginUrl,
                'loginEmail'    => $this->tenant->email,
                'loginPassword' => $this->plainPassword,
                'trialEnds'     => $this->tenant->trial_ends_at?->format('F j, Y'),
                'plan'          => ucfirst($this->tenant->subscription_plan ?? 'free'),
            ],
        );
    }
}