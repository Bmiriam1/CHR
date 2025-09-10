<?php

namespace Database\Seeders;

use App\Models\Company;
use App\Models\Program;
use App\Models\ProgramType;
use App\Models\User;
use App\Models\Schedule;
use App\Models\AttendanceRecord;
use App\Models\Payslip;
use App\Models\Device;
use App\Models\PaymentSchedule;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class ComplianceTestSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->createProgramTypes();
        $this->createCompanies();
        $this->createUsers();
        $this->createPrograms();
        $this->createDevices();
        $this->createSchedules();
        $this->createAttendanceRecords();
        $this->createPayslips();
        $this->createPaymentSchedules();
    }

    private function createProgramTypes(): void
    {
        $programTypes = [
            ['name' => 'Learnership', 'slug' => 'learnership'],
            ['name' => 'Skills Programme', 'slug' => 'skills-programme'], 
            ['name' => 'Internship', 'slug' => 'internship'],
        ];

        foreach ($programTypes as $type) {
            ProgramType::create($type);
        }
    }

    private function createCompanies(): void
    {
        $companies = [
            [
                'name' => 'TechSkills SA (Pty) Ltd',
                'tenant_key' => 'techskills-sa',
                'company_registration_number' => '2020/123456/07',
                'vat_number' => '4123456789',
                'paye_reference_number' => '7123456789',
                'uif_reference_number' => 'U123456789',
                'sdl_reference_number' => 'SDL123456789',
                'email' => 'admin@techskills.co.za',
                'phone' => '+27123456789',
                'physical_address_line1' => '123 Tech Street',
                'physical_suburb' => 'Sandton',
                'physical_city' => 'Sandton',
                'physical_postal_code' => '2196',
                'postal_address_line1' => 'PO Box 123',
                'postal_suburb' => 'Sandton',
                'postal_city' => 'Sandton',
                'postal_code' => '2146',
                'industry_sector' => 'Information Technology',
                'is_active' => true,
                'is_verified' => true,
            ],
            [
                'name' => 'Green Energy Training Academy',
                'tenant_key' => 'green-energy-academy',
                'company_registration_number' => '2019/987654/07',
                'vat_number' => '4987654321',
                'paye_reference_number' => '7987654321',
                'uif_reference_number' => 'U987654321',
                'sdl_reference_number' => 'SDL987654321',
                'email' => 'admin@greenenergy.co.za',
                'phone' => '+27987654321',
                'physical_address_line1' => '456 Solar Avenue',
                'physical_suburb' => 'Green Point',
                'physical_city' => 'Cape Town',
                'physical_postal_code' => '8001',
                'postal_address_line1' => 'PO Box 456',
                'postal_suburb' => 'Cape Town',
                'postal_city' => 'Cape Town',
                'postal_code' => '8000',
                'industry_sector' => 'Renewable Energy',
                'is_active' => true,
                'is_verified' => true,
            ]
        ];

        foreach ($companies as $companyData) {
            Company::create($companyData);
        }
    }

    private function createUsers(): void
    {
        $companies = Company::all();

        // Create admin users
        $adminData = [
            ['name' => 'Sarah Johnson', 'id_number' => '8001015009067'],
            ['name' => 'Michael Green', 'id_number' => '7512183056082'],
        ];

        foreach ($companies as $index => $company) {
            $admin = $adminData[$index];
            $firstName = explode(' ', $admin['name'])[0];
            $lastName = explode(' ', $admin['name'])[1] ?? 'Admin';
            
            User::create([
                'first_name' => $firstName,
                'last_name' => $lastName,
                'email' => $company->email,
                'email_verified_at' => now(),
                'password' => Hash::make('password'),
                'phone' => $company->phone,
                'id_number' => $admin['id_number'],
                'birth_date' => '1980-01-01',
                'gender' => 'female',
                'marital_status' => 'married',
                'citizenship_status' => 'citizen',
                'employee_number' => 'ADMIN' . str_pad($company->id, 3, '0', STR_PAD_LEFT),
                'employment_start_date' => '2020-01-15',
                'employment_status' => 'active',
                'employment_basis' => 'full_time',
                'is_employee' => true,
                'occupation' => 'CEO',
                'bank_name' => 'Standard Bank',
                'bank_account_number' => '123456789' . $company->id,
                'bank_branch_code' => '051001',
                'tax_number' => '901234567' . $company->id,
                'res_addr_line1' => $company->physical_address_line1,
                'res_suburb' => $company->physical_suburb,
                'res_city' => $company->physical_city,
                'res_postcode' => $company->physical_postal_code,
                'post_addr_line1' => $company->postal_address_line1,
                'post_suburb' => $company->postal_suburb,
                'post_city' => $company->postal_city,
                'post_postcode' => $company->postal_code,
            ]);
        }

        // Create learner users
        $learnerData = [
            [
                'name' => 'Thabo Mthembu',
                'email' => 'thabo.mthembu@example.com',
                'id_number' => '9505142345067',
                'date_of_birth' => '1995-05-14',
                'gender' => 'male',
                'phone_number' => '+27821234567',
                'first_name' => 'Thabo',
                'last_name' => 'Mthembu',
                'ethnicity' => 'african',
                'disability' => false,
                'home_language' => 'Zulu',
                'education_level' => 'matric',
                'employment_status_before' => 'unemployed',
            ],
            [
                'name' => 'Nomsa Khumalo',
                'email' => 'nomsa.khumalo@example.com',
                'id_number' => '9208256789012',
                'date_of_birth' => '1992-08-25',
                'gender' => 'female',
                'phone_number' => '+27829876543',
                'first_name' => 'Nomsa',
                'last_name' => 'Khumalo',
                'ethnicity' => 'african',
                'disability' => false,
                'home_language' => 'Xhosa',
                'education_level' => 'diploma',
                'employment_status_before' => 'unemployed',
            ],
            [
                'name' => 'Ahmed Hassan',
                'email' => 'ahmed.hassan@example.com',
                'id_number' => '8812103456078',
                'date_of_birth' => '1988-12-10',
                'gender' => 'male',
                'phone_number' => '+27834567890',
                'first_name' => 'Ahmed',
                'last_name' => 'Hassan',
                'ethnicity' => 'coloured',
                'disability' => false,
                'home_language' => 'Afrikaans',
                'education_level' => 'degree',
                'employment_status_before' => 'employed',
            ],
        ];

        foreach ($learnerData as $index => $data) {
            $company = $companies[$index % $companies->count()];

            User::create([
                'first_name' => $data['first_name'],
                'last_name' => $data['last_name'],
                'email' => $data['email'],
                'email_verified_at' => now(),
                'password' => Hash::make('password'),
                'phone' => $data['phone_number'],
                'id_number' => $data['id_number'],
                'birth_date' => $data['date_of_birth'],
                'gender' => $data['gender'],
                'marital_status' => 'single',
                'citizenship_status' => 'citizen',
                'employee_number' => 'L' . str_pad(($index + 1), 4, '0', STR_PAD_LEFT),
                'employment_start_date' => Carbon::now()->subMonths(6)->toDateString(),
                'employment_status' => 'active',
                'employment_basis' => 'learner',
                'is_learner' => true,
                'occupation' => 'Trainee',
                'bank_name' => ['FNB', 'ABSA', 'Nedbank'][rand(0, 2)],
                'bank_account_number' => '620' . str_pad(rand(1000000, 9999999), 7, '0'),
                'bank_branch_code' => ['250655', '632005', '198765'][rand(0, 2)],
                'tax_number' => '901' . str_pad(($index + 1) * 123456, 6, '0'),
                'eti_eligible' => true,
                'res_addr_line1' => 'Township Address ' . ($index + 1),
                'res_suburb' => 'Soweto',
                'res_city' => 'Johannesburg',
                'res_postcode' => '1809',
                'post_addr_line1' => 'PO Box ' . ($index + 100),
                'post_suburb' => 'Soweto',
                'post_city' => 'Johannesburg',
                'post_postcode' => '1800',
            ]);
        }
    }

    private function createPrograms(): void
    {
        $companies = Company::all();

        $programsData = [
            [
                'title' => 'Software Development Learnership',
                'program_code' => 'SDL-2024-001',
                'description' => 'Full-stack software development learnership with focus on web technologies',
                'nqf_level' => 5,
                'duration_months' => 12,
                'daily_rate' => 380.00,
                'transport_allowance' => 50.00,
                'meal_allowance' => 35.00,
                'section_12h_eligible' => true,
                'section_12h_allowance' => 45000.00, // R45,000 per learner per year
                'eti_eligible_program' => true,
                'start_date' => Carbon::now()->subMonths(6)->toDateString(),
                'end_date' => Carbon::now()->addMonths(6)->toDateString(),
                'max_learners' => 25,
                'saqa_id' => '48872',
            ],
            [
                'title' => 'Green Energy Technician Skills Program',
                'program_code' => 'GET-2024-002',
                'description' => 'Solar and wind energy installation and maintenance skills program',
                'nqf_level' => 4,
                'duration_months' => 8,
                'daily_rate' => 350.00,
                'transport_allowance' => 60.00,
                'meal_allowance' => 30.00,
                'section_12h_eligible' => true,
                'section_12h_allowance' => 30000.00, // R30,000 per learner
                'eti_eligible_program' => true,
                'start_date' => Carbon::now()->subMonths(4)->toDateString(),
                'end_date' => Carbon::now()->addMonths(4)->toDateString(),
                'max_learners' => 20,
                'saqa_id' => '58123',
            ]
        ];

        foreach ($programsData as $index => $data) {
            $company = $companies[$index % $companies->count()];

            Program::create(array_merge($data, [
                'company_id' => $company->id,
                'program_type_id' => 1, // Default program type - would need to create program types table
                'status' => 'active',
            ]));
        }
    }

    private function createDevices(): void
    {
        $learners = User::where('is_learner', true)->get();

        foreach ($learners as $learner) {
            Device::create([
                'user_id' => $learner->id,
                'device_name' => $learner->first_name . "'s Phone",
                'device_type' => 'mobile',
                'platform' => ['iOS', 'Android'][rand(0, 1)],
                'platform_version' => ['14.0', '15.0', 'Android 11', 'Android 12'][rand(0, 3)],
                'app_version' => '1.0.0',
                'expo_push_token' => 'ExponentPushToken[' . bin2hex(random_bytes(22)) . ']',
                'push_notifications_enabled' => true,
                'supports_qr_scanning' => true,
                'supports_gps' => true,
                'supports_camera' => true,
                'is_active' => true,
                'is_trusted' => true,
                'first_seen_at' => Carbon::now()->subMonths(6),
                'last_seen_at' => Carbon::now()->subDays(rand(1, 7)),
            ]);
        }
    }

    private function createSchedules(): void
    {
        $programs = Program::all();

        foreach ($programs as $program) {
            // Create weekly schedules for the past 3 months
            $startDate = Carbon::now()->subMonths(3)->startOfWeek();
            $endDate = Carbon::now();

            $weekCount = 1;
            while ($startDate->lte($endDate)) {
                for ($day = 1; $day <= 5; $day++) { // Monday to Friday
                    $sessionDate = $startDate->copy()->addDays($day - 1);

                    if ($sessionDate->lte($endDate)) {
                        Schedule::create([
                            'company_id' => $program->company_id,
                            'program_id' => $program->id,
                            'title' => $program->program_name . ' - Week ' . $weekCount . ' Day ' . $day,
                            'session_date' => $sessionDate,
                            'start_time' => '08:00:00',
                            'end_time' => '16:00:00',
                            'break_start_time' => '12:00:00',
                            'break_end_time' => '13:00:00',
                            'planned_duration_hours' => 8.0,
                            'break_duration_hours' => 1.0,
                            'net_training_hours' => 7.0,
                            'venue_name' => 'Training Room A',
                            'venue_address' => $program->company->physical_address,
                            'status' => $sessionDate->isPast() ? 'completed' : 'scheduled',
                            'expected_attendees' => 15,
                            'actual_attendees' => $sessionDate->isPast() ? rand(12, 15) : 0,
                            'attendance_rate' => $sessionDate->isPast() ? rand(80, 100) : 0,
                            'qr_code_active' => !$sessionDate->isPast(),
                        ]);
                    }
                }

                $startDate->addWeek();
                $weekCount++;
            }
        }
    }

    private function createAttendanceRecords(): void
    {
        $learners = User::where('is_learner', true)->get();
        $schedules = Schedule::where('status', 'completed')->get();

        foreach ($schedules as $schedule) {
            // Get learners for this program (simplified - all learners for now)
            $programLearners = $learners->take(rand(12, 15));

            foreach ($programLearners as $learner) {
                $device = Device::where('user_id', $learner->id)->first();

                if ($device && rand(1, 10) <= 9) { // 90% attendance rate
                    $checkInTime = $schedule->session_date->copy()->setTime(8, rand(0, 15)); // 8:00-8:15 AM
                    $checkOutTime = $schedule->session_date->copy()->setTime(16, rand(0, 30)); // 4:00-4:30 PM

                    $hoursWorked = $checkOutTime->diffInMinutes($checkInTime) / 60 - 1; // Minus lunch break
                    $isLate = $checkInTime->hour > 8 || ($checkInTime->hour === 8 && $checkInTime->minute > 10);

                    AttendanceRecord::create([
                        'company_id' => $schedule->company_id,
                        'user_id' => $learner->id,
                        'program_id' => $schedule->program_id,
                        'schedule_id' => $schedule->id,
                        'attendance_date' => $schedule->session_date,
                        'check_in_time' => $checkInTime,
                        'check_out_time' => $checkOutTime,
                        'hours_worked' => $hoursWorked,
                        'break_duration' => 1.0,
                        'status' => $isLate ? 'late' : 'present',
                        'is_payable' => true,
                        'daily_rate_applied' => $schedule->program->daily_rate,
                        'calculated_pay' => $schedule->program->daily_rate,
                        'check_in_device_id' => $device->id,
                        'check_out_device_id' => $device->id,
                        'is_validated' => true,
                        'validated_at' => now(),
                    ]);
                }
            }
        }
    }

    private function createPayslips(): void
    {
        $learners = User::where('is_learner', true)->get();
        $programs = Program::all();

        // Generate payslips for the past 3 months
        for ($monthsAgo = 3; $monthsAgo >= 1; $monthsAgo--) {
            $periodStart = Carbon::now()->subMonths($monthsAgo)->startOfMonth();
            $periodEnd = Carbon::now()->subMonths($monthsAgo)->endOfMonth();
            $payDate = $periodEnd->copy()->addDays(5);

            foreach ($learners as $learner) {
                $program = $programs->first(); // Simplified - use first program

                if (!$program) continue;

                // Get attendance records for this period
                $attendanceRecords = AttendanceRecord::where('user_id', $learner->id)
                    ->where('program_id', $program->id)
                    ->whereBetween('attendance_date', [$periodStart, $periodEnd])
                    ->where('is_payable', true)
                    ->get();

                if ($attendanceRecords->isEmpty()) continue;

                $daysWorked = $attendanceRecords->count();
                $hoursWorked = $attendanceRecords->sum('hours_worked');
                $basicEarnings = $daysWorked * $program->daily_rate;

                // Add allowances
                $transportAllowance = $daysWorked * ($program->transport_allowance ?? 0);
                $mealAllowance = $daysWorked * ($program->meal_allowance ?? 0);
                $totalAllowances = $transportAllowance + $mealAllowance;

                $grossEarnings = $basicEarnings + $totalAllowances;
                $taxableEarnings = $grossEarnings; // Simplified - allowances may be partially taxable

                // Calculate PAYE (simplified - using basic tax tables)
                $monthlyExemption = 4050; // R4,050 monthly exemption
                $taxableAmount = max(0, $taxableEarnings - $monthlyExemption);
                $payeTax = $taxableAmount * 0.18; // Simplified 18% rate

                // Calculate UIF
                $uifBase = min($taxableEarnings, 17712); // UIF ceiling
                $uifEmployee = $uifBase * 0.01;
                $uifEmployer = $uifBase * 0.01;

                // Calculate SDL
                $sdlContribution = $taxableEarnings * 0.01;

                // ETI Calculation (simplified)
                $etiEligible = $learner->eti_eligible && $learner->birth_date > Carbon::now()->subYears(30);
                $etiBenefit = 0;
                if ($etiEligible && $program->eti_eligible_program) {
                    // Simplified ETI calculation - would need proper ETI rates
                    $etiMonthNumber = Carbon::parse($learner->employment_start_date)->diffInMonths($periodStart) + 1;
                    if ($etiMonthNumber <= 12) {
                        $etiBenefit = min(1000, $taxableEarnings * 0.5); // Simplified
                    } elseif ($etiMonthNumber <= 24) {
                        $etiBenefit = min(500, $taxableEarnings * 0.25); // Simplified
                    }
                }

                $totalDeductions = $payeTax + $uifEmployee;
                $netPay = $grossEarnings - $totalDeductions;

                // SARS Code mappings
                $sars3601 = $basicEarnings; // Basic wages
                $sars3615 = $transportAllowance; // Travel allowance
                $sars3699 = $grossEarnings; // Total for UIF/SDL

                Payslip::create([
                    'company_id' => $program->company_id,
                    'user_id' => $learner->id,
                    'program_id' => $program->id,
                    'payslip_number' => 'PAY-' . $learner->employee_number . '-' . $periodStart->format('Ym'),
                    'payroll_period_start' => $periodStart,
                    'payroll_period_end' => $periodEnd,
                    'pay_date' => $payDate,
                    'pay_year' => $payDate->year,
                    'pay_month' => $payDate->month,
                    'tax_year' => $payDate->month >= 3 ? $payDate->year : $payDate->year - 1,
                    'tax_month_number' => $payDate->month >= 3 ? $payDate->month - 2 : $payDate->month + 10,
                    'days_worked' => $daysWorked,
                    'hours_worked' => $hoursWorked,
                    'basic_earnings' => $basicEarnings,
                    'daily_rate_used' => $program->daily_rate,
                    'transport_allowance' => $transportAllowance,
                    'meal_allowance' => $mealAllowance,
                    'other_allowances' => 0,
                    'gross_earnings' => $grossEarnings,
                    'taxable_earnings' => $taxableEarnings,
                    'paye_tax' => $payeTax,
                    'uif_employee' => $uifEmployee,
                    'uif_employer' => $uifEmployer,
                    'uif_contribution_base' => $uifBase,
                    'sdl_contribution' => $sdlContribution,
                    'eti_eligible' => $etiEligible,
                    'eti_benefit' => $etiBenefit,
                    'total_deductions' => $totalDeductions,
                    'net_pay' => $netPay,
                    'sars_3601' => $sars3601,
                    'sars_3615' => $sars3615,
                    'sars_3699' => $sars3699,
                    'status' => 'processed',
                    'is_final' => true,
                    'calculated_at' => $payDate->copy()->subDays(3),
                    'approved_at' => $payDate->copy()->subDays(1),
                    'processed_at' => $payDate,
                ]);
            }
        }
    }

    private function createPaymentSchedules(): void
    {
        $companies = Company::all();
        $programs = Program::all();

        foreach ($companies as $company) {
            $companyPrograms = $programs->where('company_id', $company->id);

            if ($companyPrograms->isEmpty()) continue;

            // Create monthly payment schedules for the past 2 months
            for ($monthsAgo = 2; $monthsAgo >= 1; $monthsAgo--) {
                $periodStart = Carbon::now()->subMonths($monthsAgo)->startOfMonth();
                $periodEnd = Carbon::now()->subMonths($monthsAgo)->endOfMonth();
                $paymentDue = $periodEnd->copy()->addDays(7);

                foreach ($companyPrograms as $program) {
                    $payslips = Payslip::where('company_id', $company->id)
                        ->where('program_id', $program->id)
                        ->whereBetween('pay_date', [$periodStart, $periodEnd])
                        ->get();

                    if ($payslips->isEmpty()) continue;

                    $learnerSummary = [];
                    $totalLearners = $payslips->count();
                    $totalDaysWorked = $payslips->sum('days_worked');
                    $totalHoursWorked = $payslips->sum('hours_worked');
                    $totalBasicEarnings = $payslips->sum('basic_earnings');
                    $totalGrossPay = $payslips->sum('gross_earnings');
                    $totalPayeTax = $payslips->sum('paye_tax');
                    $totalUifEmployee = $payslips->sum('uif_employee');
                    $totalUifEmployer = $payslips->sum('uif_employer');
                    $totalSdl = $payslips->sum('sdl_contribution');
                    $totalEtiBenefit = $payslips->sum('eti_benefit');
                    $totalNetPay = $payslips->sum('net_pay');

                    foreach ($payslips as $payslip) {
                        $learnerSummary[] = [
                            'user_id' => $payslip->user_id,
                            'name' => $payslip->user->name,
                            'employee_number' => $payslip->user->employee_number,
                            'days_worked' => $payslip->days_worked,
                            'hours_worked' => $payslip->hours_worked,
                            'basic_earnings' => $payslip->basic_earnings,
                            'gross_pay' => $payslip->gross_earnings,
                            'paye_tax' => $payslip->paye_tax,
                            'uif_employee' => $payslip->uif_employee,
                            'net_pay' => $payslip->net_pay,
                        ];
                    }

                    PaymentSchedule::create([
                        'company_id' => $company->id,
                        'program_id' => $program->id,
                        'schedule_number' => 'MTH-' . $periodStart->format('Y-m') . '-' . $program->program_code,
                        'title' => 'Monthly Payment Schedule - ' . $periodStart->format('F Y'),
                        'frequency' => 'monthly',
                        'period_start_date' => $periodStart,
                        'period_end_date' => $periodEnd,
                        'payment_due_date' => $paymentDue,
                        'attendance_cutoff_date' => $periodEnd,
                        'year' => $periodStart->year,
                        'period_number' => $periodStart->month,
                        'month_number' => $periodStart->month,
                        'learner_summary' => $learnerSummary,
                        'total_learners' => $totalLearners,
                        'total_days_worked' => $totalDaysWorked,
                        'total_hours_worked' => $totalHoursWorked,
                        'total_basic_earnings' => $totalBasicEarnings,
                        'total_gross_pay' => $totalGrossPay,
                        'total_paye_tax' => $totalPayeTax,
                        'total_uif_employee' => $totalUifEmployee,
                        'total_uif_employer' => $totalUifEmployer,
                        'total_sdl_contribution' => $totalSdl,
                        'total_eti_benefit' => $totalEtiBenefit,
                        'total_net_pay' => $totalNetPay,
                        'employer_uif_cost' => $totalUifEmployer,
                        'employer_sdl_cost' => $totalSdl,
                        'employer_eti_saving' => $totalEtiBenefit,
                        'employer_total_cost' => $totalGrossPay + $totalUifEmployer + $totalSdl - $totalEtiBenefit,
                        'status' => 'processed',
                        'is_final' => true,
                        'calculated_at' => $paymentDue->copy()->subDays(5),
                        'approved_at' => $paymentDue->copy()->subDays(2),
                        'attendance_records_count' => AttendanceRecord::where('company_id', $company->id)
                            ->where('program_id', $program->id)
                            ->whereBetween('attendance_date', [$periodStart, $periodEnd])
                            ->count(),
                    ]);
                }
            }
        }
    }
}
