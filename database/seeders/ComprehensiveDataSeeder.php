<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Company;
use App\Models\Program;
use App\Models\ProgramParticipant;
use App\Models\Schedule;
use App\Models\AttendanceRecord;
use App\Models\LeaveBalance;
use App\Models\LeaveType;
use App\Models\ProgramBudget;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;

class ComprehensiveDataSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('🌱 Starting comprehensive data seeding...');

        $company = $this->createCompany();
        $users = $this->createUsers($company);
        $program = $this->createProgram($company, $users[0]);
        $participants = $this->createProgramParticipants($program, $users);
        $this->createLeaveBalances($users, $company);
        $this->createProgramBudget($program, $users[0]);
        $schedules = $this->createWeeklySchedules($program);
        $this->createAttendanceRecords($schedules, $participants, $program);

        $this->command->info('✅ Comprehensive data seeding completed!');
    }

    private function createCompany()
    {
        return Company::firstOrCreate(
            ['paye_reference_number' => '7080824016'],
            [
                'name' => 'Connect HR Skills Development',
                'display_name' => 'Connect HR',
                'trading_name' => 'Connect HR',
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
    }

    private function createUsers($company)
    {
        $admin = User::firstOrCreate(
            ['email' => 'admin@connecthr.co.za'],
            [
                'first_name' => 'Mmathabo',
                'last_name' => 'Mphahlele',
                'email' => 'admin@connecthr.co.za',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
                'company_id' => $company->id,
                'employee_number' => 'ADM001',
                'occupation' => 'HR Manager',
                'phone' => '0118495307',
                'id_number' => '8501011234567',
                'birth_date' => '1985-01-01',
                'gender' => 'female',
                'is_employee' => true,
                'is_learner' => false,
                'employment_status' => 'active',
                'employment_start_date' => '2020-01-01',
            ]
        );
        $admin->assignRole('admin');

        $learners = [
            ['first_name' => 'Andile', 'last_name' => 'Mdlankomo', 'email' => 'andile.mdlankomo@connecthr.co.za', 'id_number' => '0007095439084', 'birth_date' => '2000-07-09', 'phone' => '0896609245', 'employee_number' => 'CHR001', 'gender' => 'male', 'employment_start_date' => '2024-01-15'],
            ['first_name' => 'Ayanda', 'last_name' => 'Thabethe', 'email' => 'ayanda.thabethe@connecthr.co.za', 'id_number' => '9208230852089', 'birth_date' => '1992-08-23', 'phone' => '1506763174', 'employee_number' => 'CHR002', 'gender' => 'female', 'employment_start_date' => '2024-02-01'],
            ['first_name' => 'Bongiwe', 'last_name' => 'Nkosi', 'email' => 'bongiwe.nkosi@connecthr.co.za', 'id_number' => '9707180326085', 'birth_date' => '1997-07-18', 'phone' => '3978380164', 'employee_number' => 'CHR003', 'gender' => 'female', 'employment_start_date' => '2024-01-20'],
            ['first_name' => 'Kelebogile', 'last_name' => 'Pelo', 'email' => 'kelebogile.pelo@connecthr.co.za', 'id_number' => '0110090253086', 'birth_date' => '2001-10-09', 'phone' => '1815638182', 'employee_number' => 'CHR004', 'gender' => 'female', 'employment_start_date' => '2024-03-01'],
            ['first_name' => 'Kwazusomandla', 'last_name' => 'Ndaba', 'email' => 'kwazusomandla.ndaba@connecthr.co.za', 'id_number' => '9505185169082', 'birth_date' => '1995-05-18', 'phone' => '1841646175', 'employee_number' => 'CHR005', 'gender' => 'male', 'employment_start_date' => '2024-01-10'],
            ['first_name' => 'Thabo', 'last_name' => 'Mthembu', 'email' => 'thabo.mthembu@connecthr.co.za', 'id_number' => '9803151234567', 'birth_date' => '1998-03-15', 'phone' => '0821234567', 'employee_number' => 'CHR006', 'gender' => 'male', 'employment_start_date' => '2024-02-15'],
            ['first_name' => 'Nomsa', 'last_name' => 'Dlamini', 'email' => 'nomsa.dlamini@connecthr.co.za', 'id_number' => '9604201234567', 'birth_date' => '1996-04-20', 'phone' => '0832345678', 'employee_number' => 'CHR007', 'gender' => 'female', 'employment_start_date' => '2024-01-25'],
            ['first_name' => 'Sipho', 'last_name' => 'Mkhize', 'email' => 'sipho.mkhize@connecthr.co.za', 'id_number' => '9907121234567', 'birth_date' => '1999-07-12', 'phone' => '0843456789', 'employee_number' => 'CHR008', 'gender' => 'male', 'employment_start_date' => '2024-03-10']
        ];

        $createdUsers = [$admin];

        foreach ($learners as $learnerData) {
            $user = User::firstOrCreate(
                ['email' => $learnerData['email']],
                [
                    'first_name' => $learnerData['first_name'],
                    'last_name' => $learnerData['last_name'],
                    'email' => $learnerData['email'],
                    'password' => Hash::make('password'),
                    'phone' => $learnerData['phone'],
                    'company_id' => $company->id,
                    'id_number' => $learnerData['id_number'],
                    'birth_date' => Carbon::parse($learnerData['birth_date']),
                    'gender' => $learnerData['gender'],
                    'employee_number' => $learnerData['employee_number'],
                    'employment_start_date' => Carbon::parse($learnerData['employment_start_date']),
                    'is_learner' => true,
                    'is_employee' => true,
                    'employment_status' => 'active',
                    'email_verified_at' => now(),
                ]
            );
            $user->assignRole('learner');
            $createdUsers[] = $user;
        }

        return $createdUsers;
    }

    private function createProgram($company, $admin)
    {
        return Program::firstOrCreate(
            ['program_code' => 'DSDP2024'],
            [
                'title' => 'Digital Skills Development Program',
                'program_code' => 'DSDP2024',
                'description' => 'Comprehensive digital skills training program',
                'company_id' => $company->id,
                'creator_id' => $admin->id,
                'start_date' => '2024-01-01',
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
                'section_12h_start_date' => '2024-01-01',
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
            ]
        );
    }

    private function createProgramParticipants($program, $users)
    {
        $participants = [];
        $learnerUsers = array_slice($users, 1);

        foreach ($learnerUsers as $user) {
            $participant = ProgramParticipant::firstOrCreate(
                [
                    'program_id' => $program->id,
                    'user_id' => $user->id,
                ],
                [
                    'company_id' => $program->company_id,
                    'enrolled_at' => $user->employment_start_date,
                    'status' => 'active',
                    'notes' => 'Enrolled in Digital Skills Development Program',
                ]
            );
            $participants[] = $participant;
        }

        return $participants;
    }

    private function createLeaveBalances($users, $company)
    {
        $annualLeave = LeaveType::where('code', 'ANNUAL')->first();
        $sickLeave = LeaveType::where('code', 'SICK')->first();
        $familyLeave = LeaveType::where('code', 'FAMILY')->first();

        if (!$annualLeave || !$sickLeave || !$familyLeave) {
            $this->command->warn('Leave types not found. Please run SALeaveTypesSeeder first.');
            return;
        }

        foreach ($users as $user) {
            if ($user->is_learner) {
                $employmentStartDate = Carbon::parse($user->employment_start_date);
                $monthsWorked = $employmentStartDate->diffInMonths(now());

                $annualDays = min(15, floor(($monthsWorked / 12) * 15));
                $sickDays = min(30, floor(($monthsWorked / 36) * 30));
                $familyDays = min(3, floor(($monthsWorked / 12) * 3));

                LeaveBalance::firstOrCreate(
                    ['user_id' => $user->id, 'leave_type_id' => $annualLeave->id, 'company_id' => $company->id],
                    [
                        'total_days' => $annualDays,
                        'used_days' => rand(0, max(0, $annualDays - 5)),
                        'remaining_days' => $annualDays - rand(0, max(0, $annualDays - 5)),
                        'accrued_days' => $annualDays,
                        'carried_over_days' => 0,
                        'is_probationary' => $monthsWorked < 6,
                    ]
                );

                LeaveBalance::firstOrCreate(
                    ['user_id' => $user->id, 'leave_type_id' => $sickLeave->id, 'company_id' => $company->id],
                    [
                        'total_days' => $sickDays,
                        'used_days' => rand(0, max(0, $sickDays - 10)),
                        'remaining_days' => $sickDays - rand(0, max(0, $sickDays - 10)),
                        'accrued_days' => $sickDays,
                        'carried_over_days' => 0,
                        'is_probationary' => $monthsWorked < 6,
                    ]
                );

                LeaveBalance::firstOrCreate(
                    ['user_id' => $user->id, 'leave_type_id' => $familyLeave->id, 'company_id' => $company->id],
                    [
                        'total_days' => $familyDays,
                        'used_days' => rand(0, $familyDays),
                        'remaining_days' => $familyDays - rand(0, $familyDays),
                        'accrued_days' => $familyDays,
                        'carried_over_days' => 0,
                        'is_probationary' => $monthsWorked < 6,
                    ]
                );
            }
        }
    }

    private function createProgramBudget($program, $admin)
    {
        return ProgramBudget::firstOrCreate(
            ['program_id' => $program->id],
            [
                'company_id' => $program->company_id,
                'budget_start_date' => $program->start_date,
                'budget_end_date' => $program->end_date,
                'budget_name' => 'Digital Skills Development Budget 2024',
                'description' => 'Annual budget for digital skills development program',
                'travel_daily_rate' => 50.00,
                'online_daily_rate' => 300.00,
                'equipment_daily_rate' => 25.00,
                'onsite_daily_rate' => 350.00,
                'transport_allowance' => 50.00,
                'meal_allowance' => 80.00,
                'accommodation_allowance' => 200.00,
                'total_budget' => 500000.00,
                'used_budget' => 125000.00,
                'remaining_budget' => 375000.00,
                'is_active' => true,
                'auto_calculate_rates' => true,
                'status' => 'approved',
                'created_by' => $admin->id,
                'approved_by' => $admin->id,
                'approved_at' => now(),
            ]
        );
    }

    private function createWeeklySchedules($program)
    {
        $schedules = [];
        $startDate = Carbon::parse('2024-01-01');
        $endDate = Carbon::parse('2024-12-31');
        $currentDate = $startDate->copy()->startOfWeek();

        while ($currentDate->lte($endDate)) {
            for ($day = 0; $day < 5; $day++) {
                $scheduleDate = $currentDate->copy()->addDays($day);

                if ($scheduleDate->gt($endDate) || $scheduleDate->isWeekend()) {
                    continue;
                }

                $sessionTypes = ['lecture', 'practical', 'assessment', 'workshop', 'seminar'];
                $sessionType = $sessionTypes[array_rand($sessionTypes)];

                $startTime = Carbon::parse('09:00')->addMinutes(rand(0, 30));
                $endTime = $startTime->copy()->addHours(rand(6, 8));

                $schedule = Schedule::firstOrCreate(
                    [
                        'program_id' => $program->id,
                        'session_date' => $scheduleDate,
                    ],
                    [
                        'company_id' => $program->company_id,
                        'title' => ucfirst($sessionType) . ' Session - ' . $scheduleDate->format('M d, Y'),
                        'description' => 'Digital skills training session',
                        'session_type' => $sessionType,
                        'start_time' => $startTime->format('H:i'),
                        'end_time' => $endTime->format('H:i'),
                        'venue_name' => $program->venue,
                        'is_online' => rand(0, 1) == 1,
                        'status' => $scheduleDate->lt(now()) ? 'completed' : 'scheduled',
                        'instructor_notes' => 'Regular training session',
                        'learning_outcomes' => json_encode(['Understand digital concepts', 'Apply practical skills']),
                        'required_materials' => json_encode(['Laptop', 'Notebook', 'Pen']),
                    ]
                );

                $schedules[] = $schedule;
            }
            $currentDate->addWeek();
        }

        return $schedules;
    }

    private function createAttendanceRecords($schedules, $participants, $program)
    {
        foreach ($schedules as $schedule) {
            foreach ($participants as $participant) {
                $user = $participant->user;
                $attendanceStatus = $this->determineAttendanceStatus($user, $schedule);

                if ($attendanceStatus !== null) {
                    $checkInTime = null;
                    $checkOutTime = null;
                    $hoursWorked = 0;
                    $isPayable = true;

                    if (in_array($attendanceStatus, ['present', 'late'])) {
                        $checkInTime = Carbon::parse($schedule->start_time)->addMinutes(rand(-15, 30));
                        $checkOutTime = Carbon::parse($schedule->end_time)->addMinutes(rand(-30, 15));
                        $hoursWorked = $checkInTime->diffInHours($checkOutTime);
                        $isPayable = true;
                    } elseif ($attendanceStatus === 'half_day') {
                        $checkInTime = Carbon::parse($schedule->start_time);
                        $checkOutTime = Carbon::parse($schedule->start_time)->addHours(4);
                        $hoursWorked = 4;
                        $isPayable = true;
                    } elseif ($attendanceStatus === 'absent_unauthorized') {
                        $isPayable = false;
                    } elseif (in_array($attendanceStatus, ['absent_authorized', 'excused', 'on_leave', 'sick'])) {
                        $isPayable = true;
                    }

                    $dailyRate = $program->daily_rate;
                    if ($schedule->is_online) {
                        $dailyRate = 300.00;
                    }

                    $calculatedPay = $isPayable ? $dailyRate * ($hoursWorked / 8) : 0;

                    AttendanceRecord::firstOrCreate(
                        [
                            'company_id' => $program->company_id,
                            'user_id' => $user->id,
                            'program_id' => $program->id,
                            'schedule_id' => $schedule->id,
                            'attendance_date' => $schedule->session_date,
                        ],
                        [
                            'check_in_time' => $checkInTime ? $checkInTime->format('H:i') : null,
                            'check_out_time' => $checkOutTime ? $checkOutTime->format('H:i') : null,
                            'status' => $attendanceStatus,
                            'attendance_type' => 'regular',
                            'is_payable' => $isPayable,
                            'daily_rate_applied' => $dailyRate,
                            'calculated_pay' => $calculatedPay,
                            'hours_worked' => $hoursWorked,
                            'partial_day_percentage' => $hoursWorked > 0 ? ($hoursWorked / 8) * 100 : 0,
                            'notes' => $this->getAttendanceNotes($attendanceStatus),
                        ]
                    );
                }
            }
        }
    }

    private function determineAttendanceStatus($user, $schedule)
    {
        $userId = $user->id;
        $scheduleDate = Carbon::parse($schedule->session_date);

        if ($scheduleDate->gt(now())) {
            return null;
        }

        $reliability = $userId % 10;

        if ($reliability >= 8) {
            $rand = rand(1, 100);
            if ($rand <= 95) return 'present';
            if ($rand <= 97) return 'late';
            if ($rand <= 98) return 'sick';
            return 'absent_authorized';
        } elseif ($reliability >= 6) {
            $rand = rand(1, 100);
            if ($rand <= 85) return 'present';
            if ($rand <= 90) return 'late';
            if ($rand <= 93) return 'sick';
            if ($rand <= 95) return 'absent_authorized';
            return 'absent_unauthorized';
        } elseif ($reliability >= 4) {
            $rand = rand(1, 100);
            if ($rand <= 75) return 'present';
            if ($rand <= 80) return 'late';
            if ($rand <= 85) return 'sick';
            if ($rand <= 90) return 'absent_authorized';
            if ($rand <= 95) return 'excused';
            return 'absent_unauthorized';
        } else {
            $rand = rand(1, 100);
            if ($rand <= 60) return 'present';
            if ($rand <= 65) return 'late';
            if ($rand <= 70) return 'sick';
            if ($rand <= 80) return 'absent_authorized';
            if ($rand <= 85) return 'excused';
            if ($rand <= 90) return 'half_day';
            return 'absent_unauthorized';
        }
    }

    private function getAttendanceNotes($status)
    {
        $notes = [
            'present' => 'Attended full session',
            'late' => 'Arrived late but participated',
            'sick' => 'Medical certificate provided',
            'absent_authorized' => 'Authorized absence with valid reason',
            'absent_unauthorized' => 'No valid reason provided',
            'excused' => 'Excused absence with approval',
            'on_leave' => 'On approved leave',
            'half_day' => 'Attended half day only',
        ];

        return $notes[$status] ?? '';
    }
}
