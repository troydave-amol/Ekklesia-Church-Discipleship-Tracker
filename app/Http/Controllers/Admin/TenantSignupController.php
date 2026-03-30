<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TenantSignup;
use App\Services\TenantProvisionService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Throwable;

class TenantSignupController extends Controller
{
    public function index(Request $request): View
    {
        $status = $request->query('status', 'pending');

        $signups = TenantSignup::query()
            ->when($status === 'all', fn ($q) => $q)
            ->when($status !== 'all', fn ($q) => $q->where('status', $status))
            ->orderByDesc('created_at')
            ->paginate(20)
            ->withQueryString();

        return view('admin.signups.index', [
            'signups' => $signups,
            'filter'  => $status,
        ]);
    }

    public function approve(TenantSignup $signup, TenantProvisionService $provision): RedirectResponse
    {
        if (!$signup->isPending()) {
            return back()->with('error', 'This request is no longer pending.');
        }

        try {
            $provision->provisionFromSignup($signup);
        } catch (Throwable $e) {
            return back()->with('error', 'Provisioning failed: '.$e->getMessage());
        }

        return back()->with('success', 'Church approved and workspace provisioned.');
    }

    public function reject(Request $request, TenantSignup $signup): RedirectResponse
    {
        if (!$signup->isPending()) {
            return back()->with('error', 'This request is no longer pending.');
        }

        $request->validate([
            'rejection_note' => ['nullable', 'string', 'max:2000'],
        ]);

        $signup->update([
            'status'         => 'rejected',
            'rejection_note' => $request->input('rejection_note'),
        ]);

        return back()->with('success', 'Registration request rejected.');
    }
}
