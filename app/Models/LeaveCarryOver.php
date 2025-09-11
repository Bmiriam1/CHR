<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class LeaveCarryOver extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'program_id',
        'company_id',
        'leave_type_id',
        'from_year',
        'to_year',
        'balance_at_year_end',
        'carried_over_days',
        'forfeited_days',
        'carry_over_expiry_date',
        'notes',
    ];

    protected $casts = [
        'from_year' => 'integer',
        'to_year' => 'integer',
        'balance_at_year_end' => 'decimal:2',
        'carried_over_days' => 'decimal:2',
        'forfeited_days' => 'decimal:2',
        'carry_over_expiry_date' => 'date',
    ];

    /**
     * Get the user that owns the carry over.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the program this carry over belongs to.
     */
    public function program(): BelongsTo
    {
        return $this->belongsTo(Program::class);
    }

    /**
     * Get the company this carry over belongs to.
     */
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * Get the leave type for this carry over.
     */
    public function leaveType(): BelongsTo
    {
        return $this->belongsTo(LeaveType::class);
    }

    /**
     * Process year-end carry overs for all users.
     */
    public static function processYearEndCarryOvers(int $fromYear): array
    {
        $processed = [];
        $toYear = $fromYear + 1;
        
        // Get all active leave balances
        $leaveBalances = LeaveBalance::where('is_active', true)
            ->where('leave_year', $fromYear)
            ->with(['user', 'program', 'company'])
            ->get();

        foreach ($leaveBalances as $balance) {
            $user = $balance->user;
            $program = $balance->program;
            
            // Get all leave types
            $leaveTypes = LeaveType::getActive();
            
            foreach ($leaveTypes as $leaveType) {
                // Get current balance for this leave type
                $currentBalance = LeaveAccrual::getCurrentBalance($user->id, $leaveType->id);
                
                if ($currentBalance <= 0) {
                    continue;
                }

                // Calculate carry over based on leave type rules
                $maxCarryOver = $leaveType->max_carry_over_days;
                $carriedOverDays = min($currentBalance, $maxCarryOver);
                $forfeitedDays = max(0, $currentBalance - $maxCarryOver);

                // Check if carry over already exists
                $existingCarryOver = self::where([
                    'user_id' => $user->id,
                    'leave_type_id' => $leaveType->id,
                    'from_year' => $fromYear,
                    'to_year' => $toYear,
                ])->first();

                if ($existingCarryOver) {
                    continue;
                }

                // Set expiry date (usually carried over leave expires after 6 months)
                $expiryDate = Carbon::create($toYear, 6, 30); // June 30th of following year

                // Create carry over record
                $carryOver = self::create([
                    'user_id' => $user->id,
                    'program_id' => $program->id,
                    'company_id' => $balance->company_id,
                    'leave_type_id' => $leaveType->id,
                    'from_year' => $fromYear,
                    'to_year' => $toYear,
                    'balance_at_year_end' => $currentBalance,
                    'carried_over_days' => $carriedOverDays,
                    'forfeited_days' => $forfeitedDays,
                    'carry_over_expiry_date' => $expiryDate,
                    'notes' => "Year-end carry over from {$fromYear} to {$toYear}",
                ]);

                // Create accrual record for the carried over days in the new year
                if ($carriedOverDays > 0) {
                    LeaveAccrual::createCarryOverAccrual(
                        $user->id,
                        $program->id,
                        $balance->company_id,
                        $leaveType->id,
                        $carriedOverDays,
                        "Carried over from {$fromYear} (expires {$expiryDate->format('Y-m-d')})"
                    );
                }

                $processed[] = [
                    'user' => $user->full_name,
                    'leave_type' => $leaveType->name,
                    'balance_at_year_end' => $currentBalance,
                    'carried_over' => $carriedOverDays,
                    'forfeited' => $forfeitedDays,
                    'expiry_date' => $expiryDate->format('Y-m-d'),
                ];
            }
        }

        return $processed;
    }

    /**
     * Process expired carry over days.
     */
    public static function processExpiredCarryOvers(): array
    {
        $today = Carbon::now();
        $processed = [];

        // Get all carry overs that have expired
        $expiredCarryOvers = self::where('carry_over_expiry_date', '<', $today)
            ->whereHas('leaveAccrual', function ($query) {
                $query->where('accrual_reason', 'carry_over')
                      ->where('running_balance', '>', 0);
            })
            ->with(['user', 'leaveType'])
            ->get();

        foreach ($expiredCarryOvers as $carryOver) {
            // Get the current balance for carry over days
            $currentCarryOverBalance = LeaveAccrual::where([
                'user_id' => $carryOver->user_id,
                'leave_type_id' => $carryOver->leave_type_id,
                'accrual_reason' => 'carry_over'
            ])->orderBy('accrual_date', 'desc')->first();

            if (!$currentCarryOverBalance || $currentCarryOverBalance->running_balance <= 0) {
                continue;
            }

            // Deduct the remaining carry over balance
            LeaveAccrual::deductLeaveTaken(
                $carryOver->user_id,
                $carryOver->program_id,
                $carryOver->company_id,
                $carryOver->leave_type_id,
                $currentCarryOverBalance->running_balance,
                "Expired carry over days from {$carryOver->from_year} (expired {$carryOver->carry_over_expiry_date->format('Y-m-d')})"
            );

            // Update the carry over record to show it has been processed
            $carryOver->update([
                'notes' => $carryOver->notes . " | Expired on {$today->format('Y-m-d')} - {$currentCarryOverBalance->running_balance} days forfeited"
            ]);

            $processed[] = [
                'user' => $carryOver->user->full_name,
                'leave_type' => $carryOver->leaveType->name,
                'expired_days' => $currentCarryOverBalance->running_balance,
                'expiry_date' => $carryOver->carry_over_expiry_date->format('Y-m-d'),
            ];
        }

        return $processed;
    }

    /**
     * Get carry over summary for a user.
     */
    public static function getCarryOverSummary(int $userId, ?int $year = null): array
    {
        $query = self::where('user_id', $userId);
        
        if ($year) {
            $query->where('from_year', $year);
        }

        $carryOvers = $query->with('leaveType')
            ->orderBy('from_year', 'desc')
            ->get()
            ->groupBy('from_year');

        $summary = [];
        
        foreach ($carryOvers as $year => $yearCarryOvers) {
            $yearSummary = [
                'year' => $year,
                'total_balance_at_year_end' => $yearCarryOvers->sum('balance_at_year_end'),
                'total_carried_over' => $yearCarryOvers->sum('carried_over_days'),
                'total_forfeited' => $yearCarryOvers->sum('forfeited_days'),
                'leave_types' => [],
            ];

            foreach ($yearCarryOvers as $carryOver) {
                $yearSummary['leave_types'][] = [
                    'leave_type' => $carryOver->leaveType->name,
                    'balance_at_year_end' => $carryOver->balance_at_year_end,
                    'carried_over' => $carryOver->carried_over_days,
                    'forfeited' => $carryOver->forfeited_days,
                    'expiry_date' => $carryOver->carry_over_expiry_date,
                    'is_expired' => $carryOver->carry_over_expiry_date < Carbon::now(),
                ];
            }

            $summary[] = $yearSummary;
        }

        return $summary;
    }

    /**
     * Get active carry over days for a user and leave type.
     */
    public static function getActiveCarryOverDays(int $userId, int $leaveTypeId): float
    {
        $today = Carbon::now();
        
        $carryOver = self::where([
            'user_id' => $userId,
            'leave_type_id' => $leaveTypeId,
        ])
        ->where('carry_over_expiry_date', '>=', $today)
        ->where('carried_over_days', '>', 0)
        ->orderBy('carry_over_expiry_date', 'asc')
        ->first();

        if (!$carryOver) {
            return 0;
        }

        // Get the current balance for carry over accruals
        $carryOverBalance = LeaveAccrual::where([
            'user_id' => $userId,
            'leave_type_id' => $leaveTypeId,
            'accrual_reason' => 'carry_over'
        ])->orderBy('accrual_date', 'desc')->first();

        return $carryOverBalance ? max(0, $carryOverBalance->running_balance) : 0;
    }

    /**
     * Check if a user has expiring carry over days.
     */
    public static function getExpiringCarryOverDays(int $userId, int $daysUntilExpiry = 30): array
    {
        $expiryDate = Carbon::now()->addDays($daysUntilExpiry);
        
        $expiringCarryOvers = self::where('user_id', $userId)
            ->where('carry_over_expiry_date', '<=', $expiryDate)
            ->where('carry_over_expiry_date', '>=', Carbon::now())
            ->where('carried_over_days', '>', 0)
            ->with('leaveType')
            ->get();

        $expiring = [];
        
        foreach ($expiringCarryOvers as $carryOver) {
            $remainingDays = self::getActiveCarryOverDays($userId, $carryOver->leave_type_id);
            
            if ($remainingDays > 0) {
                $expiring[] = [
                    'leave_type' => $carryOver->leaveType->name,
                    'remaining_days' => $remainingDays,
                    'expiry_date' => $carryOver->carry_over_expiry_date,
                    'days_until_expiry' => $carryOver->carry_over_expiry_date->diffInDays(Carbon::now()),
                ];
            }
        }

        return $expiring;
    }
}