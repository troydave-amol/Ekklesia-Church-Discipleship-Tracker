<?php

namespace Tests;

use App\Models\Tenant;
use App\Models\Church;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Http\Middleware\ValidateCsrfToken;
use Stancl\Tenancy\Middleware\InitializeTenancyBySubdomain;
use Stancl\Tenancy\Middleware\InitializeTenancyByDomain;
use Stancl\Tenancy\Middleware\InitializeTenancyByDomainOrSubdomain;
use Stancl\Tenancy\Middleware\PreventAccessFromCentralDomains;

abstract class TenantTestCase extends TestCase
{
    use RefreshDatabase;

    protected Tenant $tenant;
    protected Church $church;

    protected function setUp(): void
    {
        parent::setUp();

        // Disable the HTTP-layer tenancy identification middleware since we
        // initialize tenancy programmatically in setUp(). Without this, every
        // test HTTP request tries to re-identify the tenant by subdomain and
        // throws "Tenant could not be identified on domain ..." errors.
        $this->withoutMiddleware([
            InitializeTenancyBySubdomain::class,
            InitializeTenancyByDomain::class,
            InitializeTenancyByDomainOrSubdomain::class,
            PreventAccessFromCentralDomains::class,
            \App\Http\Middleware\EnsureTenantIsActive::class,
            ValidateCsrfToken::class,
        ]);

        // Create a unique test tenant with a short alphanumeric ID
        $id = 't' . substr(uniqid(), -6);
        $this->tenant = Tenant::create([
            'id'        => $id,
            'name'      => 'Test Tenant',
            'email'     => 'test@tenant.local',
            'subdomain' => $id,
            'db_name'   => 'ekklesia_tenant_test',
        ]);
        $this->tenant->domains()->create(['domain' => $id . '.test']);

        // Initialize tenancy — configures the dynamic 'tenant' DB connection
        tenancy()->initialize($this->tenant);

        // Wipe and re-run tenant migrations to guarantee a clean slate.
        // Using migrate-fresh avoids "table already exists" when the shared
        // ekklesia_tenant_test database is reused across test classes.
        $this->artisan('tenants:migrate-fresh', ['--tenants' => [$this->tenant->id]]);

        // Seed RBAC roles and permissions into the fresh tenant DB
        $this->artisan('tenants:seed', [
            '--tenants' => [$this->tenant->id],
            '--class'   => 'RolesAndPermissionsSeeder',
            '--force'   => true,
        ]);

        $this->church = Church::query()->create([
            'name' => 'Test Church',
            'address' => '123 Test Street',
            'subscription_plan' => 'free',
            'is_active' => true,
        ]);
    }

    protected function tearDown(): void
    {
        // End tenancy so the next test class starts clean
        tenancy()->end();

        // Clean up the tenant record (its DB is reused, not dropped)
        try {
            $this->tenant->domains()->delete();
            $this->tenant->delete();
        } catch (\Throwable $e) {
            // Ignore cleanup errors
        }

        parent::tearDown();
    }

    /**
     * Create a user with a specific role slug in the active tenant DB.
     *
     * Usage: $user = $this->createTenantUser('church_admin');
     */
    protected function createTenantUser(string $roleSlug, array $attributes = []): \App\Models\User
    {
        $role = \App\Models\Role::where('slug', $roleSlug)->firstOrFail();

        $attributes['church_id'] ??= $this->church->id;
        $attributes['role'] ??= $roleSlug;
        $attributes['role_id'] = $role->id;

        return \App\Models\User::factory()->create($attributes);
    }
}
