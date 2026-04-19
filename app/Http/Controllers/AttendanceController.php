<?php

namespace App\Http\Controllers;

use App\Services\AttendanceService;
use App\Models\Attendance;
use App\Models\Lesson;
use App\Models\Program;
use App\Models\User;
use Illuminate\Http\Request;

class AttendanceController extends Controller
{
    public function __construct(private AttendanceService $attendanceService) {}

    public function index(Request $request)
    {
        $churchId = auth()->user()->church_id;

        // Programs for filter dropdown
        $programs = Program::where('church_id', $churchId)
            ->where('active', true)
            ->get();

        // Lessons — optionally filtered by selected program
        $lessons = Lesson::with('program')
            ->where('church_id', $churchId)
            ->when($request->query('program_id'), fn ($q, $pid) => $q->where('program_id', $pid))
            ->orderBy('week_number')
            ->get();

        $selectedLesson        = null;
        $attendanceDisciples   = collect();
        $recentAttendance      = $this->attendanceService->recentHistory($churchId);

        if ($request->query('lesson_id')) {
            $selectedLesson = Lesson::with('program')
                ->where('church_id', $churchId)
                ->find($request->query('lesson_id'));

            if ($selectedLesson) {
                $date = $request->query('date', today()->toDateString());

                // All disciples visible to the current user
                $disciples = User::where('church_id', $churchId)
                    ->where('role', 'disciple')
                    ->when(auth()->user()->isRestrictedLeader(), fn ($q) =>
                        $q->whereHas('membership', fn ($m) => $m->where('leader_id', auth()->id())))
                    ->with(['membership.leader'])
                    ->get();

                // Existing attendance records for this lesson+date
                $existingRecords = Attendance::withoutGlobalScopes()
                    ->where('church_id', $churchId)
                    ->where('lesson_id', $selectedLesson->id)
                    ->where('date', $date)
                    ->get()
                    ->keyBy('user_id');

                // Enrich each disciple with attendance info
                $attendanceDisciples = $disciples->map(function ($d) use ($existingRecords, $churchId) {
                    $record = $existingRecords[$d->id] ?? null;
                    $d->is_present           = $record ? ($record->status === 'present') : false;
                    $d->attendance_rate       = $this->attendanceService->rateForUser($d->id, $churchId);
                    $d->last_attendance_date  = Attendance::withoutGlobalScopes()
                        ->where('church_id', $churchId)
                        ->where('user_id', $d->id)
                        ->where('status', 'present')
                        ->orderByDesc('date')
                        ->value('date');
                    return $d;
                });
            }
        }

        return view('attendance.index', compact(
            'programs',
            'lessons',
            'selectedLesson',
            'attendanceDisciples',
            'recentAttendance',
        ));
    }

    public function store(Request $request)
    {
        $request->validate([
            'lesson_id' => ['required', 'exists:lessons,id'],
            'date'      => ['required', 'date', 'before_or_equal:today'],
            'attendance' => ['required', 'array'],
        ]);

        $churchId = auth()->user()->church_id;

        // Policy: verify the user can record attendance for this lesson
        $lesson = Lesson::where('church_id', $churchId)->findOrFail($request->lesson_id);
        $this->authorize('record', $lesson);

        // Map the checkbox array (attendance[user_id] = 1|0) to the format saveSheet expects
        $records = [];
        foreach ($request->input('attendance', []) as $userId => $value) {
            $records[] = [
                'user_id' => (int) $userId,
                'status'  => ($value == 1) ? 'present' : 'absent',
            ];
        }

        $this->attendanceService->saveSheet(
            churchId:   $churchId,
            lessonId:   (int) $request->lesson_id,
            date:       $request->date,
            records:    $records,
            recordedBy: auth()->id(),
        );

        return redirect()->route('attendance.index', [
            'lesson_id'  => $request->lesson_id,
            'date'       => $request->date,
            'program_id' => $request->program_id,
        ])->with('success', 'Attendance saved.');
    }
}
