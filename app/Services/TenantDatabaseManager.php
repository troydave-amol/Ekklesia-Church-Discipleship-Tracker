<?php

namespace App\Services;

use App\Models\Tenant;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Throwable;

class TenantDatabaseManager
{
    // ── Create the physical MySQL database ────────────────────
    public function createDatabase(Tenant $tenant): void
    {
        $dbName = $this->sanitizeDbName($tenant->db_name);

        DB::statement(
            "CREATE DATABASE IF NOT EXISTS `{$dbName}`
             CHARACTER SET utf8mb4
             COLLATE utf8mb4_unicode_ci"
        );

        Log::info("Tenant database created: {$dbName}", [
            'tenant_id' => $tenant->id,
        ]);
    }

    // ── Wire up a dynamic connection then run migrations ──────
    public function runMigrations(Tenant $tenant): void
    {
        $dbName     = $this->sanitizeDbName($tenant->db_name);
        $connName   = $this->connectionName($tenant);

        // Dynamically register the tenant DB connection at runtime
        Config::set("database.connections.{$connName}", [
            'driver'         => 'mysql',
            'host'           => config('database.connections.mysql.host'),
            'port'           => config('database.connections.mysql.port'),
            'database'       => $dbName,
            'username'       => config('database.connections.mysql.username'),
            'password'       => config('database.connections.mysql.password'),
            'charset'        => 'utf8mb4',
            'collation'      => 'utf8mb4_unicode_ci',
            'prefix'         => '',
            'prefix_indexes' => true,
            'strict'         => true,
            'engine'         => null,
        ]);

        // Force Laravel to use the new connection config
        DB::purge($connName);
        DB::reconnect($connName);

        // Run migrations from the tenant folder only
        Artisan::call('migrate', [
            '--database' => $connName,
            '--path'     => 'database/migrations/tenant',
            '--force'    => true,
        ]);

        $output = Artisan::output();

        Log::info("Tenant migrations ran for: {$dbName}", [
            'tenant_id' => $tenant->id,
            'output'    => $output,
        ]);
    }

    // ── Roll back tenant migrations (for testing/cleanup) ─────
    public function rollbackMigrations(Tenant $tenant): void
    {
        $connName = $this->connectionName($tenant);

        Artisan::call('migrate:rollback', [
            '--database' => $connName,
            '--path'     => 'database/migrations/tenant',
            '--force'    => true,
        ]);
    }

    // ── Drop the physical database (cleanup on failed signup) ─
    public function dropDatabase(Tenant $tenant): void
    {
        $dbName = $this->sanitizeDbName($tenant->db_name);

        DB::statement("DROP DATABASE IF EXISTS `{$dbName}`");

        Log::info("Tenant database dropped: {$dbName}", [
            'tenant_id' => $tenant->id,
        ]);
    }

    // ── Check if the database already exists ──────────────────
    public function databaseExists(Tenant $tenant): bool
    {
        $dbName = $this->sanitizeDbName($tenant->db_name);

        $result = DB::select(
            "SELECT SCHEMA_NAME FROM information_schema.SCHEMATA
             WHERE SCHEMA_NAME = ?",
            [$dbName]
        );

        return count($result) > 0;
    }

    // ── Helpers ───────────────────────────────────────────────

    // Consistent connection name per tenant
    private function connectionName(Tenant $tenant): string
    {
        return "tenant_{$tenant->id}";
    }

    // Strip anything unsafe from DB names
    private function sanitizeDbName(string $name): string
    {
        return preg_replace('/[^a-zA-Z0-9_]/', '_', $name);
    }
}