<?php

namespace App\Services;

use App\Models\LeaveType;
use App\Models\LeaveBalance;
use App\Models\LeaveAccrual;
use App\Models\LeaveCarryOver;
use App\Models\LeaveRequest;
use App\Models\User;
use App\Models\Program;
use App\Models\Company;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;

class LeaveManagementService
{
    /**
     * Initialize leave balances for a new learner.
     */
    public function initializeLeaveBalances(User $user, Program $program, Company $company): LeaveBalance
    {
        $currentYear = Carbon::now()->year;
        
        // Check if balance already exists
        $existingBalance = LeaveBalance::where([
            'user_id' => $user->id,
            'program_id' => $program->id,
            'leave_year' => $currentYear,
        ])->first();

        if ($existingBalance) {
            return $existingBalance;
        }

        // Calculate service start date
        $employmentStartDate = $program->start_date ?? Carbon::now();
        $leaveYearStart = Carbon::create($currentYear, 1, 1);
        $leaveYearEnd = Carbon::create($currentYear, 12, 31);

        // Create leave balance record
        $balance = LeaveBalance::create([
            'user_id' => $user->id,
            'program_id' => $program->id,
            'company_id' => $company->id,
            'leave_year' => $currentYear,
            
            // SA-compliant leave entitlements
            'annual_leave_entitled' => 21,           // 3 weeks per year
            'annual_leave_taken' => 0,
            'annual_leave_balance' => 21,
            'annual_leave_carried_over' => 0,
            
            'sick_leave_cycle_entitled' => 30,       // 30 days per 36-month cycle
            'sick_leave_cycle_taken' => 0,
            'sick_leave_cycle_balance' => 30,
            'sick_leave_cycle_start' => $employmentStartDate,
            'sick_leave_cycle_end' => $employmentStartDate->copy()->addMonths(36),
            
            'maternity_leave_entitled' => 120,       // 4 months
            'maternity_leave_taken' => 0,
            'maternity_leave_balance' => 120,
            
            'paternity_leave_entitled' => 10,        // 10 consecutive days
            'paternity_leave_taken' => 0,
            'paternity_leave_balance' => 10,
            
            'family_responsibility_leave_entitled' => 3,  // 3 days per year
            'family_responsibility_leave_taken' => 0,
            'family_responsibility_leave_balance' => 3,
            
            'study_leave_entitled' => 5,             // Company policy
            'study_leave_taken' => 0,
            'study_leave_balance' => 5,
            
            'leave_year_start' => $leaveYearStart,
            'leave_year_end' => $leaveYearEnd,
            'employment_start_date' => $employmentStartDate,
            'is_probationary' => $employmentStartDate->diffInMonths(Carbon::now()) < 6,
            'probation_end_date' => $employmentStartDate->copy()->addMonths(6),
            
            'accrual_rate_per_month' => 1.75,        // 21/12 for annual leave
            'is_active' => true,
        ]);

        // Initialize accrual records for each leave type
        $this->initializeAccrualRecords($user, $program, $company, $employmentStartDate);

        return $balance;
    }

    /**
     * Initialize accrual records for leave types.
     */
    private function initializeAccrualRecords(User $user, Program $program, Company $company, Carbon $startDate): void
    {
        $leaveTypes = LeaveType::getActive();
        
        foreach ($leaveTypes as $leaveType) {
            // Calculate service months from start date
            $serviceMonths = $startDate->diffInMonths(Carbon::now());
            
            // Skip if not eligible yet
            if ($serviceMonths < $leaveType->min_service_months) {
                continue;
            }

            // Calculate initial entitlement based on service period
            $entitlement = $leaveType->calculateEntitlement($serviceMonths);
            
            if ($entitlement > 0) {
                LeaveAccrual::create([
                    'user_id' => $user->id,
                    'program_id' => $program->id,
                    'company_id' => $company->id,
                    'leave_type_id' => $leaveType->id,
                    'accrual_date' => $startDate,
                    'days_accrued' => $entitlement,
                    'running_balance' => $entitlement,
                    'accrual_reason' => 'initial_entitlement',
                    'notes' => "Initial {$leaveType->name} entitlement for learner",
                ]);
            }
        }
    }

    /**
     * Process leave request and validate against SA employment law.
     */
    public function processLeaveRequest(array $requestData): array
    {
        $user = User::find($requestData['user_id']);
        $leaveType = LeaveType::find($requestData['leave_type_id']);
        $startDate = Carbon::parse($requestData['start_date']);
        $endDate = Carbon::parse($requestData['end_date']);
        
        // Validate request against SA employment law
        $validation = $this->validateLeaveRequest($user, $leaveType, $startDate, $endDate);
        
        if (!$validation['valid']) {
            return [
                'success' => false,
                'errors' => $validation['errors'],
                'warnings' => $validation['warnings'] ?? [],
            ];
        }

        // Calculate leave days (excluding weekends for most leave types)
        $leaveDays = $this->calculateLeaveDays($startDate, $endDate, $leaveType);
        
        // Check available balance
        $currentBalance = LeaveAccrual::getCurrentBalance($user->id, $leaveType->id);
        
        if ($currentBalance < $leaveDays && $leaveType->code !== 'SICK') {
            return [
                'success' => false,
                'errors' => ["Insufficient {$leaveType->name} balance. Available: {$currentBalance} days, Requested: {$leaveDays} days"],
            ];
        }

        // Create leave request
        $leaveRequest = LeaveRequest::create([
            'user_id' => $user->id,
            'program_id' => $requestData['program_id'],
            'company_id' => $requestData['company_id'],
            'leave_type_id' => $leaveType->id,
            'leave_type' => $leaveType->code, // Keep for backward compatibility
            'start_date' => $startDate,
            'end_date' => $endDate,
            'reason' => $requestData['reason'] ?? '',
            'notes' => $requestData['notes'] ?? '',
            'status' => 'pending',
            'requires_medical_certificate' => $leaveType->requiresMedicalCertificate($leaveDays),
            'is_paid_leave' => $this->isPaidLeave($leaveType, $user),
            'daily_rate_at_time' => $this->getDailyRate($user),
            'total_leave_pay' => $this->calculateLeavePay($user, $leaveDays, $leaveType),
            'affects_tax_calculation' => $leaveType->is_taxable,
            'submitted_at' => Carbon::now(),
        ]);

        return [
            'success' => true,
            'leave_request' => $leaveRequest,
            'validation' => $validation,
        ];
    }

    /**
     * Validate leave request against SA employment law.
     */
    public function validateLeaveRequest(User $user, LeaveType $leaveType, Carbon $startDate, Carbon $endDate): array
    {
        $errors = [];
        $warnings = [];
        
        // Check minimum notice period
        $daysNotice = Carbon::now()->diffInDays($startDate);
        if ($daysNotice < $leaveType->min_notice_days) {
            $errors[] = "{$leaveType->name} requires minimum {$leaveType->min_notice_days} days notice. You provided {$daysNotice} days.";
        }

        // Check maximum consecutive days
        if ($leaveType->max_consecutive_days > 0) {
            $requestDays = $startDate->diffInDays($endDate) + 1;
            if ($requestDays > $leaveType->max_consecutive_days) {
                $errors[] = "{$leaveType->name} allows maximum {$leaveType->max_consecutive_days} consecutive days. You requested {$requestDays} days.";
            }
        }

        // Check if user is in probation for certain leave types
        $balance = LeaveBalance::where([
            'user_id' => $user->id,
            'leave_year' => $startDate->year,
        ])->first();

        if ($balance && $balance->is_probationary && in_array($leaveType->code, ['ANNUAL', 'STUDY'])) {
            $errors[] = "{$leaveType->name} is not available during probation period. Probation ends on {$balance->probation_end_date->format('Y-m-d')}.";
        }

        // Check minimum service months
        if ($balance && $balance->employment_start_date) {
            $serviceMonths = $balance->employment_start_date->diffInMonths(Carbon::now());
            if ($serviceMonths < $leaveType->min_service_months) {
                $errors[] = "{$leaveType->name} requires minimum {$leaveType->min_service_months} months of service. You have {$serviceMonths} months.";
            }
        }

        // Check for overlapping leave requests
        $overlapping = LeaveRequest::where('user_id', $user->id)
            ->where('status', '!=', 'rejected')
            ->where(function ($query) use ($startDate, $endDate) {
                $query->whereBetween('start_date', [$startDate, $endDate])
                      ->orWhereBetween('end_date', [$startDate, $endDate])
                      ->orWhere(function ($q) use ($startDate, $endDate) {
                          $q->where('start_date', '<=', $startDate)
                            ->where('end_date', '>=', $endDate);
                      });
            })
            ->exists();

        if ($overlapping) {
            $errors[] = "You have overlapping leave requests for this period.";
        }

        // Check expiring carry over days
        $expiringDays = LeaveCarryOver::getExpiringCarryOverDays($user->id, 30);
        if (!empty($expiringDays) && $leaveType->code === 'ANNUAL') {
            $warnings[] = "You have carry-over leave days expiring soon. Consider using them first.";
        }

        return [
            'valid' => empty($errors),
            'errors' => $errors,
            'warnings' => $warnings,
        ];
    }

    /**
     * Calculate leave days excluding weekends and public holidays.
     */
    public function calculateLeaveDays(Carbon $startDate, Carbon $endDate, LeaveType $leaveType): float
    {
        if ($leaveType->allows_partial_days) {
            // For partial days, use the exact period
            return $startDate->diffInDays($endDate) + 1;
        }

        // Calculate working days (excluding weekends)
        $workingDays = 0;
        $current = $startDate->copy();
        
        while ($current->lte($endDate)) {
            if (!$current->isWeekend()) {
                $workingDays++;
            }
            $current->addDay();
        }

        return $workingDays;
    }

    /**
     * Approve leave request and update balances.
     */
    public function approveLeaveRequest(LeaveRequest $leaveRequest, User $approver): array
    {
        try {
            DB::beginTransaction();

            // Update request status
            $leaveRequest->update([
                'status' => 'approved',
                'approved_by' => $approver->id,
                'approved_at' => Carbon::now(),
            ]);

            // Deduct from accrual balance
            $leaveDays = $this->calculateLeaveDays(
                $leaveRequest->start_date,
                $leaveRequest->end_date,
                $leaveRequest->leaveType
            );

            LeaveAccrual::deductLeaveTaken(
                $leaveRequest->user_id,
                $leaveRequest->program_id,
                $leaveRequest->company_id,
                $leaveRequest->leave_type_id,
                $leaveDays,
                "Leave taken: {$leaveRequest->start_date->format('Y-m-d')} to {$leaveRequest->end_date->format('Y-m-d')}"
            );

            // Update leave balance record
            $this->updateLeaveBalance($leaveRequest->user_id, $leaveRequest->leave_year ?? Carbon::now()->year);

            DB::commit();

            return [
                'success' => true,
                'message' => 'Leave request approved successfully',
                'days_deducted' => $leaveDays,
            ];

        } catch (\Exception $e) {
            DB::rollback();
            return [
                'success' => false,
                'error' => 'Failed to approve leave request: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Get leave balance summary for a user.
     */
    public function getLeaveBalanceSummary(User $user, ?int $year = null): array
    {
        $year = $year ?? Carbon::now()->year;
        
        $balance = LeaveBalance::where([
            'user_id' => $user->id,
            'leave_year' => $year,
        ])->first();

        if (!$balance) {
            return ['error' => 'No leave balance found for user'];
        }

        // Get current accrual balances
        $accrualSummary = LeaveAccrual::getAccrualSummary($user->id);
        
        // Get carry over information
        $carryOverSummary = LeaveCarryOver::getCarryOverSummary($user->id, $year);
        
        // Get expiring carry over days
        $expiringDays = LeaveCarryOver::getExpiringCarryOverDays($user->id);

        return [
            'balance_record' => $balance,
            'accrual_summary' => $accrualSummary,
            'carry_over_summary' => $carryOverSummary,
            'expiring_carry_over' => $expiringDays,
            'service_info' => [
                'employment_start_date' => $balance->employment_start_date,
                'is_probationary' => $balance->is_probationary,
                'probation_end_date' => $balance->probation_end_date,
                'service_months' => $balance->employment_start_date ? 
                    $balance->employment_start_date->diffInMonths(Carbon::now()) : 0,
            ],
        ];
    }

    /**
     * Generate SARS-compliant leave report.
     */
    public function generateSARSLeaveReport(Company $company, int $year): array
    {
        $leaveRequests = LeaveRequest::where('company_id', $company->id)
            ->whereYear('start_date', $year)
            ->where('status', 'approved')
            ->with(['user', 'leaveType'])
            ->get();

        $report = [
            'company' => $company->name,
            'year' => $year,
            'total_leave_pay' => 0,
            'total_taxable_leave' => 0,
            'leave_types' => [],
            'employees' => [],
        ];

        foreach ($leaveRequests as $request) {
            $leaveType = $request->leaveType;
            $user = $request->user;
            
            // Group by leave type
            if (!isset($report['leave_types'][$leaveType->code])) {
                $report['leave_types'][$leaveType->code] = [
                    'name' => $leaveType->name,
                    'sars_code' => $leaveType->sars_code,
                    'total_days' => 0,
                    'total_pay' => 0,
                    'is_taxable' => $leaveType->is_taxable,
                    'employee_count' => 0,
                ];
            }

            $leaveDays = $this->calculateLeaveDays($request->start_date, $request->end_date, $leaveType);
            $leavePay = $request->total_leave_pay ?? 0;

            $report['leave_types'][$leaveType->code]['total_days'] += $leaveDays;
            $report['leave_types'][$leaveType->code]['total_pay'] += $leavePay;
            $report['total_leave_pay'] += $leavePay;

            if ($leaveType->is_taxable) {
                $report['total_taxable_leave'] += $leavePay;
            }

            // Group by employee
            if (!isset($report['employees'][$user->id])) {
                $report['employees'][$user->id] = [
                    'name' => $user->full_name,
                    'id_number' => $user->id_number,
                    'total_leave_days' => 0,
                    'total_leave_pay' => 0,
                    'leave_types' => [],
                ];
            }

            $report['employees'][$user->id]['total_leave_days'] += $leaveDays;
            $report['employees'][$user->id]['total_leave_pay'] += $leavePay;
            
            if (!isset($report['employees'][$user->id]['leave_types'][$leaveType->code])) {
                $report['employees'][$user->id]['leave_types'][$leaveType->code] = [
                    'name' => $leaveType->name,
                    'days' => 0,
                    'pay' => 0,
                ];
            }
            
            $report['employees'][$user->id]['leave_types'][$leaveType->code]['days'] += $leaveDays;
            $report['employees'][$user->id]['leave_types'][$leaveType->code]['pay'] += $leavePay;
        }

        return $report;
    }

    /**
     * Helper methods
     */
    private function isPaidLeave(LeaveType $leaveType, User $user): bool
    {
        // Most leave types are paid in SA
        return !in_array($leaveType->code, ['UNPAID', 'STUDY_UNPAID']);
    }

    private function getDailyRate(User $user): float
    {
        // Get from payslip or program daily rate
        $program = $user->programs()->where('status', 'active')->first();
        return $program ? $program->daily_rate : 0;
    }

    private function calculateLeavePay(User $user, float $days, LeaveType $leaveType): float
    {
        if (!$this->isPaidLeave($leaveType, $user)) {
            return 0;
        }

        $dailyRate = $this->getDailyRate($user);
        return $dailyRate * $days;
    }

    private function updateLeaveBalance(int $userId, int $year): void
    {
        $balance = LeaveBalance::where([
            'user_id' => $userId,
            'leave_year' => $year,
        ])->first();

        if ($balance) {
            $balance->updateBalances();
        }
    }
}