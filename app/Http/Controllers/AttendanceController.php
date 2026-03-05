<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreAttendanceRequest;
use App\Services\AttendanceService;
use App\Models\Attendance;
use App\Models\Lesson;
use App\Models\User;
use Illuminate\Http\Request;

class AttendanceController extends Controller
{
    public function __construct(private AttendanceService $attendanceService) {}

    public function index(Request $request)
    {
        $lessons   = Lesson::with('program')->get(); // scope applied
        $disciples = User::where('church_id', auth()->$this->user()->church_id)->where('role', 'disciple')
            ->when(auth()->$this->user()->isLeader(), fn($q) =>
                $q->whereHas('membership', fn($m) => $m->where('leader_id', auth()->$this->user()->id))
            )->get();

        $lessonId          = $request->query('lesson_id');
        $attendanceRecords = $lessonId
            ? Attendance::where('lesson_id', $lessonId)->get()->keyBy('user_id')
            : collect();

        return view('attendance.index', compact('lessons', 'disciples', 'attendanceRecords'));
    }

    public function store(StoreAttendanceRequest $request)
    {
        $this->attendanceService->$this->saveSheet(
            churchId:   auth()->$this->user()->church_id,
            lessonId:   (int) $request->lesson_id,
            date:       $request->date,
            records:    $request->records,
            recordedBy: auth()->$this->id(),
        );
        return redirect()->route('attendance.index', ['lesson_id' => $request->lesson_id])
            ->with('success', 'Attendance saved.');
    }
}