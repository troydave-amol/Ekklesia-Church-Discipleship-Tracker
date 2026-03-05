<?php

namespace App\Services;

use App\Models\Devotional;
use App\Models\User;

class DevotionalService
{
    /**
     * Toggle a devotional entry. Leader can toggle for their disciples.
     * Disciple can only mark their own.
     */
    public function toggle(int $actorId, int $targetUserId, int $churchId, string $date, bool $completed): Devotional
    {
        $actor = User::findOrFail($actorId);

        // Leader authorization
        if ($actor->isLeader()) {
            $isAssigned = $actor->assignedDisciples()
                ->where('user_id', $targetUserId)
                ->exists();
            if (!$isAssigned) {
                abort(403, 'This disciple is not assigned to you.');
            }
        }

        // Disciple self-only
        if ($actor->isDisciple() && $actor->id !== $targetUserId) {
            abort(403, 'You can only update your own devotionals.');
        }

        return Devotional::withoutGlobalScopes()->updateOrCreate(
            ['church_id' => $churchId, 'user_id' => $targetUserId, 'date' => $date],
            [
                'completed'   => $completed,
                'verified_by' => $actor->isLeader() ? $actorId : null,
                'verified_at' => $actor->isLeader() ? now() : null,
            ]
        );
    }

    /**
     * Get devotional heatmap for a month, keyed by [user_id][day].
     */
    public function monthMap(int $churchId, int $year, int $month): array
    {
        $records = Devotional::withoutGlobalScopes()
            ->where('church_id', $churchId)
            ->whereYear('date', $year)
            ->whereMonth('date', $month)
            ->where('completed', true)
            ->get();

        $map = [];
        foreach ($records as $r) {
            $map[$r->user_id][(int) $r->date->format('j')] = true;
        }
        return $map;
    }

    /**
     * Current streak for a user (consecutive days back from today).
     */
    public function streakForUser(int $userId, int $churchId): int
    {
        $streak = 0;
        $date   = now();
        while (true) {
            $exists = Devotional::withoutGlobalScopes()
                ->where('church_id', $churchId)
                ->where('user_id', $userId)
                ->where('date', $date->toDateString())
                ->where('completed', true)
                ->exists();
            if (!$exists) break;
            $streak++;
            $date->subDay();
        }
        return $streak;
    }

    /**
     * Devotional completion rate (%) for this month.
     */
    public function monthlyRate(int $userId, int $churchId): int
    {
        $daysPassed = now()->day;
        if ($daysPassed === 0) return 0;
        $completed = Devotional::withoutGlobalScopes()
            ->where('church_id', $churchId)
            ->where('user_id', $userId)
            ->whereYear('date', now()->year)
            ->whereMonth('date', now()->month)
            ->where('completed', true)
            ->count();
        return (int) round($completed / $daysPassed * 100);
    }
}