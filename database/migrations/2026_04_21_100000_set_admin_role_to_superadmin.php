<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Set first admin user to super_admin
        DB::table('admin_users')
            ->whereNotNull('id')
            ->limit(1)
            ->update(['role' => 'super_admin']);
    }

    public function down(): void
    {
        DB::table('admin_users')->update(['role' => 'viewer']);
    }
};
