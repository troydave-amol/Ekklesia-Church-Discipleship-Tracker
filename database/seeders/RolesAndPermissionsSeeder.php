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

        // Create permissions
        $managePrograms = DB::connection('tenant')->table('permissions')->insertGetId([
            'slug' => 'manage-programs',
            'name' => 'Manage Programs',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $manageLessons = DB::connection('tenant')->table('permissions')->insertGetId([
            'slug' => 'manage-lessons',
            'name' => 'Manage Lessons',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $recordAttendance = DB::connection('tenant')->table('permissions')->insertGetId([
            'slug' => 'record-attendance',
            'name' => 'Record Attendance',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $viewReports = DB::connection('tenant')->table('permissions')->insertGetId([
            'slug' => 'view-reports',
            'name' => 'View Reports',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $manageUsers = DB::connection('tenant')->table('permissions')->insertGetId([
            'slug' => 'manage-users',
            'name' => 'Manage Users',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $manageSubscription = DB::connection('tenant')->table('permissions')->insertGetId([
            'slug' => 'manage-subscription',
            'name' => 'Manage Subscription',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Additional permissions for dashboard access
        $dashboardView = DB::connection('tenant')->table('permissions')->insertGetId([
            'slug' => 'dashboard-view',
            'name' => 'View Dashboard',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $attendanceManage = DB::connection('tenant')->table('permissions')->insertGetId([
            'slug' => 'attendance-manage',
            'name' => 'Manage Attendance',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Assign permissions to roles
        // church_admin gets all permissions
        DB::connection('tenant')->table('permission_role')->insert([
            ['role_id' => $churchAdmin, 'permission_id' => $managePrograms, 'created_at' => now(), 'updated_at' => now()],
            ['role_id' => $churchAdmin, 'permission_id' => $manageLessons, 'created_at' => now(), 'updated_at' => now()],
            ['role_id' => $churchAdmin, 'permission_id' => $recordAttendance, 'created_at' => now(), 'updated_at' => now()],
            ['role_id' => $churchAdmin, 'permission_id' => $viewReports, 'created_at' => now(), 'updated_at' => now()],
            ['role_id' => $churchAdmin, 'permission_id' => $manageUsers, 'created_at' => now(), 'updated_at' => now()],
            ['role_id' => $churchAdmin, 'permission_id' => $manageSubscription, 'created_at' => now(), 'updated_at' => now()],
            ['role_id' => $churchAdmin, 'permission_id' => $dashboardView, 'created_at' => now(), 'updated_at' => now()],
            ['role_id' => $churchAdmin, 'permission_id' => $attendanceManage, 'created_at' => now(), 'updated_at' => now()],
        ]);

        // program_leader gets: manage-programs, manage-lessons, record-attendance, view-reports, attendance-manage
        DB::connection('tenant')->table('permission_role')->insert([
            ['role_id' => $programLeader, 'permission_id' => $managePrograms, 'created_at' => now(), 'updated_at' => now()],
            ['role_id' => $programLeader, 'permission_id' => $manageLessons, 'created_at' => now(), 'updated_at' => now()],
            ['role_id' => $programLeader, 'permission_id' => $recordAttendance, 'created_at' => now(), 'updated_at' => now()],
            ['role_id' => $programLeader, 'permission_id' => $viewReports, 'created_at' => now(), 'updated_at' => now()],
            ['role_id' => $programLeader, 'permission_id' => $dashboardView, 'created_at' => now(), 'updated_at' => now()],
            ['role_id' => $programLeader, 'permission_id' => $attendanceManage, 'created_at' => now(), 'updated_at' => now()],
        ]);

        // cell_leader gets: record-attendance
        DB::connection('tenant')->table('permission_role')->insert([
            ['role_id' => $cellLeader, 'permission_id' => $recordAttendance, 'created_at' => now(), 'updated_at' => now()],
            ['role_id' => $cellLeader, 'permission_id' => $dashboardView, 'created_at' => now(), 'updated_at' => now()],
        ]);

        // disciple gets: dashboard-view (for viewing own progress)
        DB::connection('tenant')->table('permission_role')->insert([
            ['role_id' => $disciple, 'permission_id' => $dashboardView, 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}
