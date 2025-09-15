<?php

namespace App\Models;

use App\Traits\HasTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Program extends Model
{
    use HasFactory;

    protected static function booted()
    {
        static::addGlobalScope('tenant', function ($builder) {
            if (auth()->check() && auth()->user()->company_id) {
                try {
                    $userCompany = auth()->user()->company;
                    if ($userCompany && method_exists($userCompany, 'getCompanyGroup')) {
                        $allowedCompanyIds = $userCompany->getCompanyGroup()->pluck('id');
                        $builder->whereIn('programs.company_id', $allowedCompanyIds);
                    } else {
                        $builder->where('programs.company_id', auth()->user()->company_id);
                    }
                } catch (\Exception $e) {
                    $builder->where('programs.company_id', auth()->user()->company_id);
                }
            }
        });
    }

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        // Basic info
        'title',
        'thumbnail',
        'description',
        'start_date',
        'end_date',

        // Location & Category
        'location_type',
        'bbbee_category',

        // Requirements & Performance
        'specific_requirements',
        'learner_retention_rate',
        'completion_rate',
        'placement_rate',

        // Staff
        'coordinator_id',
        'program_type_id',
        'creator_id',

        // System
        'sort_order',
        'is_approved',
        'is_payment_received',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'is_approved' => 'boolean',
        'is_payment_received' => 'boolean',
    ];

    /**
     * Get the company that owns this program.
     */
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }


    /**
     * Get the host for this program.
     */
    public function host(): BelongsTo
    {
        return $this->belongsTo(Host::class);
    }

    /**
     * Get the branch this program belongs to.
     */
    public function branch(): BelongsTo
    {
        return $this->belongsTo(CompanyBranch::class, 'branch_id');
    }

    /**
     * Get the program type.
     */
    public function programType(): BelongsTo
    {
        return $this->belongsTo(ProgramType::class);
    }

    /**
     * Get the program coordinator.
     */
    public function coordinator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'coordinator_id');
    }

    /**
     * Get the user who created this program.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'creator_id');
    }

    /**
     * Get the user who approved this program.
     */
    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    /**
     * Get all learners enrolled in this program.
     */
    public function learners(): HasMany
    {
        return $this->hasMany(User::class)->where('is_learner', true);
    }

    /**
     * Get all users enrolled in this program (many-to-many relationship).
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'program_learners');
    }

    /**
     * Get all schedules for this program.
     */
    public function schedules(): HasMany
    {
        return $this->hasMany(Schedule::class);
    }

    /**
     * Get all SIM card allocations for this program.
     */
    public function simCardAllocations(): HasMany
    {
        return $this->hasMany(SimCardAllocation::class);
    }

    /**
     * Get active SIM card allocations for this program.
     */
    public function activeSimCardAllocations(): HasMany
    {
        return $this->hasMany(SimCardAllocation::class)->active();
    }

    /**
     * Get the participants for this program.
     */
    public function participants(): HasMany
    {
        return $this->hasMany(ProgramParticipant::class);
    }

    /**
     * Get the leave requests for this program.
     */
    public function leaveRequests(): HasMany
    {
        return $this->hasMany(LeaveRequest::class);
    }

    /**
     * Get the budgets for this program.
     */
    public function budgets(): HasMany
    {
        return $this->hasMany(ProgramBudget::class);
    }

    /**
     * Get the active budget for this program.
     */
    public function activeBudget(): HasMany
    {
        return $this->hasMany(ProgramBudget::class)->where('is_active', true);
    }

    /**
     * Check if the program is active.
     */
    public function isActive(): bool
    {
        return $this->status === 'active' && $this->is_approved;
    }

    /**
     * Check if the program is completed.
     */
    public function isCompleted(): bool
    {
        return $this->status === 'completed' || $this->end_date?->isPast();
    }

    /**
     * Check if enrollment is open.
     */
    public function isEnrollmentOpen(): bool
    {
        $now = now();

        return $this->isActive() &&
            ($this->enrollment_start_date === null || $now->gte($this->enrollment_start_date)) &&
            ($this->enrollment_end_date === null || $now->lte($this->enrollment_end_date)) &&
            $this->enrolled_count < $this->max_learners;
    }

    /**
     * Check if program is eligible for Section 12H allowances.
     */
    public function isSection12hEligible(): bool
    {
        return $this->section_12h_eligible &&
            $this->section_12h_contract_number &&
            $this->section_12h_start_date &&
            $this->section_12h_end_date;
    }

    /**
     * Check if program is ETI eligible.
     */
    public function isEtiEligible(): bool
    {
        return $this->eti_eligible_program && $this->company->hasEtiEnabled();
    }

    /**
     * Get total program value (daily rate × total training days).
     */
    public function getTotalValue(): float
    {
        if (!$this->total_training_days || !$this->daily_rate) {
            return 0;
        }

        return $this->daily_rate * $this->total_training_days;
    }

    /**
     * Get total allowances per day.
     */
    public function getDailyAllowances(): float
    {
        return $this->transport_allowance +
            $this->meal_allowance +
            $this->accommodation_allowance +
            $this->other_allowance;
    }

    /**
     * Get total daily payment (rate + allowances).
     */
    public function getTotalDailyPayment(): float
    {
        return $this->daily_rate + $this->getDailyAllowances();
    }

    /**
     * Get remaining enrollment spots.
     */
    public function getRemainingSpots(): int
    {
        return max(0, $this->max_learners - $this->enrolled_count);
    }

    /**
     * Get program duration in days.
     */
    public function getDurationInDays(): int
    {
        if ($this->total_training_days) {
            return $this->total_training_days;
        }

        if ($this->start_date && $this->end_date) {
            return $this->start_date->diffInDays($this->end_date) + 1;
        }

        return 0;
    }

    /**
     * Get program status badge color.
     */
    public function getStatusColor(): string
    {
        return match ($this->status) {
            'draft' => 'gray',
            'pending_approval' => 'yellow',
            'approved' => 'blue',
            'active' => 'green',
            'completed' => 'purple',
            'cancelled' => 'red',
            default => 'gray',
        };
    }

    /**
     * Scope to get active programs.
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active')->where('is_approved', true);
    }

    /**
     * Scope to get programs available for enrollment.
     */
    public function scopeAvailableForEnrollment($query)
    {
        $now = now();

        return $query->active()
            ->where(function ($q) use ($now) {
                $q->whereNull('enrollment_start_date')
                    ->orWhere('enrollment_start_date', '<=', $now);
            })
            ->where(function ($q) use ($now) {
                $q->whereNull('enrollment_end_date')
                    ->orWhere('enrollment_end_date', '>=', $now);
            })
            ->whereRaw('enrolled_count < max_learners');
    }

    /**
     * Scope to get programs by status.
     */
    public function scopeStatus($query, string $status)
    {
        return $query->where('status', $status);
    }
}
