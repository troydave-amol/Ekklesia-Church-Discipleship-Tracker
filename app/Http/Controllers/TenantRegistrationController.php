<?php

namespace App\Http\Controllers;

use App\Models\Tenant;
use App\Models\TenantSignup;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class TenantRegistrationController extends Controller
{
    public function showForm(): View
    {
        return view('auth.register-tenant');
    }

    public function provision(): View
    {
        return view('provision');
    }

    public function index(): View
    {
        $tenants = Tenant::query()->orderByDesc('created_at')->paginate(25);

        return view('admin.tenants.index', compact('tenants'));
    }

    public function show(Tenant $tenant): View
    {
        $tenant->load('domains');

        return view('admin.tenants.show', compact('tenant'));
    }

    public function edit(Tenant $tenant): View
    {
        return view('admin.tenants.edit', compact('tenant'));
    }

    public function update(Request $request, Tenant $tenant)
    {
        $data = $request->validate([
            'name'              => ['required', 'string', 'max:255'],
            'email'             => ['required', 'email', 'max:255'],
            'subscription_plan' => ['required', 'in:free,basic,standard,premium'],
            'status'            => ['required', 'in:active,inactive,suspended'],
        ]);

        $tenant->update($data);

        return redirect()
            ->route('admin.tenants.show', $tenant)
            ->with('success', "Tenant \"{$tenant->name}\" updated successfully.");
    }

    public function toggleStatus(Tenant $tenant)
    {
        $newStatus = $tenant->status === 'active' ? 'suspended' : 'active';
        $tenant->update(['status' => $newStatus]);

        $label = $newStatus === 'active' ? 'enabled' : 'suspended';

        return back()->with('success', "Tenant \"{$tenant->name}\" has been {$label}.");
    }

    public function checkSubdomain(Request $request): JsonResponse
    {
        $subdomain = strtolower(trim($request->input('subdomain', '')));

        $error = $this->validateSubdomainString($subdomain);
        if ($error) {
            return response()->json(['available' => false, 'message' => $error]);
        }

        $taken = Tenant::where('subdomain', $subdomain)->exists()
            || TenantSignup::where('subdomain', $subdomain)->where('status', 'pending')->exists();

        return response()->json([
            'available' => !$taken,
            'message'     => $taken ? 'This subdomain is already taken.' : 'Available!',
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'church_name'     => ['required', 'string', 'max:255'],
            'pastor_name'     => ['required', 'string', 'max:255'],
            'pastor_email'    => [
                'required',
                'email',
                Rule::unique('tenants', 'email'),
                Rule::unique('tenant_signups', 'pastor_email')->where(fn ($q) => $q->where('status', 'pending')),
            ],
            'pastor_password' => ['required', 'string', 'min:8'],
            'subdomain'       => [
                'required',
                'string',
                'min:3',
                'max:32',
                'regex:/^[a-z0-9][a-z0-9\-]*[a-z0-9]$/',
                Rule::unique('tenants', 'subdomain'),
                Rule::unique('tenant_signups', 'subdomain')->where(fn ($q) => $q->where('status', 'pending')),
            ],
            'plan'            => ['sometimes', 'string', 'in:free,basic,standard,premium'],
            'church_address'  => ['sometimes', 'nullable', 'string', 'max:500'],
        ], [
            'subdomain.regex'     => 'Only lowercase letters, numbers, and hyphens allowed.',
            'subdomain.unique'    => 'This subdomain is already taken.',
            'pastor_email.unique' => 'This email already has a pending registration or an existing church.',
        ]);

        TenantSignup::create([
            'church_name'       => $validated['church_name'],
            'church_address'    => $validated['church_address'] ?? null,
            'pastor_name'       => $validated['pastor_name'],
            'pastor_email'      => $validated['pastor_email'],
            'password'          => $validated['pastor_password'],
            'subdomain'         => $validated['subdomain'],
            'subscription_plan' => $validated['plan'] ?? 'free',
            'status'            => 'pending',
        ]);

        return response()->json([
            'success'          => true,
            'pending_approval' => true,
            'message'          => 'Your registration was received. The central team will review it before your subdomain and database are created. You will be notified by email.',
        ]);
    }

    private function validateSubdomainString(string $subdomain): ?string
    {
        if (strlen($subdomain) < 3) {
            return 'Too short — minimum 3 characters.';
        }
        if (strlen($subdomain) > 32) {
            return 'Too long — maximum 32 characters.';
        }
        if (!preg_match('/^[a-z0-9][a-z0-9\-]*[a-z0-9]$/', $subdomain)) {
            return 'Only lowercase letters, numbers, and hyphens allowed.';
        }

        $reserved = ['www', 'app', 'api', 'admin', 'mail', 'ftp', 'dev', 'staging'];
        if (in_array($subdomain, $reserved, true)) {
            return 'This subdomain is reserved.';
        }

        return null;
    }
}
