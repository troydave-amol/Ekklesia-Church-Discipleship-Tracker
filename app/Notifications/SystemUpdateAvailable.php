<?php

namespace App\Notifications;

use App\Models\VersionHistory;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class SystemUpdateAvailable extends Notification
{
    use Queueable;

    public function __construct(public readonly VersionHistory $release) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'type'       => 'system_update',
            'version'    => $this->release->version,
            'title'      => "System updated to v{$this->release->version}",
            'message'    => $this->release->title,
            'is_major'   => $this->release->is_major,
            'url'        => '/system/changelog',
            'release_id' => $this->release->id,
        ];
    }
}
