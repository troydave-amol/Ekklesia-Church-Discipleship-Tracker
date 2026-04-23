<?php

namespace Database\Seeders;

use App\Models\AdminUser;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * Seeds initial super_admin user for central admin dashboard.
     * This should be run when first setting up the application.
     *
     * Usage: php artisan db:seed --class=AdminSeeder
     * Or include in DatabaseSeeder.php via $this->call(AdminSeeder::class);
     */
    public function run(): void
    {
        // Check if a super_admin already exists
        $superAdminExists = AdminUser::where('role', 'super_admin')
            ->where('status', 'active')
            ->exists();

        if ($superAdminExists) {
            $this->command->info('Super admin already exists. Skipping seeding.');
            return;
        }

        // Create initial super_admin user
        $admin = AdminUser::create([
            'name' => 'System Administrator',
            'email' => 'admin@church.local',
            'password' => Hash::make('admin@123'),
            'role' => 'super_admin',
            'status' => 'active',
        ]);

        $this->command->info("Super admin user created successfully!");
        $this->command->info("Email: {$admin->email}");
        $this->command->info("Password: admin@123 (please change on first login)");
        $this->command->warn("⚠️  Remember to change the default password immediately!");
    }
}
