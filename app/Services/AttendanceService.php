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

    /**
     * @param  list<array{user_id: int, status: string}>  $records
     */
    public function saveSheet(int $churchId, int $lessonId, string $date, array $records, int $recordedBy): void
    {
        foreach ($records as $row) {
            Attendance::withoutGlobalScopes()->updateOrCreate(
                [
                    'church_id' => $churchId,
                    'user_id'   => (int) $row['user_id'],
                    'lesson_id' => $lessonId,
                    'date'      => $date,
                ],
                [
                    'status'       => $row['status'],
                    'recorded_by'  => $recordedBy,
                ]
            );
        }
    }

    /**
     * Recent attendance history grouped by lesson+date, with present/absent/total counts.
     */
    public function recentHistory(int $churchId, int $limit = 10): \Illuminate\Support\Collection
    {
        $rows = Attendance::withoutGlobalScopes()
            ->where('church_id', $churchId)
            ->selectRaw('lesson_id, date, COUNT(*) as total_count, SUM(CASE WHEN status = \'present\' THEN 1 ELSE 0 END) as present_count')
            ->groupBy('lesson_id', 'date')
            ->orderByDesc('date')
            ->limit($limit)
            ->get();

        // Eager-load lesson + program for display
        $lessonIds = $rows->pluck('lesson_id')->unique();
        $lessons = \App\Models\Lesson::withoutGlobalScopes()
            ->whereIn('id', $lessonIds)
            ->with('program')
            ->get()
            ->keyBy('id');

        return $rows->map(function ($row) use ($lessons) {
            $row->lesson        = $lessons[$row->lesson_id] ?? null;
            $row->absent_count  = $row->total_count - $row->present_count;
            $row->date          = \Carbon\Carbon::parse($row->date);
            return $row;
        });
    }
}