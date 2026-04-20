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
        $data = $request->validate([
            'name'        => ['required', 'string', 'max:100'],
            'program_key' => ['required', 'in:life_class,sol1,sol2,sol3'],
            'description' => ['nullable', 'string'],
        ]);

        Program::create(array_merge(
            $data,
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

    /**
     * Enroll the authenticated disciple into a program.
     */
    public function enroll(Request $request, int $id)
    {
        $user = $request->user();

        if (! $user->isDisciple()) {
            abort(403);
        }

        $program = Program::where('church_id', $user->church_id)->findOrFail($id);

        $exists = \App\Models\ChurchMembership::where('user_id', $user->id)
            ->where('destiny_training_level', $program->program_key)
            ->exists();

        if ($exists) {
            return response()->json(['message' => 'You are already enrolled in this program.'], 422);
        }

        // Create a membership/enrollment record; uses ChurchMembership model
        \App\Models\ChurchMembership::create([
            'church_id' => $user->church_id,
            'user_id' => $user->id,
            'leader_id' => null,
            'destiny_training_level' => $program->program_key,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect()->route('programs.show', $program)->with('success', 'Enrolled successfully.');
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