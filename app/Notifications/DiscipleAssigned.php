<?php

namespace App\Notifications;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class DiscipleAssigned extends Notification
{
    use Queueable;

    public function __construct(
        private User $disciple,
        private User $assignedBy,
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toDatabase(object $notifiable): array
    {
        $name = $this->disciple->first_name . ' ' . $this->disciple->last_name;

        return [
            'title'   => 'New Disciple Assigned',
            'message' => "{$name} has been assigned to you by {$this->assignedBy->first_name} {$this->assignedBy->last_name}.",
            'icon'    => 'user-plus',
            'url'     => route('users.show', $this->disciple->id),
        ];
    }
}
