<?php

namespace App\Policies;

use App\Models\User;

class DisciplePolicy
{
    /**
     * Pastor can manage all disciples in their church.
     * Leader can only manage their own assigned disciples.
     */
    public function view(User $actor, User $disciple): bool
    {
        if ($actor->isChurchAdmin()) {
            return $actor->church_id === $disciple->church_id;
        }

        if ($actor->isRestrictedLeader()) {
            return $disciple->membership?->leader_id === $actor->id;
        }

        // Disciple can view their own profile
        return $actor->id === $disciple->id;
    }

    public function update(User $actor, User $disciple): bool
    {
        if ($actor->isChurchAdmin()) {
            return $actor->church_id === $disciple->church_id;
        }

        if ($actor->isRestrictedLeader()) {
            return $disciple->membership?->leader_id === $actor->id;
        }

        return false;
    }

    public function delete(User $actor, User $disciple): bool
    {
        return $actor->isChurchAdmin() && $actor->church_id === $disciple->church_id;
    }
}

