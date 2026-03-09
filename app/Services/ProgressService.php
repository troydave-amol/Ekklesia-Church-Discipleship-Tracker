<?php

namespace App\Services;

use App\Models\User;
use App\Models\Church;
use App\Models\Attendance;
use App\Models\Devotional;

class ProgressService
{
    public function __construct(
        private AttendanceService $attendanceService,
        private DevotionalService $devotionalService,
    ) {}

    /**
     * Pastor dashboard stats.
     */
    public function pastorStats(Church $church): array
    {
        $churchId = $church->id;

        $disciples = \App\Models\User::where('church_id', $churchId)
            ->where('role', 'disciple')
            ->with(['membership', 'membership.leader']) 
            ->paginate(20);

        $leaders = \App\Models\User::where('church_id', $churchId)
            ->where('role', 'leader')
            ->withCount('assignedDisciples')
            ->get();

        $programs = \App\Models\Program::where('church_id', $churchId)
            ->where('active', true)
            ->withCount('members')  
            ->get();

        return [
            'totalDisciples' => \App\Models\User::where('church_id', $churchId)->where('role', 'disciple')->count(),
            'newThisMonth'   => \App\Models\User::where('church_id', $churchId)->where('role', 'disciple')->whereMonth('created_at', now()->month)->count(),
            'activeLeaders'  => \App\Models\User::where('church_id', $churchId)->where('role', 'leader')->count(),
            'avgAttendance'  => $this->attendanceService->churchAvgAttendance($churchId),
            'devotionalRate' => 0, // placeholder until DevotionalService is ready
            'programs'       => $programs,
            'leaders'        => $leaders,
            'disciples'      => $disciples,
            'weeklyStats'    => $this->attendanceService->weeklyStats($churchId),
        ];
    }

    /**
     * Leader dashboard — disciples with their rates.
     */
    public function leaderStats(User $leader): array
    {
        $churchId  = $leader->church_id;
        $disciples = User::where('church_id', $churchId)
            ->where('role', 'disciple')
            ->whereHas('membership', fn($q) => $q->where('leader_id', $leader->id))
            ->with(['membership', 'profile'])
            ->get();

        foreach ($disciples as $d) {
            $d->attendanceRate = $this->attendanceService->rateForUser($d->id, $churchId);
            $d->devotionalRate = $this->devotionalService->monthlyRate($d->id, $churchId);
        }

        return [
            'disciples'      => $disciples,
            'myDisciples'    => $disciples->count(),
            'attendanceRate' => $disciples->count() ? (int)round($disciples->avg('attendanceRate')) : 0,
            'devotionalRate' => $disciples->count() ? (int)round($disciples->avg('devotionalRate')) : 0,
            'needsAttention' => $disciples->filter(fn($d) => $d->attendanceRate < 60 || $d->devotionalRate < 40)->count(),
        ];
    }

    /**
     * Disciple's own progress.
     */
    public function discipleStats(User $disciple): array
    {
        $churchId = $disciple->church_id;
        $level    = $disciple->membership->destiny_training_level ?? null;

        $myLessons = \App\Models\Lesson::where('church_id', $churchId)
            ->when($level, fn($q) => $q->whereHas('program', fn($p) => $p->where('program_key', $level)))
            ->with(['attendance' => fn($q) => $q->where('user_id', $disciple->id)])
            ->withoutGlobalScopes()
            ->orderBy('week_number')
            ->get();

        return [
            'attendanceRate'       => $this->attendanceService->rateForUser($disciple->id, $churchId),
            'devStreak'            => $this->devotionalService->streakForUser($disciple->id, $churchId),
            'devRate'              => $this->devotionalService->monthlyRate($disciple->id, $churchId),
            'lessonsCompleted'     => $myLessons->filter(fn($l) => $l->attendance->isNotEmpty())->count(),
            'totalLessons'         => $myLessons->count(),
            'myLessons'            => $myLessons,
            'devotionalsThisMonth' => $this->devotionalService->monthMap($churchId, now()->year, now()->month)[$disciple->id] ?? [],
        ];
    }
}
