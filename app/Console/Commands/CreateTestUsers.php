<?php

namespace App\Console\Commands;

use App\Models\Company;
use App\Models\User;
use Illuminate\Console\Command;

class CreateTestUsers extends Command
{
    protected $signature = 'users:create-test';
    protected $description = 'Create test users for login testing';

    public function handle()
    {
        $company = Company::first();

        if (!$company) {
            $this->error('No company found. Please run the seeder first.');
            return;
        }

        // Create learner user
        $learner = User::firstOrCreate(
            ['email' => 'learner@test.com'],
            [
                'first_name' => 'Test',
                'last_name' => 'Learner',
                'employee_number' => 'TEST001',
                'company_id' => $company->id,
                'password' => bcrypt('password'),
                'email_verified_at' => now(),
                'is_learner' => true,
                'is_employee' => false,
            ]
        );

        if (!$learner->hasRole('learner')) {
            $learner->assignRole('learner');
        }

        $this->info('Learner user created: learner@test.com (password: password)');

        // Create admin user
        $admin = User::firstOrCreate(
            ['email' => 'admin@test.com'],
            [
                'first_name' => 'Test',
                'last_name' => 'Admin',
                'employee_number' => 'ADMIN003',
                'company_id' => $company->id,
                'password' => bcrypt('password'),
                'email_verified_at' => now(),
                'is_learner' => false,
                'is_employee' => true,
            ]
        );

        if (!$admin->hasRole('company_admin')) {
            $admin->assignRole('company_admin');
        }

        $this->info('Admin user created: admin@test.com (password: password)');
    }
}
