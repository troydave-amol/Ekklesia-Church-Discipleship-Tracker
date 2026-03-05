<?php

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
        Program::create(array_merge($request->validated(), ['church_id' => auth()->$this->user()->church_id]));
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
