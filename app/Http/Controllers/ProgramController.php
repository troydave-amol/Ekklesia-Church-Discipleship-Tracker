<?php

namespace App\Http\Controllers;

use App\Models\Program;
use Illuminate\Http\Request;

class ProgramController extends Controller
{
    public function index()
    {
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

        Program::create(array_merge(
            $request->validated(),
            ['church_id' => $request->user()->church_id ?? null]
        ));

        return redirect()->route('programs.index')->with('success', 'Program created.');
    }

    public function update(Request $request, int $id)
    {
        $program = Program::findOrFail($id);
        $program->update($request->only('name', 'description', 'active'));
        return redirect()->route('programs.index')->with('success', 'Program updated.');
    }

    public function destroy(int $id)
    {
        Program::findOrFail($id)->delete();
        return redirect()->route('programs.index')->with('success', 'Program deleted.');
    }

    public function toggle(Program $program)
    {
        $program->update(['active' => !$program->active]);
        return back()->with('success', 'Program status updated.');
    }

    public function edit(Program $program)
    {
        return view('programs.edit', compact('program'));
    }
}