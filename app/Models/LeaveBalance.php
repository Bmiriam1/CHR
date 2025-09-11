<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class LeaveBalance extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'program_id',
        'company_id',
        'leave_year',
        'sick_leave_entitled',
        'sick_leave_taken',
        'sick_leave_balance',
        'personal_leave_entitled',
        'personal_leave_taken',
        'personal_leave_balance',
        'emergency_leave_entitled',
        'emergency_leave_taken',
        'emergency_leave_balance',
        'other_leave_entitled',
        'other_leave_taken',
        'other_leave_balance',
        'total_entitled',
        'total_taken',
        'total_balance',
        'accrual_rate_per_month',
        'is_active',
    ];

    protected $casts = [
        'sick_leave_entitled' => 'decimal:2',
        'sick_leave_taken' => 'decimal:2',
        'sick_leave_balance' => 'decimal:2',
        'personal_leave_entitled' => 'decimal:2',
        'personal_leave_taken' => 'decimal:2',
        'personal_leave_balance' => 'decimal:2',
        'emergency_leave_entitled' => 'decimal:2',
        'emergency_leave_taken' => 'decimal:2',
        'emergency_leave_balance' => 'decimal:2',
        'other_leave_entitled' => 'decimal:2',
        'other_leave_taken' => 'decimal:2',
        'other_leave_balance' => 'decimal:2',
        'total_entitled' => 'decimal:2',
        'total_taken' => 'decimal:2',
        'total_balance' => 'decimal:2',
        'accrual_rate_per_month' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function program(): BelongsTo
    {
        return $this->belongsTo(Program::class);
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * Calculate accrued leave based on program start date
     */
    public function calculateAccruedLeave(): array
    {
        $program = $this->program;
        if (!$program || !$program->start_date) {
            return [
                'months_elapsed' => 0,
                'accrued_days' => 0,
                'remaining_entitlement' => 0,
            ];
        }

        $startDate = Carbon::parse($program->start_date);
        $currentDate = Carbon::now();
        $monthsElapsed = $startDate->diffInMonths($currentDate);

        // Cap at 12 months for annual entitlement
        $monthsElapsed = min($monthsElapsed, 12);

        $accruedDays = $monthsElapsed * $this->accrual_rate_per_month;
        $remainingEntitlement = max(0, $this->total_entitled - $accruedDays);

        return [
            'months_elapsed' => $monthsElapsed,
            'accrued_days' => round($accruedDays, 2),
            'remaining_entitlement' => round($remainingEntitlement, 2),
        ];
    }

    /**
     * Get leave utilization percentage
     */
    public function getUtilizationPercentage(): float
    {
        if ($this->total_entitled == 0) {
            return 0;
        }

        return round(($this->total_taken / $this->total_entitled) * 100, 1);
    }

    /**
     * Get leave balance status
     */
    public function getBalanceStatus(): string
    {
        $utilization = $this->getUtilizationPercentage();

        if ($utilization >= 90) {
            return 'critical';
        } elseif ($utilization >= 75) {
            return 'warning';
        } elseif ($utilization >= 50) {
            return 'moderate';
        } else {
            return 'good';
        }
    }

    /**
     * Get balance status color
     */
    public function getBalanceStatusColor(): string
    {
        return match ($this->getBalanceStatus()) {
            'critical' => 'text-red-600',
            'warning' => 'text-yellow-600',
            'moderate' => 'text-blue-600',
            'good' => 'text-green-600',
            default => 'text-gray-600',
        };
    }

    /**
     * Get balance status badge class
     */
    public function getBalanceStatusBadgeClass(): string
    {
        return match ($this->getBalanceStatus()) {
            'critical' => 'bg-red-100 text-red-800',
            'warning' => 'bg-yellow-100 text-yellow-800',
            'moderate' => 'bg-blue-100 text-blue-800',
            'good' => 'bg-green-100 text-green-800',
            default => 'bg-gray-100 text-gray-800',
        };
    }

    /**
     * Update balances based on leave requests
     */
    public function updateBalances(): void
    {
        $leaveRequests = LeaveRequest::where('user_id', $this->user_id)
            ->where('program_id', $this->program_id)
            ->where('status', 'approved')
            ->whereYear('start_date', $this->leave_year)
            ->get();

        // Reset taken values
        $this->sick_leave_taken = 0;
        $this->personal_leave_taken = 0;
        $this->emergency_leave_taken = 0;
        $this->other_leave_taken = 0;

        // Calculate taken leave by type
        foreach ($leaveRequests as $request) {
            $duration = $request->duration;

            switch ($request->leave_type) {
                case 'sick':
                    $this->sick_leave_taken += $duration;
                    break;
                case 'personal':
                    $this->personal_leave_taken += $duration;
                    break;
                case 'emergency':
                    $this->emergency_leave_taken += $duration;
                    break;
                case 'other':
                    $this->other_leave_taken += $duration;
                    break;
            }
        }

        // Calculate balances
        $this->sick_leave_balance = max(0, $this->sick_leave_entitled - $this->sick_leave_taken);
        $this->personal_leave_balance = max(0, $this->personal_leave_entitled - $this->personal_leave_taken);
        $this->emergency_leave_balance = max(0, $this->emergency_leave_entitled - $this->emergency_leave_taken);
        $this->other_leave_balance = max(0, $this->other_leave_entitled - $this->other_leave_taken);

        // Calculate totals
        $this->total_taken = $this->sick_leave_taken + $this->personal_leave_taken +
            $this->emergency_leave_taken + $this->other_leave_taken;
        $this->total_balance = $this->sick_leave_balance + $this->personal_leave_balance +
            $this->emergency_leave_balance + $this->other_leave_balance;

        $this->save();
    }
}
