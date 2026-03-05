<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Lesson;

class AttendancePolicy
{
    /**
     * Pastor can record attendance for any lesson in their church.
     * Leader can only record for lessons in their assigned disciples' programs.
     */
    public function record(User $actor, Lesson $lesson): bool
    {
        if ($actor->isPastor()) {
            return $actor->church_id === $lesson->church_id;
        }

        if ($actor->isLeader()) {
            // Leader must have at least one disciple in this program
            return $actor->assignedDisciples()
                ->whereHas('user.membership', fn($q) =>
                    $q->where('destiny_training_level', $lesson->program->program_key ?? '')
                )
                ->exists();
        }

        return false;
    }
}