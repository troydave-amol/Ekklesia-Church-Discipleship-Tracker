<?php

namespace App\Console\Commands;

use App\Models\AdminUser;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class CreateAdminUser extends Command
{
    protected $signature = 'app:create-admin 
        {--name= : Admin name}
        {--email= : Admin email}
        {--password= : Admin password}
    ';

    protected $description = 'Create an initial admin user for central administration';

    public function handle(): int
    {
        $name = $this->option('name') ?? $this->ask('Enter admin name');
        $email = $this->option('email') ?? $this->ask('Enter admin email');
        $password = $this->option('password') ?? $this->secret('Enter admin password');

        if (! $name || ! $email || ! $password) {
            $this->error('All fields are required.');
            return self::FAILURE;
        }

        if (AdminUser::where('email', $email)->exists()) {
            $this->error('Admin user with this email already exists.');
            return self::FAILURE;
        }

        try {
            $admin = AdminUser::create([
                'name'     => $name,
                'email'    => $email,
                'password' => Hash::make($password),
                'role'     => 'superadmin',
                'is_active' => true,
            ]);

            $this->info("Admin user '{$admin->name}' created successfully!");
            $this->line("Email: {$admin->email}");
            $this->line("Role: {$admin->role}");

            return self::SUCCESS;
        } catch (\Exception $e) {
            $this->error('Error creating admin user: '.$e->getMessage());
            return self::FAILURE;
        }
    }
}
