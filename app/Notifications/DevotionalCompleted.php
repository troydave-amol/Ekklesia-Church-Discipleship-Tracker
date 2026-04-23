<?php

namespace App\Notifications;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class DevotionalCompleted extends Notification
{
    use Queueable;

    public function __construct(
        private User $disciple,
        private string $date,
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toDatabase(object $notifiable): array
    {
        $name = $this->disciple->first_name . ' ' . $this->disciple->last_name;

        return [
            'title'   => 'Devotional Completed',
            'message' => "{$name} completed their devotional for {$this->date}.",
            'icon'    => 'book-open',
            'url'     => route('devotionals.index', ['week' => $this->date]),
        ];
    }
}
