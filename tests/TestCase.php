<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Schema\Blueprint;

abstract class TestCase extends BaseTestCase
{
    /**
     * Ensure tenant migrations are run once for the test process so
     * tenant-scoped tables exist when tests create tenant data.
     */
    protected static bool $migratedTenant = false;

    protected function setUp(): void
    {
        parent::setUp();

        // Ensure central tenants table exists (some test runners execute single files
        // which may skip the normal migration flow). Run the specific migration
        // that creates the tenants table if it's missing.
        if (! Schema::hasTable('tenants')) {
            Artisan::call('migrate', [
                '--path' => 'database/migrations/2019_09_15_000010_create_tenants_table.php',
                '--force' => true,
            ]);
        } else {
            // Ensure a clean tenants table for tests that expect a fresh central DB
            DB::table('tenants')->delete();
        }

        if (! static::$migratedTenant) {
            Artisan::call('migrate', [
                '--path' => 'database/migrations/tenant',
                '--database' => 'tenant',
                '--force' => true,
            ]);

            static::$migratedTenant = true;
        }

        // Ensure a central `users` table exists for legacy auth feature tests
        if (! Schema::hasTable('users')) {
            Schema::create('users', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('church_id')->nullable();
                $table->string('first_name');
                $table->string('last_name');
                $table->string('nickname')->nullable();
                $table->string('email')->unique();
                $table->timestamp('email_verified_at')->nullable();
                $table->string('password');
                $table->string('role')->default('disciple');
                $table->rememberToken();
                $table->timestamps();
            });
        }

        // Ensure sessions table exists for the `database` session driver used in tests
        if (! Schema::hasTable('sessions')) {
            Schema::create('sessions', function (Blueprint $table) {
                $table->string('id')->primary();
                $table->foreignId('user_id')->nullable()->index();
                $table->string('ip_address', 45)->nullable();
                $table->text('user_agent')->nullable();
                $table->longText('payload');
                $table->integer('last_activity')->index();
            });
        }
    }
}
