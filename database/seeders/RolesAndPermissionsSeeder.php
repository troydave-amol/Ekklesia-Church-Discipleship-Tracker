<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * This seeder runs on the tenant connection.
     */
    public function run(): void
    {
        // Create roles
        $churchAdmin = DB::connection('tenant')->table('roles')->insertGetId([
            'slug' => 'church_admin',
            'name' => 'Church Administrator',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $programLeader = DB::connection('tenant')->table('roles')->insertGetId([
            'slug' => 'program_leader',
            'name' => 'Program Leader',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $cellLeader = DB::connection('tenant')->table('roles')->insertGetId([
            'slug' => 'cell_leader',
            'name' => 'Cell Leader',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $disciple = DB::connection('tenant')->table('roles')->insertGetId([
            'slug' => 'disciple',
            'name' => 'Disciple',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Create permissions (exact slugs required by access matrix)
        $permissionSlugs = [
            'manage-programs' => 'Manage Programs',
            'manage-lessons' => 'Manage Lessons',
            'view-all-attendance' => 'View All Attendance',
            'view-all-devotionals' => 'View All Devotionals',
            'manage-users' => 'Manage Users',
            'manage-subscription' => 'Manage Subscription',
            'create-lesson' => 'Create Lesson',
            'edit-own-lesson' => 'Edit Own Lesson',
            'manage-disciples' => 'Manage Disciples',
            'view-programs' => 'View Programs',
            'enroll-program' => 'Enroll Program',
            'view-lessons' => 'View Lessons',
            'update-own-attendance' => 'Update Own Attendance',
            'update-own-devotional' => 'Update Own Devotional',
        ];

        $permissionIds = [];
        foreach ($permissionSlugs as $slug => $name) {
            $permissionIds[$slug] = DB::connection('tenant')->table('permissions')->insertGetId([
                'slug' => $slug,
                'name' => $name,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Assign permissions to roles
        // Assign permissions to roles according to access matrix
        $assign = [];

        // church_admin (pastor) gets all permissions
        foreach (array_keys($permissionSlugs) as $slug) {
            $assign[] = ['role_id' => $churchAdmin, 'permission_id' => $permissionIds[$slug], 'created_at' => now(), 'updated_at' => now()];
        }

        // program_leader
        $programLeaderPerms = [
            'create-lesson', 'edit-own-lesson', 'view-all-attendance', 'view-all-devotionals', 'manage-disciples', 'view-programs', 'view-lessons', 'enroll-program', 'update-own-attendance', 'update-own-devotional'
        ];
        foreach ($programLeaderPerms as $slug) {
            if (isset($permissionIds[$slug])) {
                $assign[] = ['role_id' => $programLeader, 'permission_id' => $permissionIds[$slug], 'created_at' => now(), 'updated_at' => now()];
            }
        }

        // cell_leader: limited permissions
        $cellLeaderPerms = ['view-programs', 'view-lessons', 'update-own-attendance', 'update-own-devotional'];
        foreach ($cellLeaderPerms as $slug) {
            if (isset($permissionIds[$slug])) {
                $assign[] = ['role_id' => $cellLeader, 'permission_id' => $permissionIds[$slug], 'created_at' => now(), 'updated_at' => now()];
            }
        }

        // disciple
        $disciplePerms = ['view-programs', 'enroll-program', 'view-lessons', 'update-own-attendance', 'update-own-devotional'];
        foreach ($disciplePerms as $slug) {
            if (isset($permissionIds[$slug])) {
                $assign[] = ['role_id' => $disciple, 'permission_id' => $permissionIds[$slug], 'created_at' => now(), 'updated_at' => now()];
            }
        }

        DB::connection('tenant')->table('permission_role')->insert($assign);
    }
}
