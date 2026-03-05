<?php

namespace App\Http\Controllers;

use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    // Base controller for the application.  Actual controllers live in their own files.
}


// ════════════════════════════════════════════════
// app/Http/Controllers/ChurchController.php
// ════════════════════════════════════════════════
namespace App\Http\Controllers;

use App\Services\SubscriptionService;
use Illuminate\Http\Request;

class ChurchController extends Controller
{
    public function subscription()
    {
        $church  = auth()->user()->church;
        $service = new SubscriptionService($church);

        return view('church.subscription', [
            'church'  => $church,
            'usage'   => $service->usageSummary(),
        ]);
    }

    public function updatePlan(Request $request)
    {
        $request->validate(['plan' => ['required', 'in:free,basic,standard,premium']]);
        auth()->user()->church->update(['subscription_plan' => $request->plan]);
        return redirect()->route('church.subscription')->with('success', 'Plan updated.');
    }
}


// ════════════════════════════════════════════════
// app/Http/Controllers/DiscipleController.php
// User/member management (pastor only)
// ════════════════════════════════════════════════
namespace App\Http\Controllers;

use App\Http\Requests\UpdateDiscipleRequest;
use App\Services\ProgramEnrollmentService;
use App\Services\SubscriptionService;
use App\Models\User;
use Illuminate\Http\Request;

class DiscipleController extends Controller
{
    public function __construct(private ProgramEnrollmentService $enrollmentService) {}

    public function index(Request $request)
    {
        $churchId = auth()->user()->church_id;

        $members = User::where('church_id', $churchId)
            ->with(['membership.leader', 'profile'])
            ->when($request->role,   fn($q) => $q->where('role', $request->role))
            ->when($request->search, fn($q) => $q->where(fn($s) =>
                $s->where('first_name', 'like', "%{$request->search}%")
                  ->orWhere('last_name',  'like', "%{$request->search}%")
                  ->orWhere('email',      'like', "%{$request->search}%")
            ))
            ->orderBy('first_name')
            ->paginate(20);

        $leaders = User::where('church_id', $churchId)->where('role', 'leader')->get();

        return view('disciples.index', compact('members', 'leaders'));
    }

    public function create()
    {
        $leaders = User::where('church_id', auth()->user()->church_id)->where('role', 'leader')->get();
        return view('disciples.form', compact('leaders'));
    }

    public function store(UpdateDiscipleRequest $request)
    {
        // Plan limit check (also done in middleware, this is defensive)
        $service = new SubscriptionService(auth()->user()->church);
        if (!$service->isAllowed('add_disciple')) {
            return back()->with('error', 'Disciple limit reached. Please upgrade your plan.');
        }

        $this->enrollmentService->enroll(auth()->user()->church_id, $request->validated());
        return redirect()->route('disciples.index')->with('success', 'Member added.');
    }

    public function show(int $id)
    {
        $user = User::where('id', $id)->where('church_id', auth()->user()->church_id)
            ->with(['profile', 'membership.leader'])->firstOrFail();
        return view('disciples.show', compact('user'));
    }

    public function edit(int $id)
    {
        $user    = User::where('id', $id)->where('church_id', auth()->user()->church_id)->firstOrFail();
        $leaders = User::where('church_id', auth()->user()->church_id)->where('role', 'leader')->get();
        return view('disciples.form', compact('user', 'leaders'));
    }

    public function update(UpdateDiscipleRequest $request, int $id)
    {
        $user = User::where('id', $id)->where('church_id', auth()->user()->church_id)->firstOrFail();
        $user->update($request->only('first_name','last_name','nickname','email'));
        $user->profile()->updateOrCreate(['user_id' => $user->id], $request->only(
            'mobile_number','gender','civil_status','date_of_birth','address','city','state','postal_code'
        ));
        $user->membership()->update($request->only(
            'leader_id','status','destiny_training_level','satellite','joined_at','baptized_at'
        ));
        return redirect()->route('disciples.index')->with('success', 'Member updated.');
    }

    public function destroy(int $id)
    {
        User::where('id', $id)->where('church_id', auth()->user()->church_id)->firstOrFail()->delete();
        return redirect()->route('disciples.index')->with('success', 'Member removed.');
    }

    public function import(Request $request)
    {
        $request->validate(['csv_file' => ['required', 'file', 'mimes:csv,txt']]);
        $result = $this->enrollmentService->bulkImport(
            auth()->user()->church_id,
            $request->file('csv_file')->getRealPath()
        );
        return redirect()->route('disciples.index')
            ->with('success', "Imported {$result['created']}, skipped {$result['skipped']}.");
    }
}


// ════════════════════════════════════════════════
// app/Http/Controllers/ProgramController.php
// ════════════════════════════════════════════════
namespace App\Http\Controllers;

use App\Models\Program;
use Illuminate\Http\Request;

class ProgramController extends Controller
{
    public function index()
    {
        // ChurchTenantScope auto-filters by church_id
        $programs = Program::with(['lessons'])->withCount('lessons')->get();
        return view('programs.index', compact('programs'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'        => ['required', 'string', 'max:100'],
            'program_key' => ['required', 'in:life_class,sol1,sol2,sol3'],
            'description' => ['nullable', 'string'],
        ]);
        Program::create(array_merge($request->validated(), ['church_id' => auth()->user()->church_id]));
        return redirect()->route('programs.index')->with('success', 'Program created.');
    }

    public function update(Request $request, int $id)
    {
        $program = Program::findOrFail($id); // scope already applied
        $program->update($request->only('name','description','active'));
        return redirect()->route('programs.index')->with('success', 'Program updated.');
    }

    public function destroy(int $id)
    {
        Program::findOrFail($id)->delete();
        return redirect()->route('programs.index')->with('success', 'Program deleted.');
    }
}


// ════════════════════════════════════════════════
// app/Http/Controllers/LessonController.php
// ════════════════════════════════════════════════
namespace App\Http\Controllers;

use App\Http\Requests\StoreLessonRequest;
use App\Models\Lesson;
use App\Models\Program;

class LessonController extends Controller
{
    public function index()
    {
        // Scope auto-applied
        $lessonsByProgram = Lesson::with('program')->orderBy('week_number')->get()->groupBy('program.name');
        $programs = Program::all();
        return view('lessons.index', compact('lessonsByProgram', 'programs'));
    }

    public function myLessons()
    {
        $level = auth()->user()->membership->destiny_training_level ?? null;
        $lessons = Lesson::when($level, fn($q) =>
            $q->whereHas('program', fn($p) => $p->where('program_key', $level))
        )->with(['attendance' => fn($q) => $q->where('user_id', auth()->id())])
         ->orderBy('week_number')->get();

        return view('lessons.my', compact('lessons'));
    }

    public function store(StoreLessonRequest $request)
    {
        Lesson::create(array_merge($request->validated(), ['church_id' => auth()->user()->church_id]));
        return redirect()->route('lessons.index')->with('success', 'Lesson added.');
    }

    public function update(StoreLessonRequest $request, int $id)
    {
        Lesson::findOrFail($id)->update($request->validated());
        return redirect()->route('lessons.index')->with('success', 'Lesson updated.');
    }

    public function destroy(int $id)
    {
        Lesson::findOrFail($id)->delete();
        return redirect()->route('lessons.index')->with('success', 'Lesson deleted.');
    }
}


// ════════════════════════════════════════════════
// app/Http/Controllers/AttendanceController.php
// ════════════════════════════════════════════════
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
        $disciples = User::where('church_id', auth()->user()->church_id)->where('role', 'disciple')
            ->when(auth()->user()->isLeader(), fn($q) =>
                $q->whereHas('membership', fn($m) => $m->where('leader_id', auth()->id()))
            )->get();

        $lessonId          = $request->query('lesson_id');
        $attendanceRecords = $lessonId
            ? Attendance::where('lesson_id', $lessonId)->get()->keyBy('user_id')
            : collect();

        return view('attendance.index', compact('lessons', 'disciples', 'attendanceRecords'));
    }

    public function store(StoreAttendanceRequest $request)
    {
        $this->attendanceService->saveSheet(
            churchId:   auth()->user()->church_id,
            lessonId:   (int) $request->lesson_id,
            date:       $request->date,
            records:    $request->records,
            recordedBy: auth()->id(),
        );
        return redirect()->route('attendance.index', ['lesson_id' => $request->lesson_id])
            ->with('success', 'Attendance saved.');
    }
}


// ════════════════════════════════════════════════
// app/Http/Controllers/DevotionalController.php
// ════════════════════════════════════════════════
namespace App\Http\Controllers;

use App\Http\Requests\StoreDevotionalRequest;
use App\Services\DevotionalService;
use App\Models\User;
use Illuminate\Http\Request;

class DevotionalController extends Controller
{
    public function __construct(private DevotionalService $devotionalService) {}

    public function index(Request $request)
    {
        $churchId = auth()->user()->church_id;
        [$year, $month] = $this->parseMonth($request->query('month'));

        $disciples = User::where('church_id', $churchId)->where('role', 'disciple')
            ->when(auth()->user()->isLeader(), fn($q) =>
                $q->whereHas('membership', fn($m) => $m->where('leader_id', auth()->id()))
            )->get();

        $devotionals = $this->devotionalService->monthMap($churchId, $year, $month);

        return view('devotionals.index', compact('disciples', 'devotionals'));
    }

    public function markComplete(StoreDevotionalRequest $request)
    {
        $devotional = $this->devotionalService->toggle(
            actorId:      auth()->id(),
            targetUserId: (int) $request->user_id,
            churchId:     auth()->user()->church_id,
            date:         $request->date,
            completed:    (bool) $request->completed,
        );

        return response()->json(['success' => true, 'completed' => $devotional->completed]);
    }

    private function parseMonth(?string $month): array
    {
        if ($month && preg_match('/^\d{4}-\d{2}$/', $month)) {
            return array_map('intval', explode('-', $month));
        }
        return [(int) now()->format('Y'), (int) now()->format('m')];
    }
}


// ════════════════════════════════════════════════
// app/Http/Controllers/ReportController.php
// ════════════════════════════════════════════════
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
