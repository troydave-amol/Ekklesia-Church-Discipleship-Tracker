<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateDiscipleRequest;
use App\Notifications\DiscipleAssigned;
use App\Services\AttendanceService;
use App\Services\DevotionalService;
use App\Services\ProgramEnrollmentService;
use App\Services\SubscriptionService;
use App\Models\Program;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class DiscipleController extends Controller
{
    public function __construct(
        private ProgramEnrollmentService $enrollmentService,
        private AttendanceService $attendanceService,
        private DevotionalService $devotionalService,
    ) {}

    public function index(Request $request)
    {
        return view('users.index', $this->indexData($request, false));
    }

    public function leaderDisciples(Request $request)
    {
        if (auth()->user()->isChurchAdmin()) {
            return redirect()->route('users.index');
        }

        $request->merge(['leader_id' => auth()->id()]);

        return view('users.index', $this->indexData($request, true));
    }

    /**
     * @return array<string, mixed>
     */
    private function indexData(Request $request, bool $leaderScope): array
    {
        $churchId = auth()->user()->church_id;

        $programs = Program::orderBy('name')->get();

        $disciples = User::query()
            ->where('church_id', $churchId)
            ->where('role', 'disciple')
            ->with(['membership.leader', 'profile'])
            ->when($request->filled('q'), fn ($q) => $q->where(function ($s) use ($request) {
                $term = $request->q;
                $s->where('first_name', 'like', "%{$term}%")
                    ->orWhere('last_name', 'like', "%{$term}%")
                    ->orWhere('email', 'like', "%{$term}%");
            }))
            ->when($request->filled('program') || $request->filled('program_id'), function ($q) use ($request) {
                $programId = $request->program ?? $request->program_id;
                $program   = Program::find($programId);
                if ($program?->program_key !== null) {
                    $key = $program->program_key;
                    $keyVal = is_object($key) && property_exists($key, 'value') ? $key->value : (string) $key;
                    $q->whereHas('membership', fn ($m) => $m->where('destiny_training_level', $keyVal));
                }
            })
            ->when($request->filled('leader_id'), fn ($q) => $q->whereHas('membership', fn ($m) => $m->where('leader_id', $request->leader_id)))
            ->when($request->filled('status'), fn ($q) => $q->whereHas('membership', fn ($m) => $m->where('status', $request->status)))
            ->orderBy('first_name')
            ->paginate(20);

        foreach ($disciples as $d) {
            $d->attendance_rate = $this->attendanceService->rateForUser($d->id, $churchId);
            $d->devotional_rate = $this->devotionalService->monthlyRate($d->id, $churchId);
        }

        $totalDisciples = User::where('church_id', $churchId)->where('role', 'disciple')->count();

        $leaders = User::where('church_id', $churchId)
            ->whereIn('role', ['program_leader', 'cell_leader', 'leader'])
            ->withCount('assignedDisciples')
            ->orderBy('first_name')
            ->get();

        foreach ($leaders as $leader) {
            $rates = User::where('church_id', $churchId)
                ->where('role', 'disciple')
                ->whereHas('membership', fn ($m) => $m->where('leader_id', $leader->id))
                ->pluck('id')
                ->map(fn ($id) => $this->attendanceService->rateForUser((int) $id, $churchId));
            $leader->attendance_rate = $rates->isNotEmpty() ? (int) round($rates->avg()) : 0;

            $drates = User::where('church_id', $churchId)
                ->where('role', 'disciple')
                ->whereHas('membership', fn ($m) => $m->where('leader_id', $leader->id))
                ->pluck('id')
                ->map(fn ($id) => $this->devotionalService->monthlyRate((int) $id, $churchId));
            $leader->devotional_rate = $drates->isNotEmpty() ? (int) round($drates->avg()) : 0;
        }

        $totalLeaders = $leaders->count();

        return compact(
            'disciples',
            'leaders',
            'programs',
            'totalDisciples',
            'totalLeaders',
            'leaderScope',
        );
    }

    public function create()
    {
        $leaders = User::where('church_id', auth()->user()->church_id)
            ->whereIn('role', ['program_leader', 'cell_leader', 'leader'])
            ->orderBy('first_name')
            ->get();

        return view('users.form', ['mode' => 'create', 'User' => null, 'leaders' => $leaders]);
    }

    public function store(UpdateDiscipleRequest $request)
    {
        $subscription = app(SubscriptionService::class);

        if (! $subscription->isAllowed(auth()->user()->church, 'add_disciple')) {
            return back()->with('error', 'Disciple limit reached. Please upgrade your plan.');
        }

        $disciple = $this->enrollmentService->enroll(auth()->user()->church_id, $request->validated());

        // Notify the assigned leader
        $leaderId = $request->validated('leader_id');
        if ($leaderId) {
            $leader = User::find($leaderId);
            $leader?->notify(new DiscipleAssigned($disciple, auth()->user()));
        }

        return redirect()->route('users.index')->with('success', 'Member added.');
    }

    public function show(int $id)
    {
        $user = User::where('id', $id)->where('church_id', auth()->user()->church_id)
            ->with(['profile', 'membership.leader'])->firstOrFail();

        $this->authorize('view', $user);

        return view('users.show', compact('user'));
    }

    public function edit(int $id)
    {
        $User = User::where('id', $id)->where('church_id', auth()->user()->church_id)
            ->with(['membership', 'profile'])
            ->firstOrFail();

        $leaders = User::where('church_id', auth()->user()->church_id)
            ->whereIn('role', ['program_leader', 'cell_leader', 'leader'])
            ->orderBy('first_name')
            ->get();

        return view('users.form', ['mode' => 'edit', 'User' => $User, 'leaders' => $leaders]);
    }

    public function update(UpdateDiscipleRequest $request, int $id)
    {
        $User = User::where('id', $id)->where('church_id', auth()->user()->church_id)->firstOrFail();

        $data    = $request->validated();
        $pw      = $data['password'] ?? null;
        $newRole = $data['role'] ?? null;
        unset($data['password'], $data['role']);

        // Track leader change before update
        $oldLeaderId = $User->membership?->leader_id;
        $newLeaderId = $request->input('leader_id');

        $attrs = [
            'first_name' => $data['first_name'],
            'last_name'  => $data['last_name'],
            'nickname'   => $data['nickname'] ?? null,
            'email'      => $data['email'],
        ];
        if ($pw) {
            $attrs['password'] = Hash::make($pw);
        }
        $User->update($attrs);

        $User->profile()->updateOrCreate(
            ['user_id' => $User->id],
            $request->only([
                'mobile_number', 'gender', 'civil_status', 'date_of_birth', 'address', 'city', 'state', 'postal_code',
            ])
        );

        $User->membership()->update($request->only([
            'leader_id', 'status', 'destiny_training_level', 'satellite', 'joined_at', 'baptized_at',
        ]));

        // Notify new leader if leader was changed
        if ($newLeaderId && $newLeaderId != $oldLeaderId) {
            $newLeader = User::find($newLeaderId);
            $newLeader?->notify(new DiscipleAssigned($User, auth()->user()));
        }

        if ($newRole) {
            $roleId = Role::query()->where('slug', $newRole)->value('id');
            $User->forceFill([
                'role'    => $newRole,
                'role_id' => $roleId,
            ])->save();
        }

        return redirect()->route('users.index')->with('success', 'Member updated.');
    }

    public function destroy(int $id)
    {
        $User = User::where('id', $id)->where('church_id', auth()->user()->church_id)->firstOrFail();
        $this->authorize('delete', $User);
        $User->delete();

        return redirect()->route('users.index')->with('success', 'Member removed.');
    }

    public function import(Request $request)
    {
        $request->validate(['csv_file' => ['required', 'file', 'mimes:csv,txt']]);
        $result = $this->enrollmentService->bulkImport(
            auth()->user()->church_id,
            $request->file('csv_file')->getRealPath()
        );

        return redirect()->route('users.index')
            ->with('success', "Imported {$result['created']}, skipped {$result['skipped']}.");
    }
}
