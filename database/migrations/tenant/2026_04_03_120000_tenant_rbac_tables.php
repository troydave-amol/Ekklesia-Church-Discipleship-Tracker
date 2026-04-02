<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('roles', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();
            $table->string('name');
            $table->timestamps();
        });

        Schema::create('permissions', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();
            $table->string('name');
            $table->timestamps();
        });

        Schema::create('permission_role', function (Blueprint $table) {
            $table->foreignId('permission_id')->constrained('permissions')->cascadeOnDelete();
            $table->foreignId('role_id')->constrained('roles')->cascadeOnDelete();
            $table->primary(['permission_id', 'role_id']);
        });

        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('role_id')->nullable()->after('role')->constrained('roles')->nullOnDelete();
        });

        $now = now();

        DB::table('roles')->insert([
            ['slug' => 'church_admin', 'name' => 'Church Admin', 'created_at' => $now, 'updated_at' => $now],
            ['slug' => 'program_leader', 'name' => 'Program Leader', 'created_at' => $now, 'updated_at' => $now],
            ['slug' => 'cell_leader', 'name' => 'Cell Group Leader', 'created_at' => $now, 'updated_at' => $now],
            ['slug' => 'disciple', 'name' => 'Disciple', 'created_at' => $now, 'updated_at' => $now],
        ]);

        $roleIds = DB::table('roles')->pluck('id', 'slug')->all();

        $permRows = [
            ['slug' => 'dashboard.view', 'name' => 'View dashboard'],
            ['slug' => 'church.manage', 'name' => 'Manage church settings'],
            ['slug' => 'users.manage', 'name' => 'Manage users'],
            ['slug' => 'programs.manage', 'name' => 'Manage programs'],
            ['slug' => 'lessons.manage', 'name' => 'Manage lessons'],
            ['slug' => 'attendance.manage', 'name' => 'Manage attendance'],
            ['slug' => 'devotionals.manage', 'name' => 'Manage devotionals'],
            ['slug' => 'reports.view', 'name' => 'View reports'],
            ['slug' => 'reports.export', 'name' => 'Export reports'],
        ];

        foreach ($permRows as $p) {
            DB::table('permissions')->insert([
                'slug' => $p['slug'],
                'name' => $p['name'],
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }

        $permIds = DB::table('permissions')->pluck('id', 'slug')->all();

        $assign = function (string $roleSlug, array $permSlugs) use ($roleIds, $permIds) {
            $rid = $roleIds[$roleSlug];
            foreach ($permSlugs as $ps) {
                DB::table('permission_role')->insert([
                    'role_id' => $rid,
                    'permission_id' => $permIds[$ps],
                ]);
            }
        };

        $all = array_keys($permIds);
        $assign('church_admin', $all);

        $leaderPerms = [
            'dashboard.view', 'attendance.manage', 'devotionals.manage', 'lessons.manage',
        ];
        $assign('program_leader', $leaderPerms);
        $assign('cell_leader', $leaderPerms);

        $assign('disciple', ['dashboard.view']);

        DB::table('users')->where('role', 'pastor')->update(['role' => 'church_admin']);
        DB::table('users')->where('role', 'leader')->update(['role' => 'program_leader']);

        foreach (['church_admin', 'program_leader', 'cell_leader', 'disciple'] as $slug) {
            $rid = $roleIds[$slug];
            DB::table('users')->where('role', $slug)->update(['role_id' => $rid]);
        }
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['role_id']);
            $table->dropColumn('role_id');
        });

        Schema::dropIfExists('permission_role');
        Schema::dropIfExists('permissions');
        Schema::dropIfExists('roles');
    }
};
