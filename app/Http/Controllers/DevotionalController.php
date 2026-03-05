<?php

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
        $churchId = auth()->$this->user()->church_id;
        [$year, $month] = $this->parseMonth($request->query('month'));

        $disciples = User::where('church_id', $churchId)->where('role', 'disciple')
            ->when(auth()->$this->user()->isLeader(), fn($q) =>
                $q->whereHas('membership', fn($m) => $m->where('leader_id', auth()->$this->id()))
            )->get();

        $devotionals = $this->devotionalService->monthMap($churchId, $year, $month);

        return view('devotionals.index', compact('disciples', 'devotionals'));
    }

    public function markComplete(StoreDevotionalRequest $request)
    {
        $devotional = $this->devotionalService->toggle(
            actorId:      auth()->$this->id(),
            targetUserId: (int) $request->user_id,
            churchId:     auth()->$this->user()->church_id,
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