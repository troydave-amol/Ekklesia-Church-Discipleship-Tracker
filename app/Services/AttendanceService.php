<?php

namespace App\Services;

use App\Models\Attendance;
use App\QueryBuilders\AttendanceQueryBuilder;

class AttendanceService
{
    public function weeklyStats(int $churchId): array
    {
        return Attendance::where('church_id', $churchId)
            ->where('date', '>=', now()->subWeeks(8))
            ->selectRaw('YEARWEEK(date, 1) as week, COUNT(DISTINCT user_id) as count')
            ->groupBy('week')
            ->orderBy('week')
            ->pluck('count', 'week')
            ->toArray();
    }

    public function rateForUser(int $userId, int $churchId): int
    {
        $total = Attendance::where('church_id', $churchId)->count();

        if ($total === 0) return 0;

        $attended = Attendance::where('church_id', $churchId)
            ->where('user_id', $userId)
            ->count();

        return (int) round(($attended / $total) * 100);
    }

    public function countForUser(int $userId, int $churchId): int
    {
        return Attendance::where('church_id', $churchId)
            ->where('user_id', $userId)
            ->count();
    }

    public function totalSessions(int $churchId): int
    {
        return Attendance::where('church_id', $churchId)
            ->distinct('date')
            ->count('date');
    }

    public function churchAvgAttendance(int $churchId): int
    {
        $users = \App\Models\User::where('church_id', $churchId)
            ->where('role', 'disciple')
            ->pluck('id');

        if ($users->isEmpty()) return 0;

        $rates = $users->map(fn($id) => $this->rateForUser($id, $churchId));

        return (int) round($rates->avg());
    }

    public function __construct(protected AttendanceQueryBuilder $queryBuilder) {}

    public function list(int $churchId): \Illuminate\Pagination\LengthAwarePaginator
    {
        return $this->queryBuilder->forChurch($churchId)->latest()->paginate(20);
    }

    public function create(int $churchId, array $data): Attendance
    {
        return Attendance::create(array_merge($data, ['church_id' => $churchId]));
    }

    public function delete(int $churchId, int $id): void
    {
        Attendance::where('church_id', $churchId)->findOrFail($id)->delete();
    }
}