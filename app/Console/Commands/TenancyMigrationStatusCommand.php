<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Models\Tenant;

class TenancyMigrationStatusCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tenancy:migration-status';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Show migration status for all tenants (validates connection and prefix)';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $tenants = Tenant::all();

        if ($tenants->isEmpty()) {
            $this->info('No tenants found.');
            return 0;
        }

        $headers = ['Tenant (db_name)', 'Pending Migrations', 'Migration Status'];
        $rows = [];

        $hasIssues = false;

        foreach ($tenants as $tenant) {
            try {
                // Initialize tenancy for this tenant
                tenancy()->initialize($tenant);

                // Get all migration files in tenant directory
                $migrationPath = database_path('migrations/tenant');
                $migrationFiles = $this->getMigrationFiles($migrationPath);

                // Get executed migrations from tenant DB
                $executed = DB::table('migrations')
                    ->pluck('migration')
                    ->toArray();

                // Calculate pending migrations
                $pending = array_filter($migrationFiles, function ($file) use ($executed) {
                    $baseName = str_replace('.php', '', basename($file));
                    return !in_array($baseName, $executed);
                });

                $pendingCount = count($pending);
                $statusIcon = $pendingCount === 0 ? '✓' : '⚠';
                $statusText = $pendingCount === 0 ? 'Up to date' : 'Pending';

                $rows[] = [
                    $tenant->db_name,
                    $pendingCount,
                    "$statusIcon $statusText",
                ];

                if ($pendingCount > 0) {
                    $hasIssues = true;
                }
            } catch (\Exception $e) {
                $rows[] = [
                    $tenant->db_name,
                    '—',
                    "✗ Error: {$e->getMessage()}",
                ];
                $hasIssues = true;
            } finally {
                tenancy()->end();
            }
        }

        // Display table
        $this->table($headers, $rows);

        // Validate tenant migrations have correct prefix
        $this->validateMigrationPrefixes();

        // Exit status
        if ($hasIssues) {
            $this->warn('Some tenants have pending migrations or issues.');
            return 1;
        }

        $this->info('All tenants are up to date.');
        return 0;
    }

    /**
     * Get all migration files from a directory.
     */
    private function getMigrationFiles(string $path): array
    {
        if (!is_dir($path)) {
            return [];
        }

        $files = glob($path . '/*.php');
        return $files ?: [];
    }

    /**
     * Validate that tenant migrations have the required _tenant_ prefix.
     */
    private function validateMigrationPrefixes(): void
    {
        $tenantMigrationPath = database_path('migrations/tenant');

        if (!is_dir($tenantMigrationPath)) {
            return;
        }

        $files = glob($tenantMigrationPath . '/*.php');

        if (empty($files)) {
            return;
        }

        $invalidFiles = [];

        foreach ($files as $file) {
            $baseName = basename($file);
            // Skip compiled migrations
            if (str_starts_with($baseName, '.')) {
                continue;
            }
            // Check for _tenant_ prefix after timestamp
            if (!preg_match('/^\d+_tenant_/', $baseName)) {
                $invalidFiles[] = $baseName;
            }

            // Check migration file has $connection = 'tenant'
            $content = file_get_contents($file);
            if (!str_contains($content, "protected \$connection = 'tenant'")) {
                $this->warn("⚠ Migration missing connection: $baseName");
                $invalidFiles[] = $baseName;
            }
        }

        if (!empty($invalidFiles)) {
            $this->error('Invalid tenant migration files (must have _tenant_ prefix and $connection = \'tenant\'):');
            foreach ($invalidFiles as $file) {
                $this->line("  - $file");
            }
        }
    }
}
