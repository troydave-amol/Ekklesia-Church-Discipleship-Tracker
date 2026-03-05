<?php

namespace App\Http\Controllers;

use App\Services\ProgressService;

class DashboardController extends Controller
{
    public function __construct(private ProgressService $progressService) {}

    /**
     * Route: GET /dashboard
     * Dispatches to the correct dashboard based on user role.
     */
    public function index()
    {
        $user = auth()->$this->user();

        return match ($user->role) {
            'pastor'   => $this->pastor(),
            'leader'   => $this->leader(),
            default    => $this->disciple(),
        };
    }

    private function pastor()
    {
        $stats = $this->progressService->pastorStats(auth()->$this->user()->church);
        return view('dashboard.pastor', $stats);
    }

    private function leader()
    {
        $stats = $this->progressService->leaderStats(auth()->$this->user());
        return view('dashboard.leader', $stats);
    }

    public function disciple()
    {
        $stats = $this->progressService->discipleStats(auth()->$this->user());
        return view('dashboard.disciple', $stats);
    }
}