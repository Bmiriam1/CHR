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
        // Create Connect HR company based on IRP5 data
        $company = Company::firstOrCreate(
            ['paye_reference_number' => '7080824016'],
            [
                'name' => 'Connect HR',
                'display_name' => 'Connect HR',
                'trading_name' => 'Connect HR',
                'paye_reference_number' => '7080824016',
                'uif_reference_number' => 'U080824016',
                'sdl_reference_number' => 'L080824016',
                'phone' => '0118495307',
                'email' => 'natalie@gtadmin.co.za',
                'physical_address_line1' => 'Greenstone PI',
                'physical_address_line2' => 'Stoneridge Office Park',
                'physical_suburb' => 'Greenstone',
                'physical_city' => 'Greenstone',
                'physical_postal_code' => '1616',
                'postal_country_code' => 'ZA',
                'postal_address_line1' => 'Greenstone PI',
                'postal_address_line2' => 'Stoneridge Office Park',
                'postal_suburb' => 'Greenstone',
                'postal_city' => 'Greenstone',
                'postal_code' => '1616',
                'province_id' => 3,
                'is_active' => true,
                'is_verified' => true,
                'max_learners' => 100,
                'max_programs' => 20,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );

        // Create admin user (Natalie De Lange - the accountant from the data)
        $admin = User::firstOrCreate(
            ['email' => 'mmathabo@skillspanda.co.za'],
            [
                'first_name' => 'Mmathabo',
                'last_name' => 'Mphahlele',
                'email' => 'mmathabo@skillspanda.co.za',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
                'company_id' => $company->id,
                'employee_number' => 'NAT001',
                'occupation' => 'Accountant',
                'phone' => '0118495307',
                'is_employee' => true,
                'employment_status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );

        // Learners are created by LearnerIRP5Seeder - just update them with Connect HR company
        $learnerEmails = [
            'andile.mdlankomo@connecthr.co.za',
            'ayanda.thabethe@connecthr.co.za',
            'bongiwe.nkosi@connecthr.co.za',
            'kelebogile.pelo@connecthr.co.za',
            'kwazusomandla.ndaba@connecthr.co.za',
        ];

        User::whereIn('email', $learnerEmails)->update(['company_id' => $company->id]);

        // Create a simple learner account for testing
        User::firstOrCreate(
            ['email' => 'learner@connecthr.co.za'],
            [
                'first_name' => 'Anna',
                'last_name' => 'Mupariwa',
                'email' => 'learner@connecthr.co.za',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
                'company_id' => $company->id,
                'employee_number' => 'CHR999',
                'id_number' => '9501011234567',
                'tax_number' => 'TAX987654321',
                'phone' => '0118495308',
                'birth_date' => '1995-01-01',
                'employment_start_date' => '2024-03-01',
                'res_addr_line1' => 'Greenstone PI',
                'res_addr_line2' => 'Stoneridge Office Park',
                'res_suburb' => 'Greenstone',
                'res_city' => 'Greenstone',
                'res_postcode' => '1616',
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

        $this->command->info('Connect HR test company created successfully!');
        $this->command->info('Admin: natalie@gtadmin.co.za (password: password)');
        $this->command->info('Simple Learner: learner@connecthr.co.za (password: password)');
        $this->command->info('IRP5 Learners: 5 learners linked to Connect HR company (password: password123)');
        $this->command->info('Company PAYE Reference: 7080824016');
    }
}
