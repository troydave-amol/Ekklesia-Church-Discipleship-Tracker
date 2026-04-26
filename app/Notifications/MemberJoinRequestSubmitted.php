<?php

namespace App\Notifications;

use App\Models\MemberJoinRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class MemberJoinRequestSubmitted extends Notification
{
    use Queueable;

    public function __construct(
        private MemberJoinRequest $joinRequest,
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toDatabase(object $notifiable): array
    {
        $name = trim($this->joinRequest->first_name . ' ' . $this->joinRequest->last_name);
        $role = $this->joinRequest->requested_role === 'program_leader' ? 'Program Leader' : 'Disciple';

        return [
            'title' => 'New Join Request',
            'message' => "{$name} requested to join as {$role}.",
            'icon' => 'user-plus',
            'url' => route('users.index', ['tab' => 'pending']),
        ];
    }
}
