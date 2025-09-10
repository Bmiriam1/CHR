<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProgramLearner extends Model
{
    protected $fillable = [
        'program_id',
        'user_id',
        'enrollment_date',
        'completion_date',
        'status',
        'eti_eligible',
        'eti_monthly_amount',
        'eti_months_claimed',
        'section_12h_eligible',
        'section_12h_contract_number',
        'section_12h_start_date',
        'section_12h_end_date',
        'section_12h_allowance',
        'attendance_percentage',
        'notes',
    ];

    protected $casts = [
        'enrollment_date' => 'date',
        'completion_date' => 'date',
        'eti_eligible' => 'boolean',
        'eti_monthly_amount' => 'decimal:2',
        'section_12h_eligible' => 'boolean',
        'section_12h_start_date' => 'date',
        'section_12h_end_date' => 'date',
        'section_12h_allowance' => 'decimal:2',
    ];

    /**
     * Get the program that the learner is enrolled in.
     */
    public function program(): BelongsTo
    {
        return $this->belongsTo(Program::class);
    }

    /**
     * Get the user (learner) enrolled in the program.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope for active enrollments.
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope for enrolled learners.
     */
    public function scopeEnrolled($query)
    {
        return $query->whereIn('status', ['enrolled', 'active']);
    }

    /**
     * Scope for completed programs.
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    /**
     * Check if the learner is currently active in the program.
     */
    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    /**
     * Check if the learner is enrolled in the program.
     */
    public function isEnrolled(): bool
    {
        return in_array($this->status, ['enrolled', 'active']);
    }

    /**
     * Check if the program is completed.
     */
    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    /**
     * Get the program duration in days.
     */
    public function getProgramDurationDays(): int
    {
        if ($this->completion_date && $this->enrollment_date) {
            return $this->enrollment_date->diffInDays($this->completion_date);
        }

        return $this->program->duration_weeks * 7;
    }

    /**
     * Get days remaining in the program.
     */
    public function getDaysRemaining(): int
    {
        if ($this->isCompleted()) {
            return 0;
        }

        $endDate = $this->completion_date ?? $this->program->end_date;
        return now()->diffInDays($endDate, false);
    }
}
