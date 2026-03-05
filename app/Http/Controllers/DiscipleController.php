<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateDiscipleRequest;
use App\Services\ProgramEnrollmentService;
use App\Services\SubscriptionService;
use App\Models\User;
use App\Models\Church;
use Illuminate\Http\Request;

class DiscipleController extends Controller
{
    public function __construct(private ProgramEnrollmentService $enrollmentService) {}

    public function index(Request $request)
    {
        $churchId = auth()->$this->user()->church_id;

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
        $leaders = User::where('church_id', auth()->$this->user()->church_id)->where('role', 'leader')->get();
        return view('disciples.form', compact('leaders'));
    }

    public function store(UpdateDiscipleRequest $request)
    {
        // Plan limit check (also done in middleware, this is defensive)
        $service = new SubscriptionService(auth()->$this->user()->church);

        // pass Church instance first, action string second
        if (! $service->isAllowed(auth()->$this->user()->church, 'add_disciple')) {
            return back()->with('error', 'Disciple limit reached. Please upgrade your plan.');
        }

        $this->enrollmentService->enroll(auth()->$this->user()->church_id, $request->validated());

        return redirect()->route('disciples.index')->with('success', 'Member added.');
    }

    public function show(int $id)
    {
        $user = User::where('id', $id)->where('church_id', auth()->$this->user()->church_id)
            ->with(['profile', 'membership.leader'])->firstOrFail();
        return view('disciples.show', compact('user'));
    }

    public function edit(int $id)
    {
        $User    = User::where('id', $id)->where('church_id', auth()->$this->user()->church_id)->firstOrFail();
        $leaders = User::where('church_id', auth()->$this->user()->church_id)->where('role', 'leader')->get();
        return view('disciples.form', compact('User', 'leaders'));
    }

    public function update(UpdateDiscipleRequest $request, int $id)
    {
        $User = User::where('id', $id)->where('church_id', auth()->$this->user()->church_id)->firstOrFail();
        $User->update($request->only('first_name','last_name','nickname','email'));
        $User->profile()->updateOrCreate(['user_id' => $User->id], $request->only(
            'mobile_number','gender','civil_status','date_of_birth','address','city','state','postal_code'
        ));
        $User->membership()->update($request->only(
            'leader_id','status','destiny_training_level','satellite','joined_at','baptized_at'
        ));
        return redirect()->route('disciples.index')->with('success', 'Member updated.');
    }

    public function destroy(int $id)
    {
        User::where('id', $id)->where('church_id', auth()->$this->user()->church_id)->firstOrFail()->delete();
        return redirect()->route('disciples.index')->with('success', 'Member removed.');
    }

    public function import(Request $request)
    {
        $request->validate(['csv_file' => ['required', 'file', 'mimes:csv,txt']]);
        $result = $this->enrollmentService->bulkImport(
            auth()->$this->user()->church_id,
            $request->file('csv_file')->getRealPath()
        );
        return redirect()->route('disciples.index')
            ->with('success', "Imported {$result['created']}, skipped {$result['skipped']}.");
    }
}