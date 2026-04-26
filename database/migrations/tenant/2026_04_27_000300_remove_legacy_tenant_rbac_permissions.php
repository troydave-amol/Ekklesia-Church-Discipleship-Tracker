<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $canonicalPermissions = [
            'dashboard.view' => 'View dashboard',
            'church.manage' => 'Manage church settings',
            'users.manage' => 'Manage users',
            'programs.manage' => 'Manage programs',
            'lessons.manage' => 'Manage lessons',
            'attendance.manage' => 'Manage attendance',
            'devotionals.manage' => 'Manage devotionals',
            'reports.view' => 'View reports',
            'reports.export' => 'Export reports',
        ];

        $legacyPermissionSlugs = [
            'manage-programs',
            'manage-lessons',
            'view-all-attendance',
            'view-all-devotionals',
            'manage-users',
            'manage-subscription',
            'create-lesson',
            'edit-own-lesson',
            'manage-disciples',
            'view-programs',
            'enroll-program',
            'view-lessons',
            'update-own-attendance',
            'update-own-devotional',
        ];

        foreach ($canonicalPermissions as $slug => $name) {
            DB::table('permissions')->updateOrInsert(
                ['slug' => $slug],
                ['name' => $name, 'created_at' => now(), 'updated_at' => now()]
            );
        }

        $legacyPermissionIds = DB::table('permissions')
            ->whereIn('slug', $legacyPermissionSlugs)
            ->pluck('id')
            ->all();

        if ($legacyPermissionIds !== []) {
            DB::table('permission_role')
                ->whereIn('permission_id', $legacyPermissionIds)
                ->delete();

            DB::table('permissions')
                ->whereIn('id', $legacyPermissionIds)
                ->delete();
        }
    }

    public function down(): void
    {
        // Legacy permission cleanup is intentionally not reversed.
    }
};
