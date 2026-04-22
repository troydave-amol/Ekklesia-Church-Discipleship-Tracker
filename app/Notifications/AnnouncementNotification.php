<?php

namespace App\Notifications;

use App\Models\Announcement;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class AnnouncementNotification extends Notification
{
    use Queueable;

    public function __construct(
        protected Announcement $announcement
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'title' => $this->announcement->title,
            'message' => $this->announcement->body,
            'announcement_id' => $this->announcement->id,
            'audience' => $this->announcement->audience,
            'url' => '/announcements',
        ];
    }
}
