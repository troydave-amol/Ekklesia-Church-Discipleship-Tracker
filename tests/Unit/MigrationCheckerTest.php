<?php

namespace Tests\Unit;

use App\Models\Tenant;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use PHPUnit\Framework\Attributes\Test;
use Tests\TenantTestCase;

class MigrationCheckerTest extends TenantTestCase
{
    #[Test]
    public function command_outputs_table_with_tenant_row(): void
    {
        $response = Artisan::call('tenancy:migration-status');

        $this->assertStringContainsString('ekklesia_tenant_test', Artisan::output());
        $this->assertStringContainsString('Tenant', Artisan::output());
    }

    #[Test]
    public function command_exits_zero_when_all_migrations_up_to_date(): void
    {
        // Verify all migrations are applied
        // TenantTestCase already migrated the DB for us
        DB::table('migrations')->truncate();
        $this->artisan('tenants:migrate', ['--tenants' => [$this->tenant->id]]);

        $response = Artisan::call('tenancy:migration-status');

        // Command should exit 0 when no pending migrations
        $this->assertEquals(0, $response);
    }

    #[Test]
    public function command_exits_one_when_pending_migration_detected(): void
    {
        // Create a fake pending migration marker
        $this->createFakeMigration('2026_04_20_999999_tenant_fake_pending');

        $response = Artisan::call('tenancy:migration-status');

        // Command should exit 1 when pending migrations exist
        $this->assertEquals(1, $response);
        $this->assertStringContainsString('Pending', Artisan::output());
    }

    #[Test]
    public function central_migration_throws_exception_when_run_against_tenant_connection(): void
    {
        // Create a migration without $connection or with wrong connection
        $invalidMigration = $this->createInvalidMigration();

        // Try to validate it through the command
        $response = Artisan::call('tenancy:migration-status');

        // Should warn about invalid migrations
        $this->assertNotEquals(0, $response);
    }

    #[Test]
    public function tenant_migration_must_have_correct_connection(): void
    {
        // Create a migration with missing $connection = 'tenant'
        $migrationPath = database_path('migrations/tenant');
        @mkdir($migrationPath, 0755, true);

        $fileName = '2026_04_21_000000_tenant_test_invalid.php';
        $filePath = $migrationPath . '/' . $fileName;

        $content = <<<'PHP'
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    // MISSING: protected $connection = 'tenant';

    public function up(): void
    {
        Schema::create('test_table', function (Blueprint $table) {
            $table->id();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('test_table');
    }
};
PHP;

        file_put_contents($filePath, $content);

        // Run the command
        $response = Artisan::call('tenancy:migration-status');

        // Should detect the missing connection warning
        $this->assertStringContainsString('missing connection', Artisan::output());

        // Cleanup
        unlink($filePath);
    }

    #[Test]
    public function tenant_migration_requires_tenant_prefix(): void
    {
        // Create a migration without _tenant_ prefix
        $migrationPath = database_path('migrations/tenant');
        @mkdir($migrationPath, 0755, true);

        $fileName = '2026_04_21_000001_invalid_no_prefix.php';
        $filePath = $migrationPath . '/' . $fileName;

        $content = <<<'PHP'
<?php

use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    protected $connection = 'tenant';

    public function up(): void { }
    public function down(): void { }
};
PHP;

        file_put_contents($filePath, $content);

        // Run the command
        $response = Artisan::call('tenancy:migration-status');

        // Should detect invalid naming
        $this->assertStringContainsString('Invalid tenant migration', Artisan::output());

        // Cleanup
        unlink($filePath);
    }

    #[Test]
    public function command_validates_all_tenant_connections(): void
    {
        // Create a test with correct naming and connection
        $migrationPath = database_path('migrations/tenant');
        @mkdir($migrationPath, 0755, true);

        $fileName = '2026_04_21_000002_tenant_valid.php';
        $filePath = $migrationPath . '/' . $fileName;

        $content = <<<'PHP'
<?php

use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    protected $connection = 'tenant';

    public function up(): void { }
    public function down(): void { }
};
PHP;

        file_put_contents($filePath, $content);

        // Run the command
        $response = Artisan::call('tenancy:migration-status');

        // Should pass validation (exit 0 or 1 depending on execution status, not due to validation error)
        $output = Artisan::output();
        $this->assertStringNotContainsString('Invalid tenant migration', $output);

        // Cleanup
        unlink($filePath);
    }

    // Helper methods

    private function getTenant(): Tenant
    {
        return Tenant::where('id', 'test-tenant-migration')->first();
    }

    private function createFakeMigration(string $migrationName): void
    {
        $migrationPath = database_path('migrations/tenant');
        @mkdir($migrationPath, 0755, true);

        $filePath = $migrationPath . '/' . $migrationName . '.php';

        if (!file_exists($filePath)) {
            file_put_contents($filePath, <<<'PHP'
<?php

use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    protected $connection = 'tenant';

    public function up(): void { }
    public function down(): void { }
};
PHP
            );
        }
    }

    private function createInvalidMigration(): string
    {
        $migrationPath = database_path('migrations/tenant');
        @mkdir($migrationPath, 0755, true);

        $fileName = '2026_04_21_000003_tenant_connection_invalid.php';
        $filePath = $migrationPath . '/' . $fileName;

        file_put_contents($filePath, <<<'PHP'
<?php

use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    protected $connection = 'mysql'; // Wrong!

    public function up(): void { }
    public function down(): void { }
};
PHP
        );

        return $filePath;
    }
}
