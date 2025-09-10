<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            RolePermissionSeeder::class,
            ComplianceTestSeeder::class,
            EmployeeTestSeeder::class,
        ]);

        // Create additional demo accounts for testing
        User::create([
            'first_name' => 'Demo',
            'last_name' => 'Learner',
            'email' => 'learner@demo.com',
            'email_verified_at' => now(),
            'password' => bcrypt('password'),
            'is_learner' => true,
            'employee_number' => 'DEMO001',
            'employment_status' => 'active',
            'employment_basis' => 'learner',
        ]);

        User::create([
            'first_name' => 'Demo',
            'last_name' => 'Admin',
            'email' => 'admin@demo.com',
            'email_verified_at' => now(),
            'password' => bcrypt('password'),
            'is_employee' => true,
            'employee_number' => 'ADMIN999',
            'employment_status' => 'active',
            'employment_basis' => 'full_time',
            'occupation' => 'Administrator',
        ]);
    }
}
