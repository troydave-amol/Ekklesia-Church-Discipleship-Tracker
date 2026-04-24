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

    public function destroy(Tenant $tenant)
    {
        $name = $tenant->name;

        try {
            // Stancl fires TenantDeleted → drops the physical DB + removes domains
            $tenant->delete();
        } catch (\Throwable $e) {
            // If the physical DB was already gone (orphaned record), force-delete the metadata
            $tenant->domains()->delete();
            $tenant->forceDelete();

            \Illuminate\Support\Facades\Log::warning('Tenant deleted with orphaned DB cleanup', [
                'tenant'  => $name,
                'error'   => $e->getMessage(),
            ]);
        }

        return redirect()
            ->route('admin.tenants.index')
            ->with('success', "Tenant \"{$name}\" has been permanently deleted.");
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

    // ── Admin-initiated tenant provisioning ───────────────────────────────────

    public function adminCreate(): \Illuminate\View\View
    {
        return view('admin.tenants.create');
    }

    public function adminStore(\Illuminate\Http\Request $request)
    {
        $validated = $request->validate([
            'church_name'       => ['required', 'string', 'max:255'],
            'subdomain'         => [
                'required', 'string', 'min:3', 'max:32',
                'regex:/^[a-z0-9][a-z0-9\-]*[a-z0-9]$/',
                \Illuminate\Validation\Rule::unique('tenants', 'subdomain'),
            ],
            'admin_email'       => ['required', 'email', 'max:255'],
            'admin_name'        => ['required', 'string', 'max:255'],
            'subscription_plan' => ['required', 'in:free,basic,standard,premium'],
        ], [
            'subdomain.regex'  => 'Only lowercase letters, numbers, and hyphens allowed.',
            'subdomain.unique' => 'This subdomain is already taken.',
        ]);

        try {
            $subdomain = $validated['subdomain'];
            $dbName    = 'church_' . str_replace('-', '_', $subdomain);

            // 1. Create Tenant record
            $tenant = Tenant::create([
                'name'              => $validated['church_name'],
                'email'             => $validated['admin_email'],
                'subdomain'         => $subdomain,
                'db_name'           => $dbName,
                'status'            => 'active',
                'subscription_plan' => $validated['subscription_plan'],
                'activated_at'      => now(),
            ]);

            // 2. Create Domain
            $tenant->domains()->create([
                'domain' => $subdomain . '.' . config('app.domain', 'localhost'),
            ]);

            // 3. Run tenant migrations
            \Illuminate\Support\Facades\Artisan::call('tenancy:migrate', [
                '--tenants' => [$tenant->id],
                '--force'   => true,
            ]);

            // 4. Initialize tenancy, create church_admin user, seed
            tenancy()->initialize($tenant);

            try {
                $role = \App\Models\Role::where('slug', 'church_admin')->first();

                \App\Models\User::create([
                    'first_name' => \Illuminate\Support\Str::before($validated['admin_name'], ' '),
                    'last_name'  => \Illuminate\Support\Str::after($validated['admin_name'], ' ') ?: null,
                    'email'      => $validated['admin_email'],
                    'password'   => \Illuminate\Support\Facades\Hash::make(\Illuminate\Support\Str::random(12)),
                    'role_id'    => $role?->id,
                ]);

                \Illuminate\Support\Facades\Artisan::call('db:seed', [
                    '--class' => 'RolesAndPermissionsSeeder',
                    '--force' => true,
                ]);
            } finally {
                tenancy()->end();
            }

            return redirect()
                ->route('admin.tenants.index')
                ->with('success', "Tenant \"{$validated['church_name']}\" provisioned successfully.");

        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error('Admin tenant provisioning failed', [
                'error' => $e->getMessage(),
            ]);

            return back()
                ->withInput()
                ->with('error', 'Provisioning failed: ' . $e->getMessage());
        }
    }
}
