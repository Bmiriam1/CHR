<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Company;
use App\Models\User;
use App\Models\Program;
use App\Models\ProgramType;
use Illuminate\Support\Facades\Hash;

class TestCompanySeeder extends Seeder
{
    /**
     * Run the database seeder.
     */
    public function run(): void
    {
        // Get existing company with PAYE reference 7123456789
        $company = Company::where('paye_reference_number', '7123456789')->first();

        // Create or update admin user
        $admin = User::firstOrCreate(
            ['email' => 'admin@connecthr.co.za'],
            [
                'first_name' => 'Kyle',
                'last_name' => 'Mabaso',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
                'company_id' => $company->id,
                'employee_number' => 'ADM001',
                'id_number' => '8001011234567',
                'tax_number' => 'TAX123456789',
                'phone' => '+27 11 123 4568',
                'birth_date' => '1980-01-01',
                'employment_start_date' => '2023-01-01',
                'res_addr_line1' => '456 Admin Street',
                'res_suburb' => 'Sandton',
                'res_city' => 'Johannesburg',
                'res_postcode' => '2196',
                'is_employee' => true,
                'employment_status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );

        // Create learner user
        User::firstOrCreate(
            ['email' => 'learner@connecthr.co.za'],
            [
                'first_name' => 'Michaela',
                'last_name' => 'McRowdie',
                'email' => 'learner@connecthr.co.za',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
                'company_id' => $company->id,
                'employee_number' => 'CHR001',
                'id_number' => '9501011234567',
                'tax_number' => 'TAX987654321',
                'phone' => '+27 11 123 4569',
                'birth_date' => '1995-01-01',
                'employment_start_date' => '2024-03-01',
                'res_addr_line1' => '789 Learner Avenue',
                'res_suburb' => 'Rosebank',
                'res_city' => 'Johannesburg',
                'res_postcode' => '2196',
                'is_employee' => true,
                'employment_status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );

        // Create program type
        $programType = ProgramType::firstOrCreate(
            ['name' => 'Skills Development'],
            [
                'slug' => 'skills-development',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );

        // Create test program
        Program::firstOrCreate(
            ['program_code' => 'DSDP2024'],
            [
                'title' => 'Digital Skills Development Program',
                'program_code' => 'DSDP2024',
                'description' => 'Comprehensive digital skills training program',
                'company_id' => $company->id,
                'program_type_id' => $programType->id,
                'creator_id' => $admin->id,
                'start_date' => '2024-03-01',
                'end_date' => '2024-12-31',
                'daily_rate' => 350.00,
                'transport_allowance' => 50.00,
                'payment_frequency' => 'monthly',
                'payment_day_of_month' => 25,
                'max_learners' => 50,
                'min_learners' => 5,
                'location_type' => 'hybrid',
                'venue' => 'Connect HR Training Center',
                'venue_address' => '123 Business Park Drive, Sandton',
                'section_12h_eligible' => true,
                'section_12h_contract_number' => 'SEC12H2024001',
                'section_12h_start_date' => '2024-03-01',
                'section_12h_end_date' => '2024-12-31',
                'section_12h_allowance' => 1000.00,
                'eti_eligible_program' => true,
                'eti_category' => 'youth',
                'nqf_level' => 4,
                'saqa_id' => '12345',
                'qualification_title' => 'Certificate in Digital Skills',
                'status' => 'active',
                'is_approved' => true,
                'approved_by' => $admin->id,
                'approved_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );

        $this->command->info('Test company and users created successfully!');
        $this->command->info('Admin: admin@connecthr.co.za (password: password)');
        $this->command->info('Learner: learner@connecthr.co.za (password: password)');
        $this->command->info('Company PAYE Reference: 7123456789');
    }
}
