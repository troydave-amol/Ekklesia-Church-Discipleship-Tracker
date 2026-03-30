<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $driver = DB::getDriverName();

        // SQLite stores enums as strings; no ALTER needed.
        if ($driver !== 'mysql' && $driver !== 'mariadb') {
            return;
        }

        DB::statement("ALTER TABLE tenants MODIFY COLUMN subscription_plan ENUM('free','basic','standard','premium') NOT NULL DEFAULT 'basic'");
        DB::statement("ALTER TABLE tenants MODIFY COLUMN status ENUM('active','inactive','suspended','pending') NOT NULL DEFAULT 'active'");
    }

    public function down(): void
    {
        $driver = DB::getDriverName();

        if ($driver === 'mysql' || $driver === 'mariadb') {
            DB::statement("UPDATE tenants SET subscription_plan = 'basic' WHERE subscription_plan = 'free'");
            DB::statement("UPDATE tenants SET status = 'inactive' WHERE status = 'pending'");
            DB::statement("ALTER TABLE tenants MODIFY COLUMN subscription_plan ENUM('basic','standard','premium') NOT NULL DEFAULT 'basic'");
            DB::statement("ALTER TABLE tenants MODIFY COLUMN status ENUM('active','inactive','suspended') NOT NULL DEFAULT 'active'");
        }
    }
};
