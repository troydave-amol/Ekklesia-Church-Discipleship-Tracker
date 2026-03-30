<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class TenantDefaultProgramsSeeder extends Seeder
{
    public function run(): void
    {
        $definitions = [
            ['program_key' => 'life_class', 'name' => 'Life Class', 'slug' => 'life-class', 'description' => 'Foundation class for new members.'],
            ['program_key' => 'sol1', 'name' => 'School of Leaders 1', 'slug' => 'sol-1', 'description' => 'Equipping disciples for leadership.'],
            ['program_key' => 'sol2', 'name' => 'School of Leaders 2', 'slug' => 'sol-2', 'description' => 'Advanced leadership formation.'],
            ['program_key' => 'sol3', 'name' => 'School of Leaders 3', 'slug' => 'sol-3', 'description' => 'Senior leadership development.'],
        ];

        $churchIds = DB::table('churches')->pluck('id');
        if ($churchIds->isEmpty()) {
            return;
        }

        $hasActive = Schema::hasColumn('programs', 'active');
        $hasIsActive = Schema::hasColumn('programs', 'is_active');

        foreach ($churchIds as $churchId) {
            foreach ($definitions as $program) {
                $payload = [
                    'church_id' => $churchId,
                    'program_key' => $program['program_key'],
                    'name' => $program['name'],
                    'slug' => "{$program['slug']}-{$churchId}",
                    'description' => $program['description'],
                    'updated_at' => now(),
                ];

                if ($hasActive) {
                    $payload['active'] = true;
                }
                if ($hasIsActive) {
                    $payload['is_active'] = true;
                }

                DB::table('programs')->updateOrInsert(
                    ['church_id' => $churchId, 'program_key' => $program['program_key']],
                    $payload + ['created_at' => now()]
                );
            }
        }
    }
}
