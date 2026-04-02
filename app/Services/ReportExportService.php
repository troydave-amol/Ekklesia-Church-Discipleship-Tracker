<?php

namespace App\Services;

use App\Models\Attendance;
use App\Models\Devotional;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReportExportService
{
    /**
     * Stream attendance CSV.
     */
    public function attendanceCsv(int $churchId, string $from, string $to, ?int $programId = null): StreamedResponse
    {
        $rows = Attendance::withoutGlobalScopes()
            ->join('users',    'users.id',    '=', 'attendance.user_id')
            ->join('lessons',  'lessons.id',  '=', 'attendance.lesson_id')
            ->join('programs', 'programs.id', '=', 'lessons.program_id')
            ->where('attendance.church_id', $churchId)
            ->when($programId, fn($q) => $q->where('programs.id', $programId))
            ->whereBetween('attendance.date', [$from, $to])
            ->select('users.first_name', 'users.last_name', 'users.email',
                     'programs.name as program', 'lessons.title', 'lessons.week_number',
                     'attendance.date', 'attendance.status')
            ->orderBy('attendance.date')
            ->get();

        return response()->streamDownload(function () use ($rows) {
            $h = fopen('php://output', 'w');
            fputcsv($h, ['First Name','Last Name','Email','Program','Lesson','Week','Date','Status']);
            foreach ($rows as $r) {
                fputcsv($h, [$r->first_name, $r->last_name, $r->email,
                             $r->program, $r->title, $r->week_number, $r->date, $r->status]);
            }
            fclose($h);
        }, 'attendance-' . now()->format('Y-m-d') . '.csv');
    }

    /**
     * Stream devotional CSV.
     */
    public function devotionalCsv(int $churchId, string $from, string $to, ?int $leaderId = null): StreamedResponse
    {
        $rows = Devotional::withoutGlobalScopes()
            ->join('users', 'users.id', '=', 'devotionals.user_id')
            ->join('church_membership', 'church_membership.user_id', '=', 'devotionals.user_id')
            ->where('devotionals.church_id', $churchId)
            ->when($leaderId, fn ($q) => $q->where('church_membership.leader_id', $leaderId))
            ->whereBetween('devotionals.date', [$from, $to])
            ->select('users.first_name', 'users.last_name', 'users.email',
                     'devotionals.date', 'devotionals.completed')
            ->orderBy('devotionals.date')
            ->get();

        return response()->streamDownload(function () use ($rows) {
            $h = fopen('php://output', 'w');
            fputcsv($h, ['First Name','Last Name','Email','Date','Completed']);
            foreach ($rows as $r) {
                fputcsv($h, [$r->first_name, $r->last_name, $r->email,
                             $r->date, $r->completed ? 'Yes' : 'No']);
            }
            fclose($h);
        }, 'devotionals-' . now()->format('Y-m-d') . '.csv');
    }

    public function programsSummaryCsv(int $churchId, string $from, string $to): StreamedResponse
    {
        $rows = Attendance::withoutGlobalScopes()
            ->join('lessons', 'lessons.id', '=', 'attendance.lesson_id')
            ->join('programs', 'programs.id', '=', 'lessons.program_id')
            ->where('attendance.church_id', $churchId)
            ->whereBetween('attendance.date', [$from, $to])
            ->selectRaw(
                'programs.name as program, COUNT(*) as sessions, SUM(CASE WHEN attendance.status = ? THEN 1 ELSE 0 END) as present_count',
                ['present']
            )
            ->groupBy('programs.id', 'programs.name')
            ->orderBy('programs.name')
            ->get();

        return response()->streamDownload(function () use ($rows) {
            $h = fopen('php://output', 'w');
            fputcsv($h, ['Program', 'Records', 'Present']);
            foreach ($rows as $r) {
                fputcsv($h, [$r->program, $r->sessions, $r->present_count]);
            }
            fclose($h);
        }, 'programs-summary-' . now()->format('Y-m-d') . '.csv');
    }

    public function historicalAttendanceCsv(int $churchId, string $from, string $to): StreamedResponse
    {
        return $this->attendanceCsv($churchId, $from, $to, null);
    }
}
