<?php

namespace App\Notifications;

use App\Models\SupportTicket;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class SupportTicketReplied extends Notification
{
    use Queueable;

    public function __construct(
        private SupportTicket $ticket,
        private string $replierName,
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'title'   => 'Ticket Reply',
            'message' => "{$this->replierName} replied to \"{$this->ticket->subject}\".",
            'icon'    => 'message-circle',
            'url'     => "/support/{$this->ticket->id}",
        ];
    }
}
