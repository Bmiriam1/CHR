<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Company;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class EmployeeTestSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $companies = Company::all();

        if ($companies->isEmpty()) {
            $this->command->error('No companies found. Please run the main seeder first.');
            return;
        }

        $this->createTestEmployees($companies);
        $this->createTestLearners($companies);
        $this->createTestManagers($companies);

        $this->command->info('Test employees created successfully!');
    }

    private function createTestEmployees($companies): void
    {
        $employeeData = [
            [
                'first_name' => 'John',
                'last_name' => 'Smith',
                'email' => 'john.smith@test.com',
                'employee_number' => 'EMP001',
                'id_number' => '8501015009087',
                'phone' => '+27123456795',
                'occupation' => 'Software Developer',
                'employment_basis' => 'full_time',
            ],
            [
                'first_name' => 'Sarah',
                'last_name' => 'Johnson',
                'email' => 'sarah.johnson@test.com',
                'employee_number' => 'EMP002',
                'id_number' => '9002155009087',
                'phone' => '+27123456796',
                'occupation' => 'Project Manager',
                'employment_basis' => 'full_time',
            ],
        ];

        foreach ($employeeData as $index => $data) {
            $company = $companies[$index % $companies->count()];

            User::firstOrCreate(
                ['employee_number' => $data['employee_number']],
                array_merge($data, [
                    'company_id' => $company->id,
                    'password' => Hash::make('password'),
                    'email_verified_at' => now(),
                    'is_employee' => true,
                    'is_learner' => false,
                    'employment_start_date' => Carbon::now()->subMonths(rand(6, 24)),
                    'employment_status' => 'active',
                    'birth_date' => $this->calculateBirthDate($data['id_number']),
                    'gender' => $this->getGenderFromIdNumber($data['id_number']),
                    'marital_status' => ['single', 'married', 'divorced'][rand(0, 2)],
                    'citizenship_status' => 'citizen',
                    'bank_name' => 'Standard Bank',
                    'bank_account_number' => '1234567890',
                    'bank_branch_code' => '051001',
                    'tax_number' => '9012345678',
                    'res_addr_line1' => '123 Test Street ' . ($index + 1),
                    'res_suburb' => 'Test Suburb',
                    'res_city' => 'Johannesburg',
                    'res_postcode' => '2000',
                ])
            );
        }
    }

    private function createTestLearners($companies): void
    {
        $learnerData = [
            [
                'first_name' => 'Thabo',
                'last_name' => 'Mthembu',
                'email' => 'thabo.mthembu@test.com',
                'employee_number' => 'LRN001',
                'id_number' => '9505145009087',
                'phone' => '+27123456797',
                'occupation' => 'Software Development Learner',
                'employment_basis' => 'learner',
            ],
            [
                'first_name' => 'Nomsa',
                'last_name' => 'Nkosi',
                'email' => 'nomsa.nkosi@test.com',
                'employee_number' => 'LRN002',
                'id_number' => '9807235009087',
                'phone' => '+27123456798',
                'occupation' => 'Data Science Learner',
                'employment_basis' => 'learner',
            ],
        ];

        foreach ($learnerData as $index => $data) {
            $company = $companies[$index % $companies->count()];

            $learner = User::firstOrCreate(
                ['employee_number' => $data['employee_number']],
                array_merge($data, [
                    'company_id' => $company->id,
                    'password' => Hash::make('password'),
                    'email_verified_at' => now(),
                    'is_employee' => false,
                    'is_learner' => true,
                    'employment_start_date' => Carbon::now()->subMonths(rand(1, 12)),
                    'employment_status' => 'active',
                    'birth_date' => $this->calculateBirthDate($data['id_number']),
                    'gender' => $this->getGenderFromIdNumber($data['id_number']),
                    'marital_status' => ['single', 'married'][rand(0, 1)],
                    'citizenship_status' => 'citizen',
                    'bank_name' => 'Standard Bank',
                    'bank_account_number' => '6789012345',
                    'bank_branch_code' => '051001',
                    'tax_number' => '9012345683',
                    'res_addr_line1' => '456 Learner Street ' . ($index + 1),
                    'res_suburb' => 'Learner Suburb',
                    'res_city' => 'Cape Town',
                    'res_postcode' => '8000',
                ])
            );

            $learner->assignRole('learner');
        }
    }

    private function createTestManagers($companies): void
    {
        $managerData = [
            [
                'first_name' => 'Jennifer',
                'last_name' => 'Taylor',
                'email' => 'jennifer.taylor@test.com',
                'employee_number' => 'MGR001',
                'id_number' => '8203105009087',
                'phone' => '+27123456799',
                'occupation' => 'HR Manager',
                'employment_basis' => 'full_time',
            ],
        ];

        foreach ($managerData as $index => $data) {
            $company = $companies[$index % $companies->count()];

            $manager = User::firstOrCreate(
                ['employee_number' => $data['employee_number']],
                array_merge($data, [
                    'company_id' => $company->id,
                    'password' => Hash::make('password'),
                    'email_verified_at' => now(),
                    'is_employee' => true,
                    'is_learner' => false,
                    'employment_start_date' => Carbon::now()->subMonths(rand(12, 36)),
                    'employment_status' => 'active',
                    'birth_date' => $this->calculateBirthDate($data['id_number']),
                    'gender' => $this->getGenderFromIdNumber($data['id_number']),
                    'marital_status' => ['single', 'married', 'divorced'][rand(0, 2)],
                    'citizenship_status' => 'citizen',
                    'bank_name' => 'Standard Bank',
                    'bank_account_number' => '1234567890',
                    'bank_branch_code' => '051001',
                    'tax_number' => '9012345688',
                    'res_addr_line1' => '789 Manager Street ' . ($index + 1),
                    'res_suburb' => 'Manager Suburb',
                    'res_city' => 'Durban',
                    'res_postcode' => '4000',
                ])
            );

            $manager->assignRole('hr_manager');
        }
    }

    private function calculateBirthDate($idNumber): string
    {
        $year = '19' . substr($idNumber, 0, 2);
        $month = substr($idNumber, 2, 2);
        $day = substr($idNumber, 4, 2);

        return $year . '-' . $month . '-' . $day;
    }

    private function getGenderFromIdNumber($idNumber): string
    {
        $genderDigit = (int) substr($idNumber, 6, 1);
        return $genderDigit >= 5 ? 'male' : 'female';
    }
}
