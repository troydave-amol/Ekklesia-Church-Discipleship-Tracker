<?php

namespace App\Http\Controllers;

use App\Models\MemberJoinRequest;
use App\Services\MemberJoinRequestService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Throwable;

class MemberJoinRequestController extends Controller
{
    public function __construct(
        private MemberJoinRequestService $memberJoinRequestService,
    ) {}

    public function approve(MemberJoinRequest $joinRequest): RedirectResponse
    {
        try {
            $member = $this->memberJoinRequestService->approve($joinRequest, auth()->user());
        } catch (Throwable $e) {
            return redirect()
                ->route('users.index', ['tab' => 'pending'])
                ->with('error', $e->getMessage());
        }

        return redirect()
            ->route('users.index', ['tab' => 'pending'])
            ->with('success', "{$member->fullName()} has been approved and added to Members.");
    }

    public function reject(Request $request, MemberJoinRequest $joinRequest): RedirectResponse
    {
        $validated = $request->validate([
            'rejection_note' => ['nullable', 'string', 'max:2000'],
        ]);

        try {
            $this->memberJoinRequestService->reject(
                $joinRequest,
                auth()->user(),
                $validated['rejection_note'] ?? null,
            );
        } catch (Throwable $e) {
            return redirect()
                ->route('users.index', ['tab' => 'pending'])
                ->with('error', $e->getMessage());
        }

        return redirect()
            ->route('users.index', ['tab' => 'pending'])
            ->with('success', 'Join request rejected.');
    }
}
