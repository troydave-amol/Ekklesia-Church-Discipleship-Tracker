<?php

namespace App\Http\Controllers;

use App\Mail\TenantWelcomeMail;
use App\Models\Tenant;
use App\Services\TenantDatabaseManager;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Throwable;

class TenantRegistrationController extends Controller
{
    public function __construct(
        protected TenantDatabaseManager $dbManager
    ) {}

    // ── Show the registration form ────────────────────────────
    public function showForm()
    {
        return view('auth.register-tenant');
    }

    // ── Show the provision animation page ────────────────────
    public function provision()
    {
        return view('provision');
    }

    // ── AJAX subdomain availability check ────────────────────
    public function checkSubdomain(Request $request)
    {
        $subdomain = strtolower(trim($request->input('subdomain', '')));

        $error = $this->validateSubdomainString($subdomain);
        if ($error) {
            return response()->json(['available' => false, 'message' => $error]);
        }

        $taken = Tenant::where('subdomain', $subdomain)->exists();

        return response()->json([
            'available' => !$taken,
            'message'   => $taken ? 'This subdomain is already taken.' : 'Available!',
        ]);
    }

    // ── Main API endpoint — called by provision page JS ───────
    public function store(Request $request)
    {
        $validated = $request->validate([
            'church_name'    => ['required', 'string', 'max:255'],
            'pastor_name'    => ['required', 'string', 'max:255'],
            'pastor_email'   => ['required', 'email', 'unique:tenants,email'],
            'pastor_password' => ['required', 'string', 'min:8'],
            'subdomain'      => [
                'required',
                'string',
                'min:3',
                'max:32',
                'regex:/^[a-z0-9][a-z0-9\-]*[a-z0-9]$/',
                'unique:tenants,subdomain',
            ],
            'plan'           => ['sometimes', 'string', 'in:free,basic,standard,premium'],
            'church_address' => ['sometimes', 'nullable', 'string', 'max:500'],
        ], [
            'subdomain.regex'     => 'Only lowercase letters, numbers, and hyphens allowed.',
            'subdomain.unique'    => 'This subdomain is already taken.',
            'pastor_email.unique' => 'An account with this email already exists.',
        ]);

        $dbName = 'church_' . str_replace('-', '_', $validated['subdomain']);
        $tenant = null;

        try {
            // 1. Create tenant record
            $tenant = Tenant::create([
                'name'              => $validated['church_name'],
                'email'             => $validated['pastor_email'],
                'password'          => bcrypt($validated['pastor_password'] ?? str()->random(8)), // random password if not provided
                'subdomain'         => $validated['subdomain'],
                'db_name'           => $dbName,
                'status'            => 'active',
                'subscription_plan' => $validated['plan'] ?? 'basic',
                'trial_ends_at'     => now()->addDays(14),
                'activated_at'      => now(),
            ]);

            // 2. Create domain record (subdomain only — no .localhost suffix)
            $tenant->domains()->create([
                'domain' => $validated['subdomain'],
            ]);

            // 3. Create tenant database
            $this->dbManager->createDatabase($tenant);

            // 4. Run tenant migrations
            $this->dbManager->runMigrations($tenant);

            // 5. Send welcome email (non-blocking)
            try {
                Mail::to($tenant->email)->send(new TenantWelcomeMail($tenant));
            } catch (Throwable $e) {
                Log::warning('Welcome email failed', [
                    'tenant_id' => $tenant->id,
                    'error'     => $e->getMessage(),
                ]);
            }

            // 6. Return success JSON to provision page
            return response()->json([
                'success' => true,
                'url'     => $this->buildTenantUrl($validated['subdomain']),
                'tenant'  => [
                    'id'        => $tenant->id,
                    'name'      => $tenant->name,
                    'subdomain' => $tenant->subdomain,
                    'plan'      => $tenant->subscription_plan,
                ],
            ]);

        } catch (Throwable $e) {
            if ($tenant?->id) {
                try { $this->dbManager->dropDatabase($tenant); } catch (Throwable) {}
                $tenant->delete();
            }

            Log::error('Tenant provisioning failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to provision workspace: ' . $e->getMessage(),
            ], 500);
        }
    }

    // ── Helpers ───────────────────────────────────────────────
    private function buildTenantUrl(string $subdomain): string
    {
        $domain = config('app.domain', 'localhost');
        $scheme = request()->getScheme();
        $port   = request()->getPort();

        // Local dev — include port number
        if (in_array($domain, ['localhost', '127.0.0.1'])) {
            return "{$scheme}://{$subdomain}.{$domain}:{$port}";
        }

        return "https://{$subdomain}.{$domain}";
    }

    private function validateSubdomainString(string $subdomain): ?string
    {
        if (strlen($subdomain) < 3) return 'Too short — minimum 3 characters.';
        if (strlen($subdomain) > 32) return 'Too long — maximum 32 characters.';
        if (!preg_match('/^[a-z0-9][a-z0-9\-]*[a-z0-9]$/', $subdomain)) {
            return 'Only lowercase letters, numbers, and hyphens allowed.';
        }

        $reserved = ['www', 'app', 'api', 'admin', 'mail', 'ftp', 'dev', 'staging'];
        if (in_array($subdomain, $reserved)) {
            return 'This subdomain is reserved.';
        }

        return null;
    }
}