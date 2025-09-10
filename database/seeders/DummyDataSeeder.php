<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Company;
use App\Models\Program;
use App\Models\Host;
use App\Models\User;
use Carbon\Carbon;

class DummyDataSeeder extends Seeder
{
    public function run(): void
    {
        // Get or create a company
        $company = Company::first();
        if (!$company) {
            $company = Company::create([
                'company_name' => 'Sample Training Company',
                'registration_number' => '2023/123456/07',
                'vat_number' => '4123456789',
                'contact_person' => 'John Doe',
                'contact_email' => 'john@samplecompany.co.za',
                'contact_phone' => '+27 11 123 4567',
                'address_line1' => '123 Business Street',
                'city' => 'Johannesburg',
                'province' => 'Gauteng',
                'postal_code' => '2000',
                'country' => 'South Africa',
            ]);
        }

        // Get existing program types or create them
        $programTypes = [
            ['name' => 'Short Course', 'slug' => 'short-course'],
            ['name' => 'Bootcamp', 'slug' => 'bootcamp'],
            ['name' => 'Certification', 'slug' => 'certification'],
        ];

        $createdProgramTypes = [];
        foreach ($programTypes as $typeData) {
            $programType = \App\Models\ProgramType::firstOrCreate(
                ['slug' => $typeData['slug']],
                $typeData
            );
            $createdProgramTypes[] = $programType;
        }

        // Create programs
        $programs = [
            [
                'title' => 'Digital Marketing Fundamentals',
                'program_code' => 'DMF001',
                'duration_weeks' => 8,
                'daily_rate' => 150.00,
                'description' => 'Comprehensive digital marketing course.',
                'status' => 'active',
                'is_approved' => true,
                'max_learners' => 20,
                'enrolled_count' => 0,
                'start_date' => now()->addWeek(),
                'end_date' => now()->addWeeks(9),
                'total_training_days' => 40,
            ],
            [
                'title' => 'Web Development Bootcamp',
                'program_code' => 'WDB001',
                'duration_weeks' => 12,
                'daily_rate' => 200.00,
                'description' => 'Intensive web development program.',
                'status' => 'active',
                'is_approved' => true,
                'max_learners' => 15,
                'enrolled_count' => 0,
                'start_date' => now()->addWeeks(2),
                'end_date' => now()->addWeeks(14),
                'total_training_days' => 60,
            ],
            [
                'title' => 'Data Analysis with Python',
                'program_code' => 'DAP001',
                'duration_weeks' => 10,
                'daily_rate' => 175.00,
                'description' => 'Learn data analysis using Python.',
                'status' => 'active',
                'is_approved' => true,
                'max_learners' => 12,
                'enrolled_count' => 0,
                'start_date' => now()->addWeeks(3),
                'end_date' => now()->addWeeks(13),
                'total_training_days' => 50,
            ],
        ];

        $createdPrograms = [];
        foreach ($programs as $index => $programData) {
            $program = Program::create(array_merge($programData, [
                'company_id' => $company->id,
                'program_type_id' => $createdProgramTypes[$index % count($createdProgramTypes)]->id,
            ]));
            $createdPrograms[] = $program;
        }

        // Create learners
        $learners = [
            ['first_name' => 'Alice', 'last_name' => 'Johnson', 'email' => 'alice@email.com', 'employee_number' => 'EMP001'],
            ['first_name' => 'Bob', 'last_name' => 'Smith', 'email' => 'bob@email.com', 'employee_number' => 'EMP002'],
            ['first_name' => 'Carol', 'last_name' => 'Williams', 'email' => 'carol@email.com', 'employee_number' => 'EMP003'],
        ];

        $createdLearners = [];
        foreach ($learners as $learnerData) {
            $learner = User::firstOrCreate(
                ['email' => $learnerData['email']],
                array_merge($learnerData, [
                    'company_id' => $company->id,
                    'password' => bcrypt('password'),
                    'email_verified_at' => now(),
                    'is_learner' => true,
                    'is_employee' => false,
                ])
            );

            // Assign learner role
            $learner->assignRole('learner');

            $createdLearners[] = $learner;
        }

        // Create company admin user
        $admin = User::firstOrCreate(
            ['employee_number' => 'ADMIN001'],
            [
                'first_name' => 'Admin',
                'last_name' => 'User',
                'email' => 'admin@company.com',
                'employee_number' => 'ADMIN001',
                'company_id' => $company->id,
                'password' => bcrypt('password'),
                'email_verified_at' => now(),
                'is_learner' => false,
                'is_employee' => true,
            ]
        );

        // Assign admin role
        $admin->assignRole('company_admin');

        // Create hosts
        $hosts = [
            [
                'name' => 'Main Training Center - Johannesburg',
                'address_line1' => '456 Education Street',
                'city' => 'Johannesburg',
                'province' => 'Gauteng',
                'postal_code' => '2001',
                'latitude' => -26.2041,
                'longitude' => 28.0473,
                'radius_meters' => 50,
                'check_in_start_time' => '07:00',
                'check_in_end_time' => '09:00',
            ],
            [
                'name' => 'Cape Town Branch Office',
                'address_line1' => '789 Learning Avenue',
                'city' => 'Cape Town',
                'province' => 'Western Cape',
                'postal_code' => '8001',
                'latitude' => -33.9249,
                'longitude' => 18.4241,
                'radius_meters' => 75,
                'check_in_start_time' => '07:30',
                'check_in_end_time' => '09:30',
            ],
        ];

        $createdHosts = [];
        foreach ($hosts as $hostData) {
            $host = Host::create(array_merge($hostData, [
                'company_id' => $company->id,
                'program_id' => $createdPrograms[0]->id,
            ]));
            $createdHosts[] = $host;
        }

        $this->command->info('Dummy data created successfully!');
    }
}
