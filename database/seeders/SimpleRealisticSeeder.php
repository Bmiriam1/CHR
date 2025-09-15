<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
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

class SimpleRealisticSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('Creating realistic South African training data...');

        // Get existing company and program
        $company = Company::first();
        $program = Program::first();

        if (!$company || !$program) {
            $this->command->error('Company or Program not found. Please run TestCompanySeeder first.');
            return;
        }

        // Create 15 realistic learners
        $this->createLearners($company);

        // Create program participants
        $this->createProgramParticipants($company, $program);

        // Create program budget
        $this->createProgramBudget($company, $program);

        // Create schedules for 3 months (5 days a week)
        $this->createSchedules($company, $program);

        // Create attendance records
        $this->createAttendanceRecords($company, $program);

        // Create leave balances
        $this->createLeaveBalances($company);

        $this->command->info('Realistic data seeding completed!');
    }

    private function createLearners($company)
    {
        $this->command->info('Creating 15 realistic learners...');

        $names = [
            ['first_name' => 'Thabo', 'last_name' => 'Mthembu'],
            ['first_name' => 'Nomsa', 'last_name' => 'Dlamini'],
            ['first_name' => 'Sipho', 'last_name' => 'Nkosi'],
            ['first_name' => 'Lerato', 'last_name' => 'Molefe'],
            ['first_name' => 'Mandla', 'last_name' => 'Zulu'],
            ['first_name' => 'Precious', 'last_name' => 'Mabena'],
            ['first_name' => 'Sibusiso', 'last_name' => 'Mthembu'],
            ['first_name' => 'Nthabiseng', 'last_name' => 'Moleko'],
            ['first_name' => 'Tshepo', 'last_name' => 'Mokone'],
            ['first_name' => 'Refilwe', 'last_name' => 'Mabena'],
            ['first_name' => 'Kagiso', 'last_name' => 'Molefe'],
            ['first_name' => 'Thandiwe', 'last_name' => 'Nkosi'],
            ['first_name' => 'Bongani', 'last_name' => 'Dlamini'],
            ['first_name' => 'Nomsa', 'last_name' => 'Zulu'],
            ['first_name' => 'Sipho', 'last_name' => 'Mabena'],
        ];

        foreach ($names as $index => $name) {
            User::create([
                'first_name' => $name['first_name'],
                'last_name' => $name['last_name'],
                'email' => strtolower($name['first_name'] . '.' . $name['last_name'] . '@connecthr.co.za'),
                'password' => bcrypt('password'),
                'email_verified_at' => now(),
                'company_id' => $company->id,
                'employee_number' => 'CHR' . str_pad($index + 1, 3, '0', STR_PAD_LEFT),
                'id_number' => $this->generateSAId(),
                'phone' => '0' . rand(60, 83) . rand(1000000, 9999999),
                'birth_date' => Carbon::now()->subYears(rand(18, 35)),
                'employment_start_date' => '2024-09-01',
                'res_addr_line1' => '123 Main Street',
                'res_suburb' => 'Sandton',
                'res_city' => 'Johannesburg',
                'res_postcode' => '2196',
                'res_country_code' => 'ZA',
                'is_learner' => true,
                'is_employee' => false,
                'employment_status' => 'active',
                'gender' => rand(0, 1) ? 'male' : 'female',
                'citizenship_status' => 'citizen',
                'bank_name' => 'FNB',
                'bank_account_number' => rand(1000000000, 9999999999),
                'eti_eligible' => true,
            ]);
        }
    }

    private function createProgramParticipants($company, $program)
    {
        $learners = User::where('company_id', $company->id)
            ->where('is_learner', true)
            ->get();

        foreach ($learners as $learner) {
            ProgramParticipant::create([
                'company_id' => $company->id,
                'program_id' => $program->id,
                'user_id' => $learner->id,
                'enrolled_at' => '2024-09-01',
                'status' => 'active',
                'notes' => 'Enrolled in Digital Skills Development Program',
            ]);
        }
    }

    private function createProgramBudget($company, $program)
    {
        ProgramBudget::create([
            'company_id' => $company->id,
            'program_id' => $program->id,
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

    private function createSchedules($company, $program)
    {
        $this->command->info('Creating 3 months of 5-day weekly schedules...');

        $startDate = Carbon::parse('2024-09-01');
        $endDate = Carbon::parse('2024-11-30');
        $currentDate = $startDate->copy();

        $topics = [
            'Introduction to Digital Literacy',
            'Microsoft Word Fundamentals',
            'Microsoft Excel Basics',
            'Microsoft PowerPoint Presentation',
            'Internet and Email Skills',
            'Basic Computer Maintenance',
            'Digital Security Awareness',
            'Online Research Skills',
            'Social Media for Business',
            'Introduction to Programming',
        ];

        $scheduleCount = 0;

        while ($currentDate->lte($endDate)) {
            if ($currentDate->isWeekday()) {
                $topic = $topics[array_rand($topics)];

                // Morning session
                Schedule::create([
                    'company_id' => $company->id,
                    'program_id' => $program->id,
                    'title' => $topic . ' - Morning',
                    'description' => 'Morning session covering ' . strtolower($topic),
                    'session_date' => $currentDate->format('Y-m-d'),
                    'start_time' => '09:00:00',
                    'end_time' => '12:00:00',
                    'break_start_time' => '10:30:00',
                    'break_end_time' => '10:45:00',
                    'planned_duration_hours' => 3.0,
                    'break_duration_hours' => 0.25,
                    'net_training_hours' => 2.75,
                    'session_type' => 'lecture',
                    'venue_name' => 'Training Room A',
                    'province_id' => 1, // Gauteng
                    'is_online' => false,
                    'status' => 'completed',
                    'created_by' => 1,
                ]);

                // Afternoon session
                Schedule::create([
                    'company_id' => $company->id,
                    'program_id' => $program->id,
                    'title' => $topic . ' - Afternoon',
                    'description' => 'Afternoon practical session',
                    'session_date' => $currentDate->format('Y-m-d'),
                    'start_time' => '13:00:00',
                    'end_time' => '16:00:00',
                    'break_start_time' => '14:30:00',
                    'break_end_time' => '14:45:00',
                    'planned_duration_hours' => 3.0,
                    'break_duration_hours' => 0.25,
                    'net_training_hours' => 2.75,
                    'session_type' => 'practical',
                    'venue_name' => 'Computer Lab B',
                    'province_id' => 1, // Gauteng
                    'is_online' => false,
                    'status' => 'completed',
                    'created_by' => 1,
                ]);

                $scheduleCount += 2;
            }
            $currentDate->addDay();
        }

        $this->command->info("Created {$scheduleCount} schedule sessions");
    }

    private function createAttendanceRecords($company, $program)
    {
        $this->command->info('Creating realistic attendance records...');

        $schedules = Schedule::where('program_id', $program->id)->get();
        $learners = User::where('company_id', $company->id)
            ->where('is_learner', true)
            ->get();

        $attendanceCount = 0;

        foreach ($schedules as $schedule) {
            foreach ($learners as $learner) {
                $status = $this->getAttendanceStatus();
                $isPayable = !in_array($status, ['absent_unauthorized']);

                $checkInTime = null;
                $checkOutTime = null;
                $hoursWorked = 0;

                if (in_array($status, ['present', 'late'])) {
                    $checkInTime = $status === 'late' ? '09:15:00' : '08:55:00';
                    $checkOutTime = '16:05:00';
                    $hoursWorked = 6.0;
                } elseif ($status === 'half_day') {
                    $checkInTime = '09:00:00';
                    $checkOutTime = '12:00:00';
                    $hoursWorked = 3.0;
                }

                AttendanceRecord::create([
                    'company_id' => $company->id,
                    'user_id' => $learner->id,
                    'program_id' => $program->id,
                    'schedule_id' => $schedule->id,
                    'attendance_date' => $schedule->session_date,
                    'check_in_time' => $checkInTime,
                    'check_out_time' => $checkOutTime,
                    'status' => $status,
                    'attendance_type' => 'regular',
                    'is_payable' => $isPayable,
                    'daily_rate_applied' => $program->daily_rate,
                    'calculated_pay' => $isPayable ? $program->daily_rate : 0,
                    'hours_worked' => $hoursWorked,
                    'partial_day_percentage' => $hoursWorked / 6.0,
                    'notes' => $this->getAttendanceNotes($status),
                    'created_at' => $schedule->session_date,
                ]);

                $attendanceCount++;
            }
        }

        $this->command->info("Created {$attendanceCount} attendance records");
    }

    private function createLeaveBalances($company)
    {
        $learners = User::where('company_id', $company->id)
            ->where('is_learner', true)
            ->get();

        // Get the first program for this company
        $program = \App\Models\Program::where('company_id', $company->id)->first();

        if (!$program) {
            $this->command->warn('No program found for company. Skipping leave balances.');
            return;
        }

        foreach ($learners as $learner) {
            // Create leave balance record with SA-compliant structure
            $annualUsed = rand(0, 5);
            $sickUsed = rand(0, 3);

            LeaveBalance::create([
                'company_id' => $company->id,
                'user_id' => $learner->id,
                'program_id' => $program->id, // Use the first program
                'leave_year' => 2024,

                // Annual leave: 21 days per year (SA minimum)
                'annual_leave_entitled' => 21,
                'annual_leave_taken' => $annualUsed,
                'annual_leave_balance' => 21 - $annualUsed,
                'annual_leave_carried_over' => 0,

                // Sick leave: 30 days per 3-year cycle
                'sick_leave_cycle_entitled' => 30,
                'sick_leave_cycle_taken' => $sickUsed,
                'sick_leave_cycle_balance' => 30 - $sickUsed,
                'sick_leave_cycle_start' => '2022-01-01',
                'sick_leave_cycle_end' => '2024-12-31',

                // Other leave types
                'maternity_leave_entitled' => 120,
                'maternity_leave_taken' => 0,
                'maternity_leave_balance' => 120,

                'paternity_leave_entitled' => 10,
                'paternity_leave_taken' => 0,
                'paternity_leave_balance' => 10,

                'family_responsibility_leave_entitled' => 3,
                'family_responsibility_leave_taken' => 0,
                'family_responsibility_leave_balance' => 3,

                'study_leave_entitled' => 5,
                'study_leave_taken' => 0,
                'study_leave_balance' => 5,

                // Leave year
                'leave_year_start' => '2024-01-01',
                'leave_year_end' => '2024-12-31',

                // Employment details
                'employment_start_date' => $learner->employment_start_date ?? '2020-01-01',
                'is_probationary' => false,
                'is_active' => true,

                // Accrual rate
                'accrual_rate_per_month' => 1.75,
            ]);
        }
    }

    private function generateSAId()
    {
        $year = rand(85, 99);
        $month = str_pad(rand(1, 12), 2, '0', STR_PAD_LEFT);
        $day = str_pad(rand(1, 28), 2, '0', STR_PAD_LEFT);
        $gender = rand(0, 9);
        $citizenship = rand(0, 1);
        $sequence = str_pad(rand(0, 999), 3, '0', STR_PAD_LEFT);

        return $year . $month . $day . $gender . $citizenship . $sequence;
    }

    private function getAttendanceStatus()
    {
        $rand = rand(1, 100);

        if ($rand <= 70) return 'present';
        if ($rand <= 80) return 'late';
        if ($rand <= 85) return 'absent_unauthorized';
        if ($rand <= 90) return 'absent_authorized';
        if ($rand <= 93) return 'excused';
        if ($rand <= 97) return 'on_leave';
        return 'sick';
    }

    private function getAttendanceNotes($status)
    {
        $notes = [
            'present' => 'Attended full session',
            'late' => 'Arrived late due to transport',
            'absent_unauthorized' => 'No show - no explanation',
            'absent_authorized' => 'Absent with valid reason',
            'excused' => 'Excused absence',
            'on_leave' => 'On approved leave',
            'sick' => 'Sick leave',
        ];

        return $notes[$status] ?? '';
    }
}
