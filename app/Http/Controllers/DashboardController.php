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

        return match ($user->role) {
            'pastor'  => $this->pastor($user),
            'leader'  => $this->leader($user),
            default   => $this->disciple($user),
        };
    }

    private function pastor($user)  // ✅ accepts User, not Request
    {
        $stats = $this->progressService->pastorStats($user->church);
        return view('dashboard.pastor', $stats);
    }

    private function leader($user)  // ✅
    {
        $stats = $this->progressService->leaderStats($user);
        return view('dashboard.leader', $stats);
    }

    public function disciple($user)  // ✅
    {
        $stats = $this->progressService->discipleStats($user);
        return view('dashboard.disciple', $stats);
    }
}