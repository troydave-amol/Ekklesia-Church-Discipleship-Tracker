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
        Lesson::create(array_merge($request->validated(), ['church_id' => $request->user()->church_id]));

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
