<?php

namespace App\Models;

use App\Traits\HasTenant;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProgramBudget extends Model
{
    use HasFactory, HasTenant, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        // Relationships
        'program_id',
        'company_id',

        // Budget period
        'budget_start_date',
        'budget_end_date',
        'budget_name',
        'description',

        // Daily rates
        'travel_daily_rate',
        'online_daily_rate',
        'equipment_daily_rate',
        'onsite_daily_rate',

        // Allowances
        'travel_allowance',
        'meal_allowance',
        'accommodation_allowance',
        'equipment_allowance',

        // Budget totals
        'total_budget',
        'used_budget',
        'remaining_budget',

        // Settings
        'is_active',
        'auto_calculate_rates',
        'rate_calculation_rules',

        // Approval
        'status',
        'created_by',
        'approved_by',
        'approved_at',
        'approval_notes',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'budget_start_date' => 'date',
        'budget_end_date' => 'date',
        'travel_daily_rate' => 'decimal:2',
        'online_daily_rate' => 'decimal:2',
        'equipment_daily_rate' => 'decimal:2',
        'onsite_daily_rate' => 'decimal:2',
        'travel_allowance' => 'decimal:2',
        'meal_allowance' => 'decimal:2',
        'accommodation_allowance' => 'decimal:2',
        'equipment_allowance' => 'decimal:2',
        'total_budget' => 'decimal:2',
        'used_budget' => 'decimal:2',
        'remaining_budget' => 'decimal:2',
        'is_active' => 'boolean',
        'auto_calculate_rates' => 'boolean',
        'rate_calculation_rules' => 'array',
        'approved_at' => 'datetime',
    ];

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        // Auto-calculate remaining budget on save
        static::saving(function (ProgramBudget $budget) {
            $budget->remaining_budget = $budget->total_budget - $budget->used_budget;
        });
    }

    /**
     * Get the program this budget belongs to.
     */
    public function program(): BelongsTo
    {
        return $this->belongsTo(Program::class);
    }

    /**
     * Get the company this budget belongs to.
     */
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * Get the user who created this budget.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the user who approved this budget.
     */
    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    /**
     * Get attendance records for this budget period.
     */
    public function attendanceRecords(): HasMany
    {
        return $this->hasMany(AttendanceRecord::class, 'program_id', 'program_id')
            ->whereBetween('attendance_date', [$this->budget_start_date, $this->budget_end_date]);
    }

    /**
     * Check if the budget is currently active.
     */
    public function isActive(): bool
    {
        return $this->is_active &&
            $this->status === 'active' &&
            now()->between($this->budget_start_date, $this->budget_end_date);
    }

    /**
     * Check if the budget is approved.
     */
    public function isApproved(): bool
    {
        return $this->status === 'approved' || $this->status === 'active';
    }

    /**
     * Get the appropriate daily rate for an attendance type.
     */
    public function getDailyRateForType(string $attendanceType): float
    {
        return match (strtolower($attendanceType)) {
            'travel' => $this->travel_daily_rate,
            'online' => $this->online_daily_rate,
            'equipment' => $this->equipment_daily_rate,
            'onsite' => $this->onsite_daily_rate,
            default => $this->onsite_daily_rate,
        };
    }

    /**
     * Get the appropriate allowance for an attendance type.
     */
    public function getAllowanceForType(string $attendanceType): float
    {
        return match (strtolower($attendanceType)) {
            'travel' => $this->travel_allowance,
            'online' => 0, // No allowances for online
            'equipment' => $this->equipment_allowance,
            'onsite' => $this->meal_allowance,
            default => 0,
        };
    }

    /**
     * Calculate total daily compensation for an attendance type.
     */
    public function getTotalDailyCompensation(string $attendanceType): float
    {
        return $this->getDailyRateForType($attendanceType) + $this->getAllowanceForType($attendanceType);
    }

    /**
     * Update used budget based on attendance records.
     */
    public function updateUsedBudget(): void
    {
        $totalUsed = $this->attendanceRecords()
            ->where('is_payable', true)
            ->sum('calculated_pay');

        $this->used_budget = $totalUsed;
        $this->save();
    }

    /**
     * Check if budget has sufficient funds for additional attendance.
     */
    public function hasSufficientFunds(float $amount): bool
    {
        return $this->remaining_budget >= $amount;
    }

    /**
     * Get budget utilization percentage.
     */
    public function getUtilizationPercentage(): float
    {
        if ($this->total_budget <= 0) {
            return 0;
        }

        return ($this->used_budget / $this->total_budget) * 100;
    }

    /**
     * Get budget status color for UI.
     */
    public function getStatusColor(): string
    {
        return match ($this->status) {
            'draft' => 'gray',
            'pending_approval' => 'yellow',
            'approved' => 'green',
            'rejected' => 'red',
            'active' => 'blue',
            'completed' => 'purple',
            default => 'gray',
        };
    }

    /**
     * Approve the budget.
     */
    public function approve(User $approver, ?string $notes = null): void
    {
        $this->update([
            'status' => 'approved',
            'approved_by' => $approver->id,
            'approved_at' => now(),
            'approval_notes' => $notes,
        ]);
    }

    /**
     * Activate the budget.
     */
    public function activate(): void
    {
        $this->update([
            'status' => 'active',
            'is_active' => true,
        ]);
    }

    /**
     * Complete the budget.
     */
    public function complete(): void
    {
        $this->update([
            'status' => 'completed',
            'is_active' => false,
        ]);
    }

    /**
     * Get budget summary for reporting.
     */
    public function getBudgetSummary(): array
    {
        $attendanceRecords = $this->attendanceRecords()->where('is_payable', true)->get();

        return [
            'total_days' => $attendanceRecords->count(),
            'total_learners' => $attendanceRecords->pluck('user_id')->unique()->count(),
            'total_pay' => $attendanceRecords->sum('calculated_pay'),
            'average_daily_pay' => $attendanceRecords->avg('calculated_pay'),
            'utilization_percentage' => $this->getUtilizationPercentage(),
            'remaining_budget' => $this->remaining_budget,
            'days_remaining' => max(0, $this->budget_end_date->diffInDays(now())),
        ];
    }
}
