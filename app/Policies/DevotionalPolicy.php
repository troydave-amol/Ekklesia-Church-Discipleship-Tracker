<?php

namespace App\Policies;

use App\Models\User;

class DevotionalPolicy
{
    /**
     * Can the actor mark/manage devotionals for the target disciple?
     *
     * - Pastor: any disciple in the same church
     * - Leader: only their assigned disciples
     * - Disciple: only themselves
     */
    public function manage(User $actor, User $targetDisciple): bool
    {
        // Must be same church
        if ($actor->church_id !== $targetDisciple->church_id) {
            return false;
        }

        // Pastor can manage all
        if ($actor->isChurchAdmin()) {
            return true;
        }

        // Leader can only manage assigned disciples
        if ($actor->isRestrictedLeader()) {
            return $targetDisciple->membership?->leader_id === $actor->id;
        }

        // Disciple can only mark their own
        return $actor->id === $targetDisciple->id;
    }

    /**
     * Can the actor mark their own devotional?
     */
    public function markSelf(User $actor): bool
    {
        return true; // Any authenticated user can mark their own devotional
    }
}
