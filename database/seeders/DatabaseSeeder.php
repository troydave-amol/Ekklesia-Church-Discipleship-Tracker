<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\Church;
use App\Models\User;
use App\Models\UserProfile;
use App\Models\ChurchMembership;
use App\Models\Program;
use App\Models\Lesson;
use App\Models\Attendance;
use App\Models\Devotional;
use Carbon\Carbon;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // ── 1. Church ──────────────────────────────────────────────
        $church = Church::create([
            'name'                => 'Grace Community Church',
            'subscription_plan'   => 'standard',
            'subscription_status' => 'active',
        ]);

        // ── 2. Pastor ──────────────────────────────────────────────
        $pastor = User::create([
            'church_id'  => $church->id,
            'first_name' => 'Juan',
            'last_name'  => 'dela Cruz',
            'nickname'   => 'Kuya Juan',
            'email'      => 'pastor@grace.com',
            'password'   => Hash::make('password'),
            'role'       => 'pastor',
        ]);
        UserProfile::create(['user_id' => $pastor->id, 'gender' => 'male', 'city' => 'Davao City']);
        ChurchMembership::create([
            'user_id'    => $pastor->id,
            'church_id'  => $church->id,
            'status'     => 'active',
            'joined_at'  => now()->subYears(5),
        ]);

        // ── 3. Leaders ─────────────────────────────────────────────
        $leadersData = [
            ['first_name' => 'Maria',  'last_name' => 'Santos',   'email' => 'maria@grace.com'],
            ['first_name' => 'Pedro',  'last_name' => 'Reyes',    'email' => 'pedro@grace.com'],
        ];

        $leaders = [];
        foreach ($leadersData as $ld) {
            $leader = User::create([
                'church_id'  => $church->id,
                'first_name' => $ld['first_name'],
                'last_name'  => $ld['last_name'],
                'email'      => $ld['email'],
                'password'   => Hash::make('password'),
                'role'       => 'leader',
            ]);
            UserProfile::create(['user_id' => $leader->id]);
            ChurchMembership::create([
                'user_id'   => $leader->id,
                'church_id' => $church->id,
                'status'    => 'active',
                'joined_at' => now()->subYears(2),
            ]);
            $leaders[] = $leader;
        }

        // ── 4. Disciples ───────────────────────────────────────────
        $disciplesData = [
            ['first_name' => 'Ana',      'last_name' => 'Lim',      'email' => 'ana@grace.com',      'level' => 'life_class', 'leader' => 0],
            ['first_name' => 'Carlo',    'last_name' => 'Bautista',  'email' => 'carlo@grace.com',    'level' => 'life_class', 'leader' => 0],
            ['first_name' => 'Diana',    'last_name' => 'Torres',    'email' => 'diana@grace.com',    'level' => 'sol1',       'leader' => 0],
            ['first_name' => 'Eduardo',  'last_name' => 'Mendoza',   'email' => 'eduardo@grace.com',  'level' => 'sol1',       'leader' => 1],
            ['first_name' => 'Fatima',   'last_name' => 'Garcia',    'email' => 'fatima@grace.com',   'level' => 'sol2',       'leader' => 1],
            ['first_name' => 'Gabriel',  'last_name' => 'Ramos',     'email' => 'gabriel@grace.com',  'level' => 'sol2',       'leader' => 1],
        ];

        $disciples = [];
        foreach ($disciplesData as $dd) {
            $disc = User::create([
                'church_id'  => $church->id,
                'first_name' => $dd['first_name'],
                'last_name'  => $dd['last_name'],
                'email'      => $dd['email'],
                'password'   => Hash::make('password'),
                'role'       => 'disciple',
            ]);
            UserProfile::create(['user_id' => $disc->id]);
            ChurchMembership::create([
                'user_id'                 => $disc->id,
                'church_id'               => $church->id,
                'leader_id'               => $leaders[$dd['leader']]->id,
                'status'                  => 'active',
                'destiny_training_level'  => $dd['level'],
                'joined_at'               => now()->subMonths(rand(3, 18)),
                'baptized_at'             => now()->subMonths(rand(1, 12)),
            ]);
            $disciples[] = $disc;
        }

        // ── 5. Programs ────────────────────────────────────────────
        $programDefs = [
            ['key' => 'life_class', 'name' => 'Life Class',           'desc' => 'Foundation class for new members.'],
            ['key' => 'sol1',       'name' => 'School of Leaders 1',  'desc' => 'Equipping disciples for leadership.'],
            ['key' => 'sol2',       'name' => 'School of Leaders 2',  'desc' => 'Advanced leadership formation.'],
            ['key' => 'sol3',       'name' => 'School of Leaders 3',  'desc' => 'Senior leadership development.'],
        ];

        $programs = [];
        foreach ($programDefs as $pd) {
            $programs[$pd['key']] = Program::create([
                'church_id'   => $church->id,
                'name'        => $pd['name'],
                'description' => $pd['desc'],
                'program_key' => $pd['key'],
                'active'      => true,
            ]);
        }

        // ── 6. Lessons ─────────────────────────────────────────────
        $lessonTitles = [
            'life_class' => ['Who Is God?', 'Salvation & Grace', 'The Holy Spirit', 'Prayer & Worship', 'The Church', 'Christian Living'],
            'sol1'       => ['Foundations of Leadership', 'Servant Heart', 'Discipleship Basics', 'Spiritual Disciplines'],
            'sol2'       => ['Vision & Mission', 'Building Small Groups', 'Conflict Resolution', 'Multiplication'],
            'sol3'       => ['Church Planting', 'Strategic Leadership', 'Coaching Others', 'Legacy & Succession'],
        ];

        $allLessons = [];
        foreach ($lessonTitles as $key => $titles) {
            foreach ($titles as $i => $title) {
                $allLessons[$key][] = Lesson::create([
                    'church_id'   => $church->id,
                    'program_id'  => $programs[$key]->id,
                    'week_number' => $i + 1,
                    'title'       => $title,
                    'content'     => "This is the lesson content for Week " . ($i + 1) . ": {$title}. Discuss key scripture passages and application points.",
                ]);
            }
        }

        // ── 7. Attendance (past 4 weeks) ───────────────────────────
        $levelMap = ['life_class' => 'life_class', 'sol1' => 'sol1', 'sol2' => 'sol2', 'sol3' => 'sol3'];

        foreach ($disciples as $disciple) {
            $level = $disciple->membership->destiny_training_level;
            $lessons = ($level && isset($allLessons[$level->value])) ? $allLessons[$level->value] : [];

            foreach ($lessons as $lesson) {
                $date = now()->subWeeks(rand(0, 4))->startOfWeek();
                Attendance::create([
                    'church_id'   => $church->id,
                    'user_id'     => $disciple->id,
                    'lesson_id'   => $lesson->id,
                    'date'        => $date,
                    'status'      => rand(0, 100) > 20 ? 'present' : 'absent',
                    'recorded_by' => $leaders[0]->id,
                ]);
            }
        }

        // ── 8. Devotionals (past 30 days) ──────────────────────────
        foreach ($disciples as $disciple) {
            for ($d = 30; $d >= 0; $d--) {
                $date = now()->subDays($d)->toDateString();
                // 75% chance of completing devotional
                if (rand(0, 100) > 25) {
                    Devotional::create([
                        'church_id' => $church->id,
                        'user_id'   => $disciple->id,
                        'date'      => $date,
                        'completed' => true,
                    ]);
                }
            }
        }

        $this->command->info('✅ Seed complete!');
        $this->command->table(
            ['Role', 'Email', 'Password'],
            [
                ['Pastor',     'pastor@grace.com',   'password'],
                ['Leader',     'maria@grace.com',    'password'],
                ['Leader',     'pedro@grace.com',    'password'],
                ['Disciple',   'ana@grace.com',      'password'],
                ['Disciple',   'carlo@grace.com',    'password'],
                ['Disciple',   'diana@grace.com',    'password'],
            ]
        );
    }
}
