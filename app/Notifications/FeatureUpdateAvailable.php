<?php

namespace App\Notifications;

use App\Models\FeatureUpdate;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class FeatureUpdateAvailable extends Notification
{
    use Queueable;

    public function __construct(public readonly FeatureUpdate $featureUpdate) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'type'       => 'feature_update',
            'name'       => $this->featureUpdate->name,
            'version'    => $this->featureUpdate->version,
            'title'      => "New feature available: {$this->featureUpdate->name} v{$this->featureUpdate->version}",
            'message'    => $this->featureUpdate->description ?? 'A new feature is ready to install.',
            'url'        => '/system/changelog',
            'feature_id' => $this->featureUpdate->id,
        ];
    }
}
