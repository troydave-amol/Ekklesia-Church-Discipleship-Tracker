<?php

namespace App\Http\Controllers;

use App\Services\ReportExportService;
use App\Models\User;
use App\Models\Program;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function __construct(private ReportExportService $reportService) {}

    public function index()
    {
        $programs = Program::all();
        $leaders  = User::where('church_id', auth()->user()->church_id)
            ->whereIn('role', ['program_leader', 'cell_leader', 'leader'])
            ->get();

        return view('reports.index', compact('programs', 'leaders'));
    }

    public function export(Request $request)
    {
        $request->validate([
            'type'       => ['required', 'in:attendance,devotional'],
            'from'       => ['required', 'date'],
            'to'         => ['required', 'date', 'after_or_equal:from'],
            'program_id' => ['nullable', 'exists:programs,id'],
            'leader_id'  => ['nullable', 'exists:users,id'],
        ]);

        $churchId = auth()->user()->church_id;

        return $request->type === 'attendance'
            ? $this->reportService->attendanceCsv($churchId, $request->from, $request->to, $request->program_id)
            : $this->reportService->devotionalCsv($churchId, $request->from, $request->to, $request->leader_id);
    }

    public function attendanceExport(Request $request)
    {
        $request->merge([
            'from' => $request->get('from', now()->subMonth()->toDateString()),
            'to'   => $request->get('to', now()->toDateString()),
        ]);

        $request->validate([
            'from'       => ['required', 'date'],
            'to'         => ['required', 'date', 'after_or_equal:from'],
            'program_id' => ['nullable', 'exists:programs,id'],
        ]);

        return $this->reportService->attendanceCsv(
            auth()->user()->church_id,
            $request->from,
            $request->to,
            $request->program_id ? (int) $request->program_id : null
        );
    }

    public function devotionalsExport(Request $request)
    {
        $request->merge([
            'from' => $request->get('from', now()->subMonth()->toDateString()),
            'to'   => $request->get('to', now()->toDateString()),
        ]);
        $request->validate([
            'from'      => ['required', 'date'],
            'to'        => ['required', 'date', 'after_or_equal:from'],
            'leader_id' => ['nullable', 'exists:users,id'],
        ]);

        return $this->reportService->devotionalCsv(
            auth()->user()->church_id,
            $request->from,
            $request->to,
            $request->leader_id ? (int) $request->leader_id : null
        );
    }

    public function programsExport(Request $request)
    {
        $request->merge([
            'from' => $request->get('from', now()->subMonths(3)->toDateString()),
            'to'   => $request->get('to', now()->toDateString()),
        ]);
        $request->validate([
            'from' => ['required', 'date'],
            'to'   => ['required', 'date', 'after_or_equal:from'],
        ]);

        return $this->reportService->programsSummaryCsv(
            auth()->user()->church_id,
            $request->from,
            $request->to
        );
    }

    public function historicalExport(Request $request)
    {
        $request->merge([
            'from' => $request->get('from', now()->subYear()->toDateString()),
            'to'   => $request->get('to', now()->toDateString()),
        ]);
        $request->validate([
            'from' => ['required', 'date'],
            'to'   => ['required', 'date', 'after_or_equal:from'],
        ]);

        return $this->reportService->historicalAttendanceCsv(
            auth()->user()->church_id,
            $request->from,
            $request->to
        );
    }
}
