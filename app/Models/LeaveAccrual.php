<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class LeaveAccrual extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'program_id',
        'company_id',
        'leave_type_id',
        'accrual_date',
        'days_accrued',
        'running_balance',
        'accrual_reason',
        'notes',
    ];

    protected $casts = [
        'accrual_date' => 'date',
        'days_accrued' => 'decimal:2',
        'running_balance' => 'decimal:2',
    ];

    /**
     * Get the user that owns the accrual.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the program this accrual belongs to.
     */
    public function program(): BelongsTo
    {
        return $this->belongsTo(Program::class);
    }

    /**
     * Get the company this accrual belongs to.
     */
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * Get the leave type for this accrual.
     */
    public function leaveType(): BelongsTo
    {
        return $this->belongsTo(LeaveType::class);
    }

    /**
     * Process monthly accruals for all users.
     */
    public static function processMonthlyAccruals(): array
    {
        $processed = [];
        $date = Carbon::now()->startOfMonth();
        
        // Get all active leave balances
        $leaveBalances = LeaveBalance::where('is_active', true)
            ->with(['user', 'program', 'company'])
            ->get();

        foreach ($leaveBalances as $balance) {
            $user = $balance->user;
            $program = $balance->program;
            
            if (!$program || !$program->start_date) {
                continue;
            }

            // Calculate service months
            $serviceMonths = $program->start_date->diffInMonths($date);
            
            // Process accruals for each leave type
            $leaveTypes = LeaveType::getActive();
            
            foreach ($leaveTypes as $leaveType) {
                $monthlyAccrual = $leaveType->calculateMonthlyAccrual($serviceMonths);
                
                if ($monthlyAccrual <= 0) {
                    continue;
                }

                // Check if accrual already exists for this month
                $existingAccrual = self::where([
                    'user_id' => $user->id,
                    'program_id' => $program->id,
                    'leave_type_id' => $leaveType->id,
                    'accrual_date' => $date,
                    'accrual_reason' => 'monthly'
                ])->first();

                if ($existingAccrual) {
                    continue;
                }

                // Get current balance
                $currentBalance = self::where([
                    'user_id' => $user->id,
                    'leave_type_id' => $leaveType->id
                ])->orderBy('accrual_date', 'desc')->first();

                $runningBalance = $currentBalance ? $currentBalance->running_balance + $monthlyAccrual : $monthlyAccrual;

                // Create accrual record
                $accrual = self::create([
                    'user_id' => $user->id,
                    'program_id' => $program->id,
                    'company_id' => $balance->company_id,
                    'leave_type_id' => $leaveType->id,
                    'accrual_date' => $date,
                    'days_accrued' => $monthlyAccrual,
                    'running_balance' => $runningBalance,
                    'accrual_reason' => 'monthly',
                    'notes' => "Monthly accrual for {$leaveType->name}",
                ]);

                $processed[] = [
                    'user' => $user->full_name,
                    'leave_type' => $leaveType->name,
                    'days_accrued' => $monthlyAccrual,
                    'running_balance' => $runningBalance,
                ];
            }
        }

        return $processed;
    }

    /**
     * Create bonus accrual (e.g., for good performance).
     */
    public static function createBonusAccrual(
        int $userId,
        int $programId,
        int $companyId,
        int $leaveTypeId,
        float $days,
        string $reason = 'bonus',
        ?string $notes = null
    ): self {
        // Get current balance
        $currentBalance = self::where([
            'user_id' => $userId,
            'leave_type_id' => $leaveTypeId
        ])->orderBy('accrual_date', 'desc')->first();

        $runningBalance = $currentBalance ? $currentBalance->running_balance + $days : $days;

        return self::create([
            'user_id' => $userId,
            'program_id' => $programId,
            'company_id' => $companyId,
            'leave_type_id' => $leaveTypeId,
            'accrual_date' => Carbon::now(),
            'days_accrued' => $days,
            'running_balance' => $runningBalance,
            'accrual_reason' => $reason,
            'notes' => $notes,
        ]);
    }

    /**
     * Create carry over accrual from previous year.
     */
    public static function createCarryOverAccrual(
        int $userId,
        int $programId,
        int $companyId,
        int $leaveTypeId,
        float $days,
        ?string $notes = null
    ): self {
        return self::createBonusAccrual(
            $userId,
            $programId,
            $companyId,
            $leaveTypeId,
            $days,
            'carry_over',
            $notes ?? 'Carried over from previous year'
        );
    }

    /**
     * Deduct leave taken.
     */
    public static function deductLeaveTaken(
        int $userId,
        int $programId,
        int $companyId,
        int $leaveTypeId,
        float $days,
        ?string $notes = null
    ): self {
        // Get current balance
        $currentBalance = self::where([
            'user_id' => $userId,
            'leave_type_id' => $leaveTypeId
        ])->orderBy('accrual_date', 'desc')->first();

        $runningBalance = $currentBalance ? $currentBalance->running_balance - $days : -$days;

        return self::create([
            'user_id' => $userId,
            'program_id' => $programId,
            'company_id' => $companyId,
            'leave_type_id' => $leaveTypeId,
            'accrual_date' => Carbon::now(),
            'days_accrued' => -$days, // Negative for deduction
            'running_balance' => $runningBalance,
            'accrual_reason' => 'leave_taken',
            'notes' => $notes ?? 'Leave taken deduction',
        ]);
    }

    /**
     * Get accrual summary for a user.
     */
    public static function getAccrualSummary(int $userId, ?int $leaveTypeId = null): array
    {
        $query = self::where('user_id', $userId);
        
        if ($leaveTypeId) {
            $query->where('leave_type_id', $leaveTypeId);
        }

        $accruals = $query->with('leaveType')
            ->orderBy('accrual_date', 'desc')
            ->get()
            ->groupBy('leave_type_id');

        $summary = [];
        
        foreach ($accruals as $leaveTypeId => $typeAccruals) {
            $leaveType = $typeAccruals->first()->leaveType;
            $currentBalance = $typeAccruals->first()->running_balance;
            $totalAccrued = $typeAccruals->where('days_accrued', '>', 0)->sum('days_accrued');
            $totalDeducted = $typeAccruals->where('days_accrued', '<', 0)->sum('days_accrued');

            $summary[] = [
                'leave_type' => $leaveType->name,
                'leave_type_code' => $leaveType->code,
                'current_balance' => $currentBalance,
                'total_accrued' => $totalAccrued,
                'total_deducted' => abs($totalDeducted),
                'last_accrual_date' => $typeAccruals->first()->accrual_date,
                'accrual_count' => $typeAccruals->count(),
            ];
        }

        return $summary;
    }

    /**
     * Get current balance for a user and leave type.
     */
    public static function getCurrentBalance(int $userId, int $leaveTypeId): float
    {
        $latestAccrual = self::where([
            'user_id' => $userId,
            'leave_type_id' => $leaveTypeId
        ])->orderBy('accrual_date', 'desc')->first();

        return $latestAccrual ? $latestAccrual->running_balance : 0;
    }
}