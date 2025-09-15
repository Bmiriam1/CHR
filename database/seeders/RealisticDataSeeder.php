<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\Company;
use App\Models\Program;
use App\Models\ProgramType;
use App\Models\ProgramParticipant;
use App\Models\Schedule;
use App\Models\AttendanceRecord;
use App\Models\LeaveBalance;
use App\Models\LeaveRequest;
use App\Models\ProgramBudget;
use Carbon\Carbon;
use Faker\Factory as Faker;
use RealisticDataSeederHelpers;

class RealisticDataSeeder extends Seeder
{
    private $faker;
    private $company;
    private $program;
    private $learners = [];

    public function run(): void
    {
        $this->faker = Faker::create('en_ZA'); // South African locale
        
        $this->command->info('Creating realistic South African training data...');
        
        // Create company and program
        $this->createCompanyAndProgram();
        
        // Create realistic learners
        $this->createLearners();
        
        // Create program participants
        $this->createProgramParticipants();
        
        // Create program budget
        $this->createProgramBudget();
        
        // Create realistic schedules (5 days a week for 3 months)
        $this->createSchedules();
        
        // Create attendance records with realistic patterns
        $this->createAttendanceRecords();
        
        // Create leave balances based on SA labour law
        $this->createLeaveBalances();
        
        // Create some leave requests
        $this->createLeaveRequests();
        
        $this->command->info('Realistic data seeding completed!');
    }

    private function createCompanyAndProgram()
    {
        // Create or get company
        $this->company = Company::firstOrCreate(
            ['paye_reference_number' => '7080824016'],
            [
                'name' => 'Connect HR Training Solutions',
                'display_name' => 'Connect HR',
                'trading_name' => 'Connect HR Training',
                'paye_reference_number' => '7080824016',
                'uif_reference_number' => 'U080824016',
                'sdl_reference_number' => 'L080824016',
                'phone' => '0118495307',
                'email' => 'info@connecthr.co.za',
                'physical_address_line1' => 'Greenstone PI',
                'physical_address_line2' => 'Stoneridge Office Park',
                'physical_suburb' => 'Greenstone',
                'physical_city' => 'Johannesburg',
                'physical_postal_code' => '1616',
                'postal_country_code' => 'ZA',
                'is_active' => true,
                'is_verified' => true,
                'max_learners' => 100,
                'max_programs' => 20,
            ]
        );

        // Create program type
        $programType = ProgramType::firstOrCreate(
            ['name' => 'Skills Development'],
            ['slug' => 'skills-development']
        );

        // Create program
        $this->program = Program::firstOrCreate(
            ['program_code' => 'DSDP2024'],
            [
                'title' => 'Digital Skills Development Program',
                'program_code' => 'DSDP2024',
                'description' => 'Comprehensive digital skills training program covering Microsoft Office, basic programming, and digital literacy',
                'company_id' => $this->company->id,
                'program_type_id' => $programType->id,
                'creator_id' => 1, // Assuming admin user exists
                'start_date' => '2024-09-01',
                'end_date' => '2024-11-30',
                'daily_rate' => 350.00,
                'transport_allowance' => 50.00,
                'payment_frequency' => 'monthly',
                'payment_day_of_month' => 25,
                'max_learners' => 25,
                'min_learners' => 5,
                'location_type' => 'hybrid',
                'venue' => 'Connect HR Training Center',
                'venue_address' => '123 Business Park Drive, Sandton',
                'section_12h_eligible' => true,
                'section_12h_contract_number' => 'SEC12H2024001',
                'section_12h_start_date' => '2024-09-01',
                'section_12h_end_date' => '2024-11-30',
                'section_12h_allowance' => 1000.00,
                'eti_eligible_program' => true,
                'eti_category' => 'youth',
                'nqf_level' => 4,
                'saqa_id' => '12345',
                'qualification_title' => 'Certificate in Digital Skills',
                'status' => 'active',
                'is_approved' => true,
                'approved_by' => 1,
                'approved_at' => now(),
            ]
        );
    }

    private function createLearners()
    {
        $this->command->info('Creating realistic learners...');
        
        // South African names and details
        $southAfricanNames = [
            ['first_name' => 'Thabo', 'last_name' => 'Mthembu', 'email' => 'thabo.mthembu@connecthr.co.za'],
            ['first_name' => 'Nomsa', 'last_name' => 'Dlamini', 'email' => 'nomsa.dlamini@connecthr.co.za'],
            ['first_name' => 'Sipho', 'last_name' => 'Nkosi', 'email' => 'sipho.nkosi@connecthr.co.za'],
            ['first_name' => 'Lerato', 'last_name' => 'Molefe', 'email' => 'lerato.molefe@connecthr.co.za'],
            ['first_name' => 'Mandla', 'last_name' => 'Zulu', 'email' => 'mandla.zulu@connecthr.co.za'],
            ['first_name' => 'Precious', 'last_name' => 'Mabena', 'email' => 'precious.mabena@connecthr.co.za'],
            ['first_name' => 'Sibusiso', 'last_name' => 'Mthembu', 'email' => 'sibusiso.mthembu@connecthr.co.za'],
            ['first_name' => 'Nthabiseng', 'last_name' => 'Moleko', 'email' => 'nthabiseng.moleko@connecthr.co.za'],
            ['first_name' => 'Tshepo', 'last_name' => 'Mokone', 'email' => 'tshepo.mokone@connecthr.co.za'],
            ['first_name' => 'Refilwe', 'last_name' => 'Mabena', 'email' => 'refilwe.mabena@connecthr.co.za'],
            ['first_name' => 'Kagiso', 'last_name' => 'Molefe', 'email' => 'kagiso.molefe@connecthr.co.za'],
            ['first_name' => 'Thandiwe', 'last_name' => 'Nkosi', 'email' => 'thandiwe.nkosi@connecthr.co.za'],
            ['first_name' => 'Bongani', 'last_name' => 'Dlamini', 'email' => 'bongani.dlamini@connecthr.co.za'],
            ['first_name' => 'Nomsa', 'last_name' => 'Zulu', 'email' => 'nomsa.zulu@connecthr.co.za'],
            ['first_name' => 'Sipho', 'last_name' => 'Mabena', 'email' => 'sipho.mabena@connecthr.co.za'],
        ];

        foreach ($southAfricanNames as $index => $nameData) {
            $user = User::create([
                'first_name' => $nameData['first_name'],
                'last_name' => $nameData['last_name'],
                'email' => $nameData['email'],
                'password' => bcrypt('password'),
                'email_verified_at' => now(),
                'company_id' => $this->company->id,
                'employee_number' => 'CHR' . str_pad($index + 1, 3, '0', STR_PAD_LEFT),
                'id_number' => $this->generateSouthAfricanIdNumber(),
                'tax_number' => 'TAX' . rand(100000000, 999999999),
                'phone' => '0' . rand(60, 83) . rand(1000000, 9999999), // SA mobile format
                'birth_date' => $this->faker->dateTimeBetween('-35 years', '-18 years'),
                'employment_start_date' => '2024-09-01',
                'res_addr_line1' => $this->faker->streetAddress(),
                'res_addr_line2' => $this->faker->secondaryAddress(),
                'res_suburb' => $this->faker->randomElement(['Sandton', 'Rosebank', 'Midrand', 'Randburg', 'Fourways']),
                'res_city' => 'Johannesburg',
                'res_postcode' => rand(1600, 2199),
                'res_country_code' => 'ZA',
                'is_learner' => true,
                'is_employee' => false,
                'employment_status' => 'active',
                'gender' => $this->faker->randomElement(['male', 'female']),
                'marital_status' => $this->faker->randomElement(['single', 'married', 'divorced']),
                'citizenship_status' => 'citizen',
                'bank_name' => $this->faker->randomElement(['FNB', 'Standard Bank', 'ABSA', 'Nedbank', 'Capitec']),
                'bank_account_number' => rand(1000000000, 9999999999),
                'bank_branch_code' => rand(100000, 999999),
                'eti_eligible' => true,
            ]);

            // Assign learner role
            $user->assignRole('learner');
            
            $this->learners[] = $user;
        }

        $this->command->info('Created ' . count($this->learners) . ' learners');
    }

    private function createProgramParticipants()
    {
        $this->command->info('Creating program participants...');
        
        foreach ($this->learners as $learner) {
            ProgramParticipant::create([
                'company_id' => $this->company->id,
                'program_id' => $this->program->id,
                'user_id' => $learner->id,
                'enrolled_at' => '2024-09-01',
                'status' => 'active',
                'notes' => 'Enrolled in Digital Skills Development Program',
            ]);
        }
    }

    private function createProgramBudget()
    {
        $this->command->info('Creating program budget...');
        
        ProgramBudget::create([
            'company_id' => $this->company->id,
            'program_id' => $this->program->id,
            'budget_start_date' => '2024-09-01',
            'budget_end_date' => '2024-11-30',
            'budget_name' => 'Q4 2024 Digital Skills Budget',
            'description' => 'Budget for Digital Skills Development Program Q4 2024',
            'travel_daily_rate' => 50.00,
            'online_daily_rate' => 300.00,
            'equipment_daily_rate' => 25.00,
            'onsite_daily_rate' => 350.00,
            'travel_allowance' => 50.00,
            'meal_allowance' => 30.00,
            'equipment_allowance' => 20.00,
            'total_budget' => 150000.00,
            'used_budget' => 0.00,
            'remaining_budget' => 150000.00,
            'is_active' => true,
            'auto_calculate_rates' => true,
            'status' => 'approved',
            'created_by' => 1,
            'approved_by' => 1,
            'approved_at' => now(),
        ]);
    }