<?php

namespace App\Http\Controllers\Church;

use App\Http\Controllers\Controller;
use App\Http\Requests\Church\StoreAttendanceRequest;
use App\Services\AttendanceService;
use Illuminate\Http\Request;

class AttendanceController extends Controller
{
    public function __construct(protected AttendanceService $attendanceService) {}

    public function index(Request $request)
    {
        $records = $this->attendanceService->list($request->user()->church_id);
        return view('church.attendance.index', compact('records'));
    }

    public function store(StoreAttendanceRequest $request)
    {
        $this->attendanceService->create($request->user()->church_id, $request->validated());
        return redirect()->route('church.attendance.index')->with('success', 'Attendance recorded.');
    }

    public function destroy(int $id, Request $request)
    {
        $this->attendanceService->delete($request->user()->church_id, $id);
        return redirect()->route('church.attendance.index')->with('success', 'Deleted.');
    }
}