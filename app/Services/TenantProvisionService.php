<?php

namespace App\Services;

use App\Mail\TenantWelcomeMail;
use App\Models\Church;
use App\Models\ChurchMembership;
use App\Models\Tenant;
use App\Models\TenantSignup;
use App\Models\User;
use App\Models\UserProfile;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Throwable;

class TenantProvisionService
{
    /**
     * Creates tenant + domain. Stancl's TenantCreated listener runs CreateDatabase and MigrateDatabase,
     * then the pastor account is created in the tenant DB so subdomain login matches registration.
     */
    public function provisionFromSignup(TenantSignup $signup): Tenant
    {
        if ($signup->status !== 'pending') {
            throw new \InvalidArgumentException('Signup is not pending approval.');
        }

        $dbName = 'church_' . str_replace('-', '_', $signup->subdomain);

        $tenant = Tenant::create([
            'name'              => $signup->church_name,
            'email'             => $signup->pastor_email,
            'subdomain'         => $signup->subdomain,
            'db_name'           => $dbName,
            'status'            => 'active',
            'subscription_plan' => $signup->subscription_plan,
            'trial_ends_at'     => now()->addDays(14),
            'activated_at'      => now(),
        ]);

        $tenant->domains()->create([
            'domain' => $signup->subdomain,
        ]);

        $tenant->run(function () use ($signup): void {
            $this->createPastorAccountInTenantDatabase($signup);
        });

        try {
            Mail::to($tenant->email)->send(new TenantWelcomeMail($tenant));
        } catch (Throwable $e) {
            Log::warning('Welcome email failed after provision', [
                'tenant_id' => $tenant->id,
                'error'     => $e->getMessage(),
            ]);
        }

        $signup->update([
            'status'    => 'provisioned',
            'tenant_id' => $tenant->id,
        ]);

        return $tenant;
    }

    /**
     * Mirrors the structure created by AuthController::register so tenant login works with signup credentials.
     */
    private function createPastorAccountInTenantDatabase(TenantSignup $signup): void
    {
        $church = Church::query()->create([
            'name'              => $signup->church_name,
            'address'           => $signup->church_address,
            'subscription_plan' => $signup->subscription_plan,
            'is_active'         => true,
        ]);

        [$firstName, $lastName] = $this->splitPastorName($signup->pastor_name);

        $passwordHash = $signup->getRawOriginal('password');

        $user = User::query()->create([
            'church_id'  => $church->id,
            'first_name' => $firstName,
            'last_name'  => $lastName,
            'email'      => $signup->pastor_email,
            'password'   => $passwordHash,
            'role'       => 'pastor',
        ]);

        UserProfile::query()->create([
            'user_id' => $user->id,
        ]);

        ChurchMembership::query()->create([
            'user_id'   => $user->id,
            'church_id' => $church->id,
            'status'    => 'active',
            'joined_at' => now()->toDateString(),
        ]);
    }

    /**
     * @return array{0: string, 1: string}
     */
    private function splitPastorName(string $name): array
    {
        $name = trim($name);
        if ($name === '') {
            return ['Pastor', '-'];
        }

        $parts = preg_split('/\s+/u', $name, 2, PREG_SPLIT_NO_EMPTY) ?: [];

        $first = $parts[0] ?? 'Pastor';
        $last  = $parts[1] ?? '-';

        return [$first, $last];
    }

    public function buildTenantUrl(string $subdomain): string
    {
        $domain = config('app.domain', 'localhost');
        $scheme = request()->getScheme();
        $port   = request()->getPort();

        if (in_array($domain, ['localhost', '127.0.0.1'], true)) {
            return "{$scheme}://{$subdomain}.{$domain}:{$port}";
        }

        return "https://{$subdomain}.{$domain}";
    }
}
