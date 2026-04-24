<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * This seeder runs on the tenant connection.
     *
     * Required permission slugs (14 total — hyphenated only):
     * manage-programs, manage-lessons, view-all-attendance, view-all-devotionals,
     * manage-users, manage-subscription, create-lesson, edit-own-lesson,
     * manage-disciples, view-programs, enroll-program, view-lessons,
     * update-own-attendance, update-own-devotional
     */
    public function run(): void
    {
        // ── Create roles ───────────────────────────────────────────────────
        $roleDefinitions = [
            'church_admin'   => 'Church Administrator',
            'program_leader' => 'Program Leader',
            'cell_leader'    => 'Cell Leader',
            'disciple'       => 'Disciple',
        ];

        $roleIds = [];
        foreach ($roleDefinitions as $slug => $name) {
            DB::connection('tenant')->table('roles')->updateOrInsert(
                ['slug' => $slug],
                ['name' => $name, 'created_at' => now(), 'updated_at' => now()]
            );
            $roleIds[$slug] = DB::connection('tenant')->table('roles')->where('slug', $slug)->value('id');
        }

        // ── Create all 14 required permissions ─────────────────────────────
        $permissionSlugs = [
            'manage-programs'      => 'Manage Programs',
            'manage-lessons'       => 'Manage Lessons',
            'view-all-attendance'  => 'View All Attendance',
            'view-all-devotionals' => 'View All Devotionals',
            'manage-users'         => 'Manage Users',
            'manage-subscription'  => 'Manage Subscription',
            'create-lesson'        => 'Create Lesson',
            'edit-own-lesson'      => 'Edit Own Lesson',
            'manage-disciples'     => 'Manage Disciples',
            'view-programs'        => 'View Programs',
            'enroll-program'       => 'Enroll Program',
            'view-lessons'         => 'View Lessons',
            'update-own-attendance'  => 'Update Own Attendance',
            'update-own-devotional'  => 'Update Own Devotional',
        ];

        $permissionIds = [];
        foreach ($permissionSlugs as $slug => $name) {
            DB::connection('tenant')->table('permissions')->updateOrInsert(
                ['slug' => $slug],
                ['name' => $name, 'created_at' => now(), 'updated_at' => now()]
            );
            $permissionIds[$slug] = DB::connection('tenant')->table('permissions')->where('slug', $slug)->value('id');
        }

        // ── Role → Permission assignments (EXACT per access matrix) ────────

        // church_admin (pastor) → ALL 14 slugs
        $churchAdminPerms = array_keys($permissionSlugs);

        // program_leader → EXACTLY 8 slugs
        $programLeaderPerms = [
            'manage-lessons',
            'view-all-attendance',
            'view-all-devotionals',
            'create-lesson',
            'edit-own-lesson',
            'manage-disciples',
            'view-programs',
            'view-lessons',
        ];

        // cell_leader → EXACTLY 4 slugs
        $cellLeaderPerms = [
            'view-programs',
            'view-lessons',
            'view-all-attendance',
            'view-all-devotionals',
        ];

        // disciple → EXACTLY 5 slugs
        $disciplePerms = [
            'view-programs',
            'enroll-program',
            'view-lessons',
            'update-own-attendance',
            'update-own-devotional',
        ];

        $assignments = [
            'church_admin'   => $churchAdminPerms,
            'program_leader' => $programLeaderPerms,
            'cell_leader'    => $cellLeaderPerms,
            'disciple'       => $disciplePerms,
        ];

        // Clear existing assignments and re-seed for idempotency
        foreach ($assignments as $roleSlug => $perms) {
            $roleId = $roleIds[$roleSlug];

            // Remove any stale/incorrect assignments for this role
            DB::connection('tenant')->table('permission_role')
                ->where('role_id', $roleId)
                ->delete();

            // Insert the correct assignments
            foreach ($perms as $permSlug) {
                if (isset($permissionIds[$permSlug])) {
                    DB::connection('tenant')->table('permission_role')->insert([
                        'role_id'       => $roleId,
                        'permission_id' => $permissionIds[$permSlug],
                    ]);
                }
            }
        }
    }
}
