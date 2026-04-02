<?php

namespace App\Http\Controllers;

use App\Services\ProgressService;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function __construct(private ProgressService $progressService) {}

    public function index(Request $request)
    {
        $user = $request->user();

        if ($user->isChurchAdmin()) {
            return $this->pastor($user);
        }
        if ($user->isRestrictedLeader()) {
            return $this->leader($user);
        }

        return $this->disciple($user);
    }

    public function discipleDashboard(Request $request)
    {
        return $this->disciple($request->user());
    }

    private function pastor($user)
    {
        $stats = $this->progressService->pastorStats($user->church);

        return view('dashboard.pastor', $stats);
    }

    private function leader($user)
    {
        $stats = $this->progressService->leaderStats($user);

        return view('dashboard.leader', $stats);
    }

    private function disciple($user)
    {
        $stats = $this->progressService->discipleStats($user);

        return view('dashboard.disciple', $stats);
    }
}
