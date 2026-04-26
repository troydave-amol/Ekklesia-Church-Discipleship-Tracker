<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Canonical tenant roles and dot-style permissions used by central RBAC.
     */
    public function run(): void
    {
        $roleDefinitions = [
            'church_admin' => 'Church Admin',
            'program_leader' => 'Program Leader',
            'cell_leader' => 'Cell Group Leader',
            'disciple' => 'Disciple',
        ];

        $permissionDefinitions = [
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

        $defaultAssignments = [
            'church_admin' => array_keys($permissionDefinitions),
            'program_leader' => [
                'dashboard.view',
                'lessons.manage',
                'attendance.manage',
                'devotionals.manage',
            ],
            'cell_leader' => [
                'dashboard.view',
                'lessons.manage',
                'attendance.manage',
                'devotionals.manage',
            ],
            'disciple' => [
                'dashboard.view',
            ],
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

        $roleIds = [];
        foreach ($roleDefinitions as $slug => $name) {
            DB::connection('tenant')->table('roles')->updateOrInsert(
                ['slug' => $slug],
                ['name' => $name, 'created_at' => now(), 'updated_at' => now()]
            );

            $roleIds[$slug] = DB::connection('tenant')->table('roles')
                ->where('slug', $slug)
                ->value('id');
        }

        foreach ($permissionDefinitions as $slug => $name) {
            DB::connection('tenant')->table('permissions')->updateOrInsert(
                ['slug' => $slug],
                ['name' => $name, 'created_at' => now(), 'updated_at' => now()]
            );
        }

        $legacyPermissionIds = DB::connection('tenant')->table('permissions')
            ->whereIn('slug', $legacyPermissionSlugs)
            ->pluck('id')
            ->all();

        if ($legacyPermissionIds !== []) {
            DB::connection('tenant')->table('permission_role')
                ->whereIn('permission_id', $legacyPermissionIds)
                ->delete();

            DB::connection('tenant')->table('permissions')
                ->whereIn('id', $legacyPermissionIds)
                ->delete();
        }

        $permissionIds = DB::connection('tenant')->table('permissions')
            ->whereIn('slug', array_keys($permissionDefinitions))
            ->pluck('id', 'slug')
            ->all();

        foreach ($defaultAssignments as $roleSlug => $permissionSlugs) {
            $roleId = $roleIds[$roleSlug];

            DB::connection('tenant')->table('permission_role')
                ->where('role_id', $roleId)
                ->delete();

            foreach ($permissionSlugs as $permissionSlug) {
                DB::connection('tenant')->table('permission_role')->insert([
                    'role_id' => $roleId,
                    'permission_id' => $permissionIds[$permissionSlug],
                ]);
            }
        }
    }
}
