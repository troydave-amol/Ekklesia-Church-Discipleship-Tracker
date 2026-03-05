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
        $programs = Program::all(); // scope applied
        $leaders  = User::where('church_id', auth()->user()->church_id)->where('role', 'leader')->get();
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
}
