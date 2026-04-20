<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreLessonRequest;
use App\Models\Lesson;
use App\Models\Program;
use Illuminate\Http\Request;

class LessonController extends Controller
{
    public function index(Request $request)
    {
        $programs = Program::orderBy('name')->get();

        $lessons = Lesson::query()
            ->with('program')
            ->when($request->filled('program_id'), fn ($q) => $q->where('program_id', $request->program_id))
            ->orderBy('week_number')
            ->paginate(24)
            ->withQueryString();

        $lessonsByProgram = Lesson::with('program')->orderBy('week_number')->get()->groupBy('program.name');

        return view('lessons.index', compact('lessonsByProgram', 'programs', 'lessons'));
    }

    public function create()
    {
        $programs = Program::orderBy('name')->get();

        return view('lessons.create', compact('programs'));
    }

    public function show(Lesson $lesson)
    {
        $lesson->load('program');

        return view('lessons.show', compact('lesson'));
    }

    public function edit(Lesson $lesson)
    {
        $programs = Program::orderBy('name')->get();

        return view('lessons.edit', compact('lesson', 'programs'));
    }

    public function myLessons(Request $request)
    {
        $user  = $request->user();
        $level = $user->membership->destiny_training_level ?? null;
        $levelVal = is_object($level) && property_exists($level, 'value') ? $level->value : $level;

        $lessons = Lesson::when($levelVal, fn ($q) =>
            $q->whereHas('program', fn ($p) => $p->where('program_key', $levelVal)))
            ->with(['attendance' => fn ($q) => $q->where('user_id', $user->id)])
            ->orderBy('week_number')
            ->get();

        return view('lessons.my', compact('lessons'));
    }

    public function store(StoreLessonRequest $request)
    {
        $user = $request->user();

        // If actor is a program leader, ensure they are assigned to the program
        if ($user->isLeader()) {
            $program = Program::where('id', $request->program_id)->first();
            if (! $program) {
                abort(404);
            }

            $assigned = \App\Models\ChurchMembership::where('leader_id', $user->id)
                ->where('destiny_training_level', $program->program_key)
                ->exists();

            if (! $assigned) {
                abort(403, 'You are not assigned to this program.');
            }
        }

        Lesson::create(array_merge($request->validated(), ['church_id' => $request->user()->church_id]));

        return redirect()->route('lessons.index')->with('success', 'Lesson added.');
    }

    public function update(StoreLessonRequest $request, int $id)
    {
        $lesson = Lesson::findOrFail($id);
        $user = $request->user();

        if ($user->isLeader()) {
            // Leader may only edit lessons they created. If the lessons table does not
            // have a creator column, deny edits by leaders to be safe.
            if (isset($lesson->user_id) && $lesson->user_id === $user->id) {
                $lesson->update($request->validated());
            } else {
                abort(403, 'You can only edit your own lessons.');
            }
        } else {
            $lesson->update($request->validated());
        }

        return redirect()->route('lessons.index')->with('success', 'Lesson updated.');
    }

    public function destroy(int $id)
    {
        $lesson = Lesson::findOrFail($id);
        $user = auth()->user();

        if ($user->isLeader()) {
            if (isset($lesson->user_id) && $lesson->user_id === $user->id) {
                $lesson->delete();
            } else {
                abort(403, 'You can only delete your own lessons.');
            }
        } else {
            $lesson->delete();
        }

        return redirect()->route('lessons.index')->with('success', 'Lesson deleted.');
    }
}
