<?php

namespace App\Notifications;

use App\Models\SupportTicket;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class SupportTicketReplied extends Notification
{
    use Queueable;

    public function __construct(
        private SupportTicket $ticket,
        private User $repliedBy,
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toDatabase(object $notifiable): array
    {
        $name = $this->repliedBy->first_name . ' ' . $this->repliedBy->last_name;

        return [
            'title'   => 'Ticket Reply',
            'message' => "{$name} replied to \"{$this->ticket->subject}\".",
            'icon'    => 'message-circle',
            'url'     => route('support.show', $this->ticket->id),
        ];
    }
}
