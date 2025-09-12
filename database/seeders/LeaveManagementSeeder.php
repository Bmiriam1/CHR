<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\LeaveType;
use App\Models\LeaveBalance;
use App\Models\LeaveRequest;
use App\Models\LeaveAccrual;
use App\Models\LeaveCarryOver;
use App\Models\User;
use App\Models\Program;
use App\Models\Company;
use App\Services\LeaveManagementService;
use Carbon\Carbon;
use Faker\Factory as Faker;

class LeaveManagementSeeder extends Seeder
{
    protected $faker;
    protected $leaveService;

    public function __construct()
    {
        $this->faker = Faker::create();
        $this->leaveService = app(LeaveManagementService::class);
    }

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        if ($this->command) {
            $this->command->info('Starting Leave Management Seeder...');
        }

        // Step 1: Create leave types
        $this->createLeaveTypes();

        // Step 2: Initialize leave balances for existing users
        $this->initializeLeaveBalances();

        // Step 3: Create sample leave requests
        $this->createSampleLeaveRequests();

        // Step 4: Create sample accrual records
        $this->createSampleAccruals();

        // Step 5: Create sample carry over records
        $this->createSampleCarryOvers();

        if ($this->command) {
            $this->command->info('Leave Management Seeder completed successfully!');
        }
    }

    /**
     * Create standard South African leave types
     */
    private function createLeaveTypes(): void
    {
        if ($this->command) {
            $this->command->info('Creating leave types...');
        }

        LeaveType::createSAStandardTypes();

        if ($this->command) {
            $this->command->info('✓ Leave types created successfully');
        }
    }

    /**
     * Initialize leave balances for existing users
     */
    private function initializeLeaveBalances(): void
    {
        if ($this->command) {
            $this->command->info('Initializing leave balances...');
        }

        $users = User::whereHas('programs')->get();
        $processed = 0;

        foreach ($users as $user) {
            $programs = $user->programs()->where('programs.status', 'active')->get();

            foreach ($programs as $program) {
                // Check if balance already exists
                $existingBalance = LeaveBalance::where([
                    'user_id' => $user->id,
                    'program_id' => $program->id,
                    'leave_year' => Carbon::now()->year,
                ])->first();

                if (!$existingBalance) {
                    $this->leaveService->initializeLeaveBalances($user, $program, $program->company);
                    $processed++;
                }
            }
        }

        if ($this->command) {
            $this->command->info("✓ Leave balances initialized for {$processed} user-program combinations");
        }
    }

    /**
     * Create sample leave requests
     */
    private function createSampleLeaveRequests(): void
    {
        if ($this->command) {
            $this->command->info('Creating sample leave requests...');
        }

        $users = User::whereHas('programs')->with('programs')->get();
        $leaveTypes = LeaveType::where('is_active', true)->get();
        $created = 0;

        foreach ($users as $user) {
            $programs = $user->programs()->where('programs.status', 'active')->get();

            foreach ($programs as $program) {
                // Create 1-3 leave requests per user-program combination
                $numRequests = $this->faker->numberBetween(1, 3);

                for ($i = 0; $i < $numRequests; $i++) {
                    $leaveType = $leaveTypes->random();

                    // Generate realistic date ranges
                    $startDate = $this->generateRealisticLeaveDate();
                    $endDate = $startDate->copy()->addDays($this->faker->numberBetween(1, $leaveType->max_consecutive_days ?: 5));

                    // Check if dates are in the past (for approved/rejected requests) or future (for pending)
                    $isPast = $this->faker->boolean(60); // 60% chance of past dates
                    if ($isPast && $startDate->isFuture()) {
                        $startDate = $startDate->subMonths($this->faker->numberBetween(1, 12));
                        $endDate = $startDate->copy()->addDays($this->faker->numberBetween(1, $leaveType->max_consecutive_days ?: 5));
                    }

                    // Generate status based on date
                    $status = $this->generateLeaveStatus($startDate, $isPast);

                    // Calculate leave days
                    $leaveDays = $this->leaveService->calculateLeaveDays($startDate, $endDate, $leaveType);

                    // Get daily rate from program
                    $dailyRate = $program->daily_rate ?? 100;
                    $totalPay = $leaveDays * $dailyRate;

                    // Map new leave types to existing enum values
                    $mappedLeaveType = $this->mapLeaveTypeToEnum($leaveType->code);

                    $leaveRequest = LeaveRequest::create([
                        'user_id' => $user->id,
                        'program_id' => $program->id,
                        'company_id' => $program->company_id,
                        'leave_type_id' => $leaveType->id,
                        'leave_type' => $mappedLeaveType,
                        'start_date' => $startDate,
                        'end_date' => $endDate,
                        'reason' => $this->generateLeaveReason($leaveType->code),
                        'notes' => $this->faker->optional(0.3)->sentence(),
                        'status' => $status,
                        'is_emergency' => $this->faker->boolean(10), // 10% emergency requests
                        'requires_medical_certificate' => $leaveType->requiresMedicalCertificate($leaveDays),
                        'medical_certificate_path' => $leaveType->requiresMedicalCertificate($leaveDays) && $this->faker->boolean(80)
                            ? 'medical-certificates/sample-' . $this->faker->uuid() . '.pdf'
                            : null,
                        'is_paid_leave' => !in_array($leaveType->code, ['UNPAID', 'STUDY_UNPAID']),
                        'daily_rate_at_time' => $dailyRate,
                        'total_leave_pay' => $totalPay,
                        'affects_tax_calculation' => $leaveType->is_taxable,
                        'is_advance_leave' => $this->faker->boolean(5), // 5% advance requests
                        'submitted_at' => $startDate->copy()->subDays($this->faker->numberBetween(1, 30)),
                    ]);

                    // Add approval information for approved/rejected requests
                    if (in_array($status, ['approved', 'rejected'])) {
                        $adminUser = User::whereHas('roles', function ($query) {
                            $query->whereIn('name', ['admin', 'hr_manager', 'company_admin']);
                        })->first();

                        if ($adminUser) {
                            $leaveRequest->update([
                                'approved_by' => $adminUser->id,
                                'approved_at' => $leaveRequest->submitted_at->copy()->addDays($this->faker->numberBetween(1, 7)),
                                'rejection_reason' => $status === 'rejected' ? $this->faker->sentence() : null,
                            ]);
                        }
                    }

                    $created++;
                }
            }
        }

        if ($this->command) {
            $this->command->info("✓ Created {$created} sample leave requests");
        }
    }

    /**
     * Create sample accrual records
     */
    private function createSampleAccruals(): void
    {
        if ($this->command) {
            $this->command->info('Creating sample accrual records...');
        }

        $users = User::whereHas('programs')->get();
        $leaveTypes = LeaveType::where('is_active', true)->get();
        $created = 0;

        foreach ($users as $user) {
            $programs = $user->programs()->where('programs.status', 'active')->get();

            foreach ($programs as $program) {
                $employmentStart = $program->start_date ?? Carbon::now()->subMonths($this->faker->numberBetween(6, 24));

                foreach ($leaveTypes as $leaveType) {
                    // Create initial entitlement accrual
                    if ($leaveType->annual_entitlement_days > 0) {
                        LeaveAccrual::create([
                            'user_id' => $user->id,
                            'program_id' => $program->id,
                            'company_id' => $program->company_id,
                            'leave_type_id' => $leaveType->id,
                            'accrual_date' => $employmentStart,
                            'days_accrued' => $leaveType->annual_entitlement_days,
                            'running_balance' => $leaveType->annual_entitlement_days,
                            'accrual_reason' => 'initial_entitlement',
                            'notes' => "Initial {$leaveType->name} entitlement",
                        ]);
                        $created++;
                    }

                    // Create monthly accruals if applicable
                    if ($leaveType->accrual_rate_per_month > 0) {
                        $monthsSinceStart = $employmentStart->diffInMonths(Carbon::now());
                        $currentBalance = $leaveType->annual_entitlement_days;

                        for ($month = 1; $month <= $monthsSinceStart; $month++) {
                            $accrualDate = $employmentStart->copy()->addMonths($month);

                            // Skip if accrual date is in the future
                            if ($accrualDate->isFuture()) {
                                break;
                            }

                            $monthlyAccrual = $leaveType->accrual_rate_per_month;
                            $currentBalance += $monthlyAccrual;

                            LeaveAccrual::create([
                                'user_id' => $user->id,
                                'program_id' => $program->id,
                                'company_id' => $program->company_id,
                                'leave_type_id' => $leaveType->id,
                                'accrual_date' => $accrualDate,
                                'days_accrued' => $monthlyAccrual,
                                'running_balance' => $currentBalance,
                                'accrual_reason' => 'monthly',
                                'notes' => "Monthly {$leaveType->name} accrual",
                            ]);
                            $created++;
                        }
                    }

                    // Create some bonus accruals (performance bonuses, etc.)
                    if ($this->faker->boolean(20)) { // 20% chance
                        LeaveAccrual::createBonusAccrual(
                            $user->id,
                            $program->id,
                            $program->company_id,
                            $leaveType->id,
                            $this->faker->randomFloat(1, 0.5, 2.0),
                            'bonus',
                            'Performance bonus accrual'
                        );
                        $created++;
                    }
                }
            }
        }

        if ($this->command) {
            $this->command->info("✓ Created {$created} sample accrual records");
        }
    }

    /**
     * Create sample carry over records
     */
    private function createSampleCarryOvers(): void
    {
        if ($this->command) {
            $this->command->info('Creating sample carry over records...');
        }

        $users = User::whereHas('programs')->get();
        $leaveTypes = LeaveType::where('is_active', true)->get();
        $created = 0;

        // Process carry overs for previous year
        $previousYear = Carbon::now()->year - 1;

        foreach ($users as $user) {
            $programs = $user->programs()->where('programs.status', 'active')->get();

            foreach ($programs as $program) {
                foreach ($leaveTypes as $leaveType) {
                    // Only annual leave typically carries over
                    if ($leaveType->code !== 'ANNUAL' || $leaveType->max_carry_over_days <= 0) {
                        continue;
                    }

                    // Simulate some users having unused leave from previous year
                    if ($this->faker->boolean(30)) { // 30% of users have carry over
                        $unusedBalance = $this->faker->randomFloat(1, 1, $leaveType->max_carry_over_days);
                        $carriedOver = min($unusedBalance, $leaveType->max_carry_over_days);
                        $forfeited = max(0, $unusedBalance - $leaveType->max_carry_over_days);

                        $expiryDate = Carbon::create($previousYear + 1, 6, 30); // June 30th

                        LeaveCarryOver::create([
                            'user_id' => $user->id,
                            'program_id' => $program->id,
                            'company_id' => $program->company_id,
                            'leave_type_id' => $leaveType->id,
                            'from_year' => $previousYear,
                            'to_year' => $previousYear + 1,
                            'balance_at_year_end' => $unusedBalance,
                            'carried_over_days' => $carriedOver,
                            'forfeited_days' => $forfeited,
                            'carry_over_expiry_date' => $expiryDate,
                            'notes' => "Year-end carry over from {$previousYear}",
                        ]);

                        // Create accrual record for carried over days
                        if ($carriedOver > 0) {
                            LeaveAccrual::createCarryOverAccrual(
                                $user->id,
                                $program->id,
                                $program->company_id,
                                $leaveType->id,
                                $carriedOver,
                                "Carried over from {$previousYear} (expires {$expiryDate->format('Y-m-d')})"
                            );
                        }

                        $created++;
                    }
                }
            }
        }

        if ($this->command) {
            $this->command->info("✓ Created {$created} sample carry over records");
        }
    }

    /**
     * Generate realistic leave dates
     */
    private function generateRealisticLeaveDate(): Carbon
    {
        $now = Carbon::now();

        // 70% chance of dates in the past, 30% in the future
        if ($this->faker->boolean(70)) {
            return $now->copy()->subDays($this->faker->numberBetween(1, 365));
        } else {
            return $now->copy()->addDays($this->faker->numberBetween(1, 90));
        }
    }

    /**
     * Generate leave status based on date
     */
    private function generateLeaveStatus(Carbon $startDate, bool $isPast): string
    {
        if ($isPast) {
            // Past dates: mostly approved, some rejected
            return $this->faker->randomElement(['approved', 'approved', 'approved', 'approved', 'approved', 'approved', 'approved', 'approved', 'approved', 'rejected']);
        } else {
            // Future dates: mostly pending, some approved
            return $this->faker->randomElement(['pending', 'pending', 'pending', 'pending', 'pending', 'pending', 'pending', 'approved', 'approved', 'approved']);
        }
    }

    /**
     * Map new leave types to existing enum values
     */
    private function mapLeaveTypeToEnum(string $leaveTypeCode): string
    {
        $mapping = [
            'ANNUAL' => 'personal',
            'SICK' => 'sick',
            'MATERNITY' => 'personal',
            'PATERNITY' => 'personal',
            'FAMILY' => 'personal',
            'STUDY' => 'personal',
        ];

        return $mapping[$leaveTypeCode] ?? 'other';
    }

    /**
     * Generate realistic leave reasons based on leave type
     */
    private function generateLeaveReason(string $leaveType): string
    {
        $reasons = [
            'ANNUAL' => [
                'Family vacation',
                'Personal time off',
                'Rest and relaxation',
                'Travel',
                'Personal matters',
                'Holiday break',
            ],
            'SICK' => [
                'Illness',
                'Medical appointment',
                'Flu symptoms',
                'Doctor consultation',
                'Recovery time',
                'Health issues',
            ],
            'MATERNITY' => [
                'Maternity leave',
                'Birth of child',
                'Postnatal care',
                'Newborn care',
            ],
            'PATERNITY' => [
                'Paternity leave',
                'Birth of child',
                'Supporting partner',
                'Newborn care',
            ],
            'FAMILY' => [
                'Family emergency',
                'Child illness',
                'Family responsibility',
                'Supporting family member',
            ],
            'STUDY' => [
                'Study leave',
                'Exam preparation',
                'Course attendance',
                'Academic requirements',
            ],
        ];

        $typeReasons = $reasons[$leaveType] ?? ['Personal leave'];
        return $this->faker->randomElement($typeReasons);
    }
}
