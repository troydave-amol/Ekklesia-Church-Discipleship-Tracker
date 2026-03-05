<?php

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