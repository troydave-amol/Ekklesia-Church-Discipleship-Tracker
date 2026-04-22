<?php

namespace App\Jobs;

use App\Models\Announcement;
use App\Models\Tenant;
use App\Models\User;
use App\Notifications\AnnouncementNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;

class BroadcastAnnouncementJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        protected int $announcementId
    ) {}

    public function handle(): void
    {
        // Fetch announcement from central DB
        $announcement = Announcement::find($this->announcementId);

        if (!$announcement || !$announcement->published_at) {
            Log::warning("BroadcastAnnouncementJob: Announcement #{$this->announcementId} not found or not published.");
            return;
        }

        $tenants = Tenant::active()->get();

        foreach ($tenants as $tenant) {
            try {
                tenancy()->initialize($tenant);

                // Determine target users based on audience
                $users = match ($announcement->audience) {
                    'admins' => User::whereHas('role', function ($q) {
                        $q->whereIn('slug', ['church_admin', 'pastor']);
                    })->get(),
                    'tenants' => User::all(),
                    default => User::all(), // 'all'
                };

                if ($users->isNotEmpty()) {
                    Notification::send($users, new AnnouncementNotification($announcement));
                }

                tenancy()->end();
            } catch (\Throwable $e) {
                Log::error("BroadcastAnnouncementJob: Failed for tenant {$tenant->id}: {$e->getMessage()}");
                // Ensure tenancy is ended even on failure
                try { tenancy()->end(); } catch (\Throwable $ignored) {}
                continue; // Don't let one failing tenant block others
            }
        }

        Log::info("BroadcastAnnouncementJob: Announcement #{$this->announcementId} dispatched to {$tenants->count()} tenants.");
    }
}
