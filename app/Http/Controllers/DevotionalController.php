<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreDevotionalRequest;
use App\Notifications\DevotionalCompleted;
use App\Services\DevotionalService;
use App\Models\Devotional;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DevotionalController extends Controller
{
    public function __construct(private DevotionalService $devotionalService) {}

    public function index(Request $request)
    {
        return $this->buildLeaderDevotionalIndex($request, null);
    }

    public function mine(Request $request)
    {
        if (! auth()->user()->isDisciple()) {
            abort(403);
        }

        return $this->buildDiscipleDevotionalView($request);
    }

    public function bulkStore(Request $request)
    {
        $request->validate([
            'date'                => ['required', 'date', 'before_or_equal:today'],
            'completed'           => ['required', 'array'],
            'completed.*'         => ['nullable', 'in:0,1'],
        ]);

        $churchId = auth()->user()->church_id;
        $date     = $request->date;

        foreach ($request->input('completed', []) as $userId => $raw) {
            if (! is_numeric($userId)) {
                continue;
            }

            // Policy: verify actor can manage this disciple's devotional
            $targetUser = User::where('church_id', $churchId)->find((int) $userId);
            if (! $targetUser || auth()->user()->cannot('manage', [Devotional::class, $targetUser])) {
                continue; // silently skip unauthorized targets
            }

            $completed = ($raw === '1' || $raw === 1 || $raw === true);
            $this->devotionalService->toggle(auth()->id(), (int) $userId, $churchId, $date, $completed);
        }

        return redirect()->route('devotionals.index', ['week' => $date])->with('success', 'Devotionals saved.');
    }

    public function markComplete(StoreDevotionalRequest $request)
    {
        $churchId   = auth()->user()->church_id;
        $targetUser = User::where('church_id', $churchId)->findOrFail((int) $request->user_id);

        // Policy: verify actor can manage this disciple's devotional
        $this->authorize('manage', [Devotional::class, $targetUser]);

        $devotional = $this->devotionalService->toggle(
            actorId:      auth()->id(),
            targetUserId: (int) $request->user_id,
            churchId:     $churchId,
            date:         $request->date,
            completed:    (bool) $request->completed,
        );

        // Notify the leader when a disciple completes a devotional
        if ($devotional->completed && $targetUser->membership?->leader_id) {
            $leader = User::find($targetUser->membership->leader_id);
            if ($leader && $leader->id !== auth()->id()) {
                $leader->notify(new DevotionalCompleted($targetUser, $request->date));
            }
        }

        return response()->json(['success' => true, 'completed' => $devotional->completed]);
    }

    private function buildDiscipleDevotionalView(Request $request)
    {
        $user     = auth()->user();
        $churchId = $user->church_id;

        $weekRef = $request->query('week')
            ? Carbon::parse($request->query('week'))->startOfWeek(Carbon::MONDAY)
            : now()->startOfWeek(Carbon::MONDAY);

        $weekStart = $weekRef->copy();
        $weekEnd   = $weekRef->copy()->endOfWeek(Carbon::SUNDAY);
        $weekDays  = collect(range(0, 6))->map(fn ($i) => $weekStart->copy()->addDays($i));
        $prevWeek  = $weekStart->copy()->subWeek()->toDateString();
        $nextWeek  = $weekStart->copy()->addWeek()->toDateString();

        $d         = $user->load('membership.leader');
        $d->weeklyDevotionals = $this->weeklyCompletionMap($d->id, $churchId, $weekStart, $weekEnd);
        $d->streak            = $this->devotionalService->streakForUser($d->id, $churchId);
        $d->devotional_rate   = $this->devotionalService->monthlyRate($d->id, $churchId);

        $disciples = collect([$d]);

        $completedToday = Devotional::withoutGlobalScopes()
            ->where('church_id', $churchId)
            ->where('user_id', $d->id)
            ->where('date', now()->toDateString())
            ->where('completed', true)
            ->exists() ? 1 : 0;
        $totalDisciples = 1;

        return view('devotionals.mine', compact(
            'disciples',
            'weekDays',
            'prevWeek',
            'nextWeek',
            'weekStart',
            'weekEnd',
            'completedToday',
            'totalDisciples',
        ));
    }

    private function buildLeaderDevotionalIndex(Request $request, ?User $restrictToUser): mixed
    {
        $churchId = auth()->user()->church_id;
        $actor    = auth()->user();

        $weekRef = $request->query('week')
            ? Carbon::parse($request->query('week'))->startOfWeek(Carbon::MONDAY)
            : now()->startOfWeek(Carbon::MONDAY);

        $weekStart = $weekRef->copy();
        $weekEnd   = $weekRef->copy()->endOfWeek(Carbon::SUNDAY);
        $weekDays  = collect(range(0, 6))->map(fn ($i) => $weekStart->copy()->addDays($i));
        $prevWeek  = $weekStart->copy()->subWeek()->toDateString();
        $nextWeek  = $weekStart->copy()->addWeek()->toDateString();

        $disciplesQuery = User::where('church_id', $churchId)->where('role', 'disciple')
            ->when($restrictToUser, fn ($q) => $q->where('id', $restrictToUser->id))
            ->when(! $restrictToUser && $actor->isRestrictedLeader(), fn ($q) =>
                $q->whereHas('membership', fn ($m) => $m->where('leader_id', $actor->id)));

        $disciples = $disciplesQuery->with('membership.leader')->get();

        foreach ($disciples as $d) {
            $d->weeklyDevotionals = $this->weeklyCompletionMap($d->id, $churchId, $weekStart, $weekEnd);
            $d->streak            = $this->devotionalService->streakForUser($d->id, $churchId);
            $d->devotional_rate   = $this->devotionalService->monthlyRate($d->id, $churchId);
        }

        $totalDisciples = $disciples->count();

        $todayStr = now()->toDateString();
        $completedToday = $disciples->filter(fn ($d) => ($d->weeklyDevotionals[$todayStr] ?? false))->count();
        $todayRate      = $totalDisciples > 0 ? (int) round($completedToday / $totalDisciples * 100) : 0;

        $rates = $disciples->pluck('devotional_rate')->filter();
        $weeklyAvg = $rates->isNotEmpty() ? (int) round($rates->avg()) : 0;

        $prevWeekStart = $weekStart->copy()->subWeek();
        $prevCompleted = 0;
        $prevTotal     = 0;
        foreach ($disciples as $d) {
            $map = $this->weeklyCompletionTrueCount($d->id, $churchId, $prevWeekStart);
            $prevCompleted += $map;
            $prevTotal     += 7;
        }
        $prevWeeklyAvg = $prevTotal > 0 ? (int) round($prevCompleted / $prevTotal * 100) : 0;
        $weeklyChange  = $weeklyAvg - $prevWeeklyAvg;

        $longestStreakUser = $disciples->sortByDesc(fn ($d) => $d->streak)->first();
        $longestStreak     = $longestStreakUser
            ? (object) ['streak' => $longestStreakUser->streak, 'name' => $longestStreakUser->first_name.' '.$longestStreakUser->last_name]
            : (object) ['streak' => 0, 'name' => '—'];

        $perfectWeekCount = $disciples->filter(function ($d) use ($weekDays, $churchId) {
            foreach ($weekDays as $day) {
                if ($day->isFuture()) {
                    continue;
                }
                $ok = Devotional::withoutGlobalScopes()
                    ->where('church_id', $churchId)
                    ->where('user_id', $d->id)
                    ->where('date', $day->toDateString())
                    ->where('completed', true)
                    ->exists();
                if (! $ok) {
                    return false;
                }
            }

            return true;
        })->count();

        return view('devotionals.index', compact(
            'disciples',
            'weekDays',
            'prevWeek',
            'nextWeek',
            'weekStart',
            'weekEnd',
            'completedToday',
            'totalDisciples',
            'todayRate',
            'weeklyAvg',
            'weeklyChange',
            'longestStreak',
            'perfectWeekCount'
        ));
    }

    private function weeklyCompletionTrueCount(int $userId, int $churchId, Carbon $weekStart): int
    {
        $weekEnd = $weekStart->copy()->endOfWeek(Carbon::SUNDAY);

        return (int) Devotional::withoutGlobalScopes()
            ->where('church_id', $churchId)
            ->where('user_id', $userId)
            ->where('completed', true)
            ->whereBetween('date', [$weekStart->toDateString(), $weekEnd->toDateString()])
            ->count();
    }

    /**
     * @return array<string, bool>
     */
    private function weeklyCompletionMap(int $userId, int $churchId, Carbon $weekStart, Carbon $weekEnd): array
    {
        $rows = Devotional::withoutGlobalScopes()
            ->where('church_id', $churchId)
            ->where('user_id', $userId)
            ->whereBetween('date', [$weekStart->toDateString(), $weekEnd->toDateString()])
            ->where('completed', true)
            ->pluck('date');

        $map = [];
        foreach ($rows as $date) {
            $map[is_string($date) ? $date : $date->toDateString()] = true;
        }

        return $map;
    }

    /**
     * Update a devotional record.
     * church_admin can update any record; disciple can only update own.
     */
    public function update(Request $request, int $id)
    {
        $request->validate([
            'completed' => ['required', 'boolean'],
        ]);

        $devotional = Devotional::withoutGlobalScopes()->findOrFail($id);
        $user = auth()->user();

        if ($devotional->church_id !== $user->church_id) {
            abort(403);
        }

        // Disciples can only update their own records
        if ($user->isDisciple() && $devotional->user_id !== $user->id) {
            abort(403, 'You can only update your own devotional records.');
        }

        $devotional->completed = $request->boolean('completed');
        $devotional->save();

        return redirect()->back()->with('success', 'Devotional updated.');
    }

    /**
     * Delete a devotional record (church_admin only — enforced by route middleware).
     */
    public function destroy(int $id)
    {
        if (! auth()->user()->isChurchAdmin()) {
            abort(403);
        }

        $devotional = Devotional::withoutGlobalScopes()->findOrFail($id);

        if ($devotional->church_id !== auth()->user()->church_id) {
            abort(403);
        }

        $devotional->delete();

        return redirect()->back()->with('success', 'Devotional record deleted.');
    }
}
